<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderLifecycleService
{
    public function __construct(
        private readonly InventoryReservationService $inventoryService,
        private readonly VoucherService $voucherService
    ) {
    }

    public function cancelUnpaid(Order|int $order, string $reason): Order
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId, $reason): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($orderId);

            if ($lockedOrder->status === Order::STATUS_CANCELLED) {
                return $lockedOrder;
            }

            if ($lockedOrder->isPaid()) {
                throw ValidationException::withMessages([
                    'order' => 'Đơn hàng đã thanh toán cần thực hiện quy trình hoàn tiền.',
                ]);
            }

            if (in_array($lockedOrder->status, [Order::STATUS_SHIPPING, Order::STATUS_COMPLETED], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Không thể hủy đơn hàng đang giao hoặc đã hoàn thành.',
                ]);
            }

            $lockedOrder->update([
                'status' => Order::STATUS_CANCELLED,
                'cancel_reason' => $reason,
                'payment_status' => $lockedOrder->payment_method === 'vnpay'
                    ? Order::PAYMENT_FAILED
                    : $lockedOrder->payment_status,
            ]);

            $this->inventoryService->release($lockedOrder);
            $this->voucherService->releaseForOrder($lockedOrder);

            return $lockedOrder->refresh();
        });
    }

    /** @return 'cancelled'|'refund_requested' */
    public function requestCancellation(Order|int $order, string $reason): string
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId, $reason): string {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($orderId);

            if ($lockedOrder->status === Order::STATUS_CANCELLED) {
                return 'cancelled';
            }

            if (! $lockedOrder->isPaid()) {
                $this->cancelUnpaid($lockedOrder, $reason);

                return 'cancelled';
            }

            if ($lockedOrder->status !== Order::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'order' => 'Chỉ có thể yêu cầu hủy đơn đã thanh toán khi đơn đang chờ xác nhận.',
                ]);
            }

            $lockedOrder->update([
                'cancel_requested_at' => now(),
                'cancel_request_reason' => $reason,
                'refund_status' => Order::REFUND_REQUESTED,
            ]);

            return 'refund_requested';
        });
    }

    public function expirePayment(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId): bool {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($orderId);

            if ($lockedOrder->payment_method !== 'vnpay'
                || $lockedOrder->payment_status !== Order::PAYMENT_PENDING
                || ! $lockedOrder->payment_expires_at
                || $lockedOrder->payment_expires_at->isFuture()) {
                return false;
            }

            Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->where('status', Payment::STATUS_PENDING)
                ->update([
                    'status' => Payment::STATUS_EXPIRED,
                    'expired_at' => now(),
                    'updated_at' => now(),
                ]);

            $lockedOrder->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => Order::PAYMENT_EXPIRED,
                'cancel_reason' => 'Phiên thanh toán VNPAY đã hết hạn.',
            ]);

            $this->inventoryService->release($lockedOrder);
            $this->voucherService->releaseForOrder($lockedOrder);

            return true;
        });
    }

    public function complete(Order|int $order): Order
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($orderId);
            $lockedOrder->update(['status' => Order::STATUS_COMPLETED]);
            $this->inventoryService->consume($lockedOrder);
            $this->voucherService->markUsedForOrder($lockedOrder);

            return $lockedOrder->refresh();
        });
    }

    public function markRefunded(Order|int $order, ?string $reference = null): Order
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return DB::transaction(function () use ($orderId, $reference): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($orderId);

            if (! $lockedOrder->isPaid()) {
                throw ValidationException::withMessages([
                    'refund' => 'Đơn hàng chưa ghi nhận thanh toán nên không thể hoàn tiền.',
                ]);
            }

            Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->where('status', Payment::STATUS_PAID)
                ->update([
                    'status' => Payment::STATUS_REFUNDED,
                    'refunded_at' => now(),
                    'updated_at' => now(),
                ]);

            $lockedOrder->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => Order::PAYMENT_REFUNDED,
                'refund_status' => Order::REFUND_COMPLETED,
                'payment_reference' => $reference ?: $lockedOrder->payment_reference,
                'cancel_reason' => $lockedOrder->cancel_request_reason ?: 'Đơn hàng đã được hoàn tiền.',
            ]);

            $this->inventoryService->release($lockedOrder);
            $this->voucherService->releaseForOrder($lockedOrder);

            return $lockedOrder->refresh();
        });
    }
}
