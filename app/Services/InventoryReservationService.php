<?php

namespace App\Services;

use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\productVariants;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryReservationService
{
    /**
     * @param array<int, array{product_variant_id: int, quantity: int}> $items
     */
    public function reserve(Order $order, array $items): void
    {
        DB::transaction(function () use ($order, $items): void {
            if (InventoryReservation::query()->where('order_id', $order->id)->exists()) {
                return;
            }

            foreach ($items as $item) {
                $variant = productVariants::query()
                    ->whereKey((int) $item['product_variant_id'])
                    ->lockForUpdate()
                    ->first();

                $quantity = max((int) $item['quantity'], 1);

                if (! $variant || (int) $variant->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng.',
                    ]);
                }

                $updated = productVariants::query()
                    ->whereKey($variant->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($updated !== 1) {
                    throw ValidationException::withMessages([
                        'cart' => 'Tồn kho vừa thay đổi. Vui lòng kiểm tra lại giỏ hàng.',
                    ]);
                }

                InventoryReservation::query()->create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'status' => InventoryReservation::STATUS_RESERVED,
                    'reserved_at' => now(),
                ]);
            }

            $order->forceFill([
                'stock_reserved_at' => now(),
                'stock_released_at' => null,
            ])->save();
        });
    }

    public function release(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId): bool {
            $reservations = InventoryReservation::query()
                ->where('order_id', $orderId)
                ->where('status', InventoryReservation::STATUS_RESERVED)
                ->lockForUpdate()
                ->get();

            if ($reservations->isEmpty()) {
                return false;
            }

            foreach ($reservations as $reservation) {
                productVariants::query()
                    ->whereKey($reservation->product_variant_id)
                    ->increment('stock', $reservation->quantity);

                $reservation->update([
                    'status' => InventoryReservation::STATUS_RELEASED,
                    'released_at' => now(),
                ]);
            }

            Order::query()->whereKey($orderId)->update([
                'stock_released_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });
    }

    public function consume(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return InventoryReservation::query()
            ->where('order_id', $orderId)
            ->where('status', InventoryReservation::STATUS_RESERVED)
            ->update([
                'status' => InventoryReservation::STATUS_CONSUMED,
                'consumed_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }
}
