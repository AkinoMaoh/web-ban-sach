<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class VoucherService
{
    /**
     * Kiểm tra voucher và trả về kết quả tính giảm giá, chưa giữ lượt sử dụng.
     *
     * @return array{voucher: Voucher, discount_amount: float, payable_subtotal: float, customer_key: string|null}
     */
    public function quote(
        string $code,
        float $subtotal,
        ?int $userId = null,
        ?string $email = null
    ): array {
        $normalizedCode = $this->normalizeCode($code);

        $voucher = Voucher::query()
            ->where('code', $normalizedCode)
            ->first();

        if (! $voucher) {
            $this->fail('Mã giảm giá không tồn tại hoặc đã được lưu trữ.');
        }

        return $this->quoteVoucher($voucher, $subtotal, $userId, $email);
    }

    /**
     * @return array{voucher: Voucher, discount_amount: float, payable_subtotal: float, customer_key: string|null}
     */
    public function quoteVoucher(
        Voucher $voucher,
        float $subtotal,
        ?int $userId = null,
        ?string $email = null
    ): array {
        $subtotal = round(max($subtotal, 0), 2);
        $now = now();

        if (! $voucher->is_active) {
            $this->fail('Mã giảm giá đang tạm khóa.');
        }

        if ($voucher->start_date && $now->isBefore($voucher->start_date->copy()->startOfDay())) {
            $this->fail('Mã giảm giá chưa đến thời gian sử dụng.');
        }

        if ($voucher->end_date && $now->isAfter($voucher->end_date->copy()->endOfDay())) {
            $this->fail('Mã giảm giá đã hết hạn.');
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            $this->fail('Mã giảm giá đã hết lượt sử dụng.');
        }

        if ($subtotal < (float) $voucher->min_order_value) {
            $missingAmount = (float) $voucher->min_order_value - $subtotal;

            $this->fail(
                'Bạn cần mua thêm '.number_format($missingAmount, 0, ',', '.').'đ để dùng mã này.'
            );
        }

        $customerKey = $this->customerKey($userId, $email);

        if ($voucher->usage_limit_per_customer !== null) {
            if ($customerKey === null) {
                $this->fail('Vui lòng nhập email trước khi áp dụng mã giảm giá.', 'billing_email');
            }

            if ($this->customerUsageCount($voucher, $customerKey) >= $voucher->usage_limit_per_customer) {
                $this->fail('Bạn đã sử dụng hết số lượt được phép của mã này.');
            }
        }

        $discountAmount = $voucher->discountAmountFor($subtotal);

        return [
            'voucher' => $voucher,
            'discount_amount' => $discountAmount,
            'payable_subtotal' => round(max($subtotal - $discountAmount, 0), 2),
            'customer_key' => $customerKey,
        ];
    }

    public function reserve(
        Voucher $voucher,
        Order $order,
        string $customerKey,
        float $discountAmount
    ): VoucherUsage {
        $existingUsage = VoucherUsage::query()->where('order_id', $order->id)->first();

        if ($existingUsage) {
            return $existingUsage;
        }

        if ($voucher->usage_limit_per_customer !== null
            && $this->customerUsageCount($voucher, $customerKey) >= $voucher->usage_limit_per_customer) {
            $this->fail('Bạn đã sử dụng hết số lượt được phép của mã này.');
        }

        $today = now()->toDateString();
        $affectedRows = Voucher::query()
            ->whereKey($voucher->id)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where(function ($query): void {
                $query
                    ->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->increment('used_count');

        if ($affectedRows !== 1) {
            $this->fail('Mã giảm giá vừa hết lượt hoặc không còn hiệu lực. Vui lòng chọn mã khác.');
        }

        try {
            return VoucherUsage::query()->create([
                'voucher_id' => $voucher->id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'customer_key' => $customerKey,
                'discount_amount' => round($discountAmount, 2),
                'status' => VoucherUsage::STATUS_RESERVED,
                'reserved_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Voucher::withTrashed()
                ->whereKey($voucher->id)
                ->where('used_count', '>', 0)
                ->decrement('used_count');

            $existingUsage = VoucherUsage::query()->where('order_id', $order->id)->first();

            if ($existingUsage) {
                return $existingUsage;
            }

            throw $exception;
        }
    }

    public function markUsedForOrder(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return VoucherUsage::query()
            ->where('order_id', $orderId)
            ->where('status', VoucherUsage::STATUS_RESERVED)
            ->update([
                'status' => VoucherUsage::STATUS_USED,
                'used_at' => now(),
                'updated_at' => now(),
            ]) === 1;
    }

    public function releaseForOrder(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;
        $usage = VoucherUsage::query()
            ->where('order_id', $orderId)
            ->whereIn('status', [VoucherUsage::STATUS_RESERVED, VoucherUsage::STATUS_USED])
            ->first();

        if (! $usage) {
            return false;
        }

        $released = VoucherUsage::query()
            ->whereKey($usage->id)
            ->whereIn('status', [VoucherUsage::STATUS_RESERVED, VoucherUsage::STATUS_USED])
            ->update([
                'status' => VoucherUsage::STATUS_RELEASED,
                'released_at' => now(),
                'updated_at' => now(),
            ]);

        if ($released !== 1) {
            return false;
        }

        Voucher::withTrashed()
            ->whereKey($usage->voucher_id)
            ->where('used_count', '>', 0)
            ->decrement('used_count');

        return true;
    }

    public function customerUsageCount(Voucher $voucher, string $customerKey): int
    {
        return VoucherUsage::query()
            ->where('voucher_id', $voucher->id)
            ->where('customer_key', $customerKey)
            ->whereIn('status', [VoucherUsage::STATUS_RESERVED, VoucherUsage::STATUS_USED])
            ->count();
    }

    public function customerKey(?int $userId, ?string $email): ?string
    {
        if ($userId !== null) {
            return 'user:'.$userId;
        }

        $normalizedEmail = Str::lower(trim((string) $email));

        if ($normalizedEmail === '') {
            return null;
        }

        return 'email:'.hash('sha256', $normalizedEmail);
    }

    public function normalizeCode(string $code): string
    {
        return Str::upper(trim($code));
    }

    private function fail(string $message, string $field = 'voucher_code'): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
