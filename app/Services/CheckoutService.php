<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\productVariants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutService
{
    public function __construct(
        private readonly VoucherService $voucherService,
        private readonly ShippingService $shippingService,
        private readonly CheckoutAddressService $addressService,
        private readonly InventoryReservationService $inventoryService,
        private readonly VnpayService $vnpayService
    ) {
    }

    /**
     * @param array<string, mixed> $validated
     * @param array{order_items: array<int, array{product_variant_id: int, price: float, quantity: int}>, subtotal: float} $snapshot
     * @return array{order: Order, payment: Payment|null, created: bool}
     */
    public function create(array $validated, array $snapshot, ?int $userId): array
    {
        $existing = Order::query()->where('checkout_token', $validated['checkout_token'])->first();

        if ($existing) {
            return $this->existingResult($existing, $validated, $userId);
        }

        $voucherQuote = null;

        if (! empty($validated['applied_voucher'])) {
            $voucherQuote = $this->voucherService->quote(
                $validated['applied_voucher'],
                $snapshot['subtotal'],
                $userId,
                $validated['billing_email']
            );
        }

        $shippingQuote = $this->shippingService->verifyQuote(
            $validated['shipping_quote_token'],
            (int) $validated['province_id'],
            (int) $validated['district_id'],
            (string) $validated['ward_code'],
            $snapshot['order_items']
        );
        $address = $this->addressService->resolve($validated);
        $discountAmount = (float) ($voucherQuote['discount_amount'] ?? 0);
        $totalAmount = round(
            max((float) $snapshot['subtotal'] - $discountAmount, 0) + $shippingQuote['fee'],
            2
        );

        try {
            $result = DB::transaction(function () use (
                $validated,
                $snapshot,
                $userId,
                $voucherQuote,
                $shippingQuote,
                $address,
                $discountAmount,
                $totalAmount
            ): array {
                $existing = Order::query()
                    ->where('checkout_token', $validated['checkout_token'])
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $this->existingResult($existing, $validated, $userId);
                }

                $isVnpay = $validated['payment_method'] === 'vnpay';
                $order = Order::query()->create([
                    'user_id' => $userId,
                    'checkout_token' => $validated['checkout_token'],
                    'billing_email' => $validated['billing_email'],
                    'voucher_id' => $voucherQuote['voucher']->id ?? null,
                    'voucher_code' => $voucherQuote['voucher']->code ?? null,
                    'subtotal_amount' => $snapshot['subtotal'],
                    'discount_amount' => $discountAmount,
                    'shipping_fee' => $shippingQuote['fee'],
                    'total_amount' => $totalAmount,
                    'status' => Order::STATUS_PENDING,
                    'shipping_name' => $validated['shipping_name'],
                    'shipping_phone' => $validated['shipping_phone'],
                    'shipping_address' => $address['full_address'],
                    'province_id' => $validated['province_id'],
                    'district_id' => $validated['district_id'],
                    'ward_code' => $validated['ward_code'],
                    'specific_address' => $validated['specific_address'],
                    'shipping_provider' => $shippingQuote['provider'],
                    'shipping_service' => $shippingQuote['service'],
                    'shipping_weight' => $shippingQuote['weight'],
                    'notes' => $validated['order_notes'] ?? null,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $isVnpay ? Order::PAYMENT_PENDING : Order::PAYMENT_UNPAID,
                    'payment_expires_at' => $isVnpay
                        ? now()->addMinutes($this->vnpayService->expiryMinutes())
                        : null,
                    'refund_status' => Order::REFUND_NONE,
                ]);

                $this->createOrderDetails($order, $snapshot['order_items']);
                $this->inventoryService->reserve($order, $snapshot['order_items']);

                if ($voucherQuote !== null) {
                    $customerKey = $voucherQuote['customer_key']
                        ?? $this->voucherService->customerKey($userId, $validated['billing_email']);

                    $this->voucherService->reserve(
                        $voucherQuote['voucher'],
                        $order,
                        (string) $customerKey,
                        $discountAmount
                    );
                }

                $payment = $isVnpay ? $this->vnpayService->createAttempt($order) : null;

                return ['order' => $order, 'payment' => $payment, 'created' => true];
            }, 3);
        } catch (Throwable $exception) {
            $existing = Order::query()->where('checkout_token', $validated['checkout_token'])->first();

            if (! $existing) {
                throw $exception;
            }

            $result = $this->existingResult($existing, $validated, $userId);
        }

        if ($result['created']) {
            $this->addressService->saveForUser($userId, $validated);
        }

        return $result;
    }

    /**
     * @param array<int, array{product_variant_id: int, price: float, quantity: int}> $items
     */
    private function createOrderDetails(Order $order, array $items): void
    {
        $variants = productVariants::query()
            ->with(['product', 'variant'])
            ->whereIn('id', collect($items)->pluck('product_variant_id'))
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $variant = $variants->get((int) $item['product_variant_id']);

            if (! $variant || ! $variant->product) {
                throw ValidationException::withMessages([
                    'cart' => 'Một sản phẩm không còn được bán. Vui lòng kiểm tra lại giỏ hàng.',
                ]);
            }

            OrderDetail::query()->create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_name' => $variant->variant?->name ?? 'Mặc định',
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => round($item['price'] * $item['quantity'], 2),
                'image' => $variant->product->image,
            ]);
        }
    }

    /**
     * @param array<string, mixed> $validated
     * @return array{order: Order, payment: Payment|null, created: bool}
     */
    private function existingResult(Order $order, array $validated, ?int $userId): array
    {
        $sameOwner = $userId !== null
            ? (int) $order->user_id === $userId
            : $order->user_id === null
                && Str::lower((string) $order->billing_email) === Str::lower($validated['billing_email']);

        if (! $sameOwner) {
            throw ValidationException::withMessages([
                'checkout_token' => 'Phiên thanh toán không thuộc về bạn. Vui lòng tải lại trang.',
            ]);
        }

        return [
            'order' => $order,
            'payment' => $order->payment_method === 'vnpay'
                ? $this->vnpayService->latestPendingAttempt($order)
                : null,
            'created' => false,
        ];
    }
}
