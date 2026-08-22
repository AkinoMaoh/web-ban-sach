<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class ShippingService
{
    /**
     * @param array<int, array{product_variant_id: int, quantity: int, price?: float}> $items
     * @return array{fee: float, weight: int, provider: string, service: string, quote_token: string}
     */
    public function quote(int $provinceId, int $districtId, string $wardCode, array $items): array
    {
        $weight = $this->calculateWeight($items);
        $fee = $this->requestFee($districtId, $wardCode, $weight);
        $payload = [
            'province_id' => $provinceId,
            'district_id' => $districtId,
            'ward_code' => $wardCode,
            'fee' => $fee,
            'weight' => $weight,
            'provider' => 'ghn',
            'service' => 'standard',
            'cart_fingerprint' => $this->cartFingerprint($items),
            'issued_at' => now()->timestamp,
        ];

        return [
            'fee' => $fee,
            'weight' => $weight,
            'provider' => $payload['provider'],
            'service' => $payload['service'],
            'quote_token' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
        ];
    }

    /**
     * @param array<int, array{product_variant_id: int, quantity: int, price?: float}> $items
     * @return array{fee: float, weight: int, provider: string, service: string}
     */
    public function verifyQuote(
        string $token,
        int $provinceId,
        int $districtId,
        string $wardCode,
        array $items
    ): array {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $this->invalidQuote('Phí vận chuyển không hợp lệ. Vui lòng chọn lại địa chỉ.');
        }

        $ttlSeconds = max((int) config('services.ghn.quote_ttl_minutes', 15), 1) * 60;
        $issuedAt = (int) ($payload['issued_at'] ?? 0);

        if ($issuedAt <= 0
            || $issuedAt > now()->timestamp + 60
            || now()->timestamp - $issuedAt > $ttlSeconds) {
            $this->invalidQuote('Phí vận chuyển đã hết hiệu lực. Vui lòng tính lại phí.');
        }

        $matchesAddress = (int) ($payload['province_id'] ?? 0) === $provinceId
            && (int) ($payload['district_id'] ?? 0) === $districtId
            && (string) ($payload['ward_code'] ?? '') === $wardCode;

        if (! $matchesAddress || ($payload['cart_fingerprint'] ?? '') !== $this->cartFingerprint($items)) {
            $this->invalidQuote('Địa chỉ hoặc giỏ hàng đã thay đổi. Vui lòng tính lại phí vận chuyển.');
        }

        return [
            'fee' => round(max((float) ($payload['fee'] ?? 0), 0), 2),
            'weight' => max((int) ($payload['weight'] ?? 0), 1),
            'provider' => (string) ($payload['provider'] ?? 'ghn'),
            'service' => (string) ($payload['service'] ?? 'standard'),
        ];
    }

    /** @param array<int, array{product_variant_id: int, quantity: int}> $items */
    public function calculateWeight(array $items): int
    {
        $variantIds = collect($items)
            ->pluck('product_variant_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $weights = DB::table('product_variants')
            ->whereIn('id', $variantIds)
            ->pluck('weight_grams', 'id');

        $defaultWeight = max((int) config('services.ghn.default_item_weight', 500), 1);
        $totalWeight = collect($items)->sum(function (array $item) use ($weights, $defaultWeight): int {
            $weight = max((int) ($weights[(int) $item['product_variant_id']] ?? $defaultWeight), 1);
            $quantity = max((int) $item['quantity'], 1);

            return $weight * $quantity;
        });

        return min(max((int) $totalWeight, 1), 50000);
    }

    /** @param array<int, array{product_variant_id: int, quantity: int, price?: float}> $items */
    public function cartFingerprint(array $items): string
    {
        $normalized = collect($items)
            ->map(fn (array $item) => [
                'product_variant_id' => (int) $item['product_variant_id'],
                'quantity' => max((int) $item['quantity'], 1),
                'price' => round((float) ($item['price'] ?? 0), 2),
            ])
            ->sortBy('product_variant_id')
            ->values()
            ->all();

        return hash('sha256', json_encode($normalized, JSON_THROW_ON_ERROR));
    }

    private function requestFee(int $districtId, string $wardCode, int $weight): float
    {
        $fallbackFee = max((float) config('services.ghn.default_fee', 30000), 0);
        $token = (string) config('services.ghn.token');
        $shopId = (string) config('services.ghn.shop_id');
        $storeDistrictId = (int) config('services.ghn.store_district_id');

        if ($token === '' || $shopId === '' || $storeDistrictId <= 0) {
            return $fallbackFee;
        }

        try {
            $http = Http::timeout(8)->retry(2, 200);

            if (app()->environment('local')) {
                $http = $http->withoutVerifying();
            }

            $response = $http
                ->withHeaders([
                    'Token' => $token,
                    'ShopId' => $shopId,
                ])
                ->post(rtrim((string) config('services.ghn.base_url'), '/').'/shipping-order/fee', [
                    'from_district_id' => $storeDistrictId,
                    'to_district_id' => $districtId,
                    'to_ward_code' => $wardCode,
                    'weight' => $weight,
                    'service_type_id' => (int) config('services.ghn.service_type_id', 2),
                ]);

            if ($response->successful() && is_numeric($response->json('data.total'))) {
                return round(max((float) $response->json('data.total'), 0), 2);
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return $fallbackFee;
    }

    private function invalidQuote(string $message): never
    {
        throw ValidationException::withMessages(['shipping_quote_token' => $message]);
    }
}
