<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentCallbackService
{
    public function __construct(
        private readonly InventoryReservationService $inventoryService,
        private readonly VoucherService $voucherService
    ) {
    }

    /**
     * @param array{payment: Payment, order: Order, data: array<string, mixed>, successful: bool} $verified
     * @return array{state: string, order: Order, payment: Payment, first_success: bool}
     */
    public function processVnpay(array $verified): array
    {
        return DB::transaction(function () use ($verified): array {
            $payment = Payment::query()->lockForUpdate()->findOrFail($verified['payment']->id);
            $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);
            $data = $verified['data'];

            if ($verified['successful']) {
                return $this->recordSuccess($payment, $order, $data);
            }

            return $this->recordFailure($payment, $order, $data);
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array{state: string, order: Order, payment: Payment, first_success: bool}
     */
    private function recordSuccess(Payment $payment, Order $order, array $data): array
    {
        $firstSuccess = $payment->status !== Payment::STATUS_PAID;

        $payment->update([
            'status' => Payment::STATUS_PAID,
            'gateway_transaction_no' => $data['vnp_TransactionNo'] ?? $payment->gateway_transaction_no,
            'response_code' => $data['vnp_ResponseCode'] ?? null,
            'bank_code' => $data['vnp_BankCode'] ?? null,
            'card_type' => $data['vnp_CardType'] ?? null,
            'gateway_payload' => $data,
            'paid_at' => $payment->paid_at ?? now(),
            'failed_at' => null,
            'expired_at' => null,
        ]);

        $wasAlreadyPaid = $order->payment_status === Order::PAYMENT_PAID;
        $needsRefund = $order->status === Order::STATUS_CANCELLED
            || $order->stock_released_at !== null
            || ($wasAlreadyPaid && $firstSuccess);

        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => $order->paid_at ?? now(),
            'payment_reference' => $data['vnp_TransactionNo'] ?? $order->payment_reference,
            'payment_expires_at' => null,
            'refund_status' => $needsRefund ? Order::REFUND_REQUESTED : $order->refund_status,
            'cancel_requested_at' => $needsRefund ? now() : $order->cancel_requested_at,
            'cancel_request_reason' => $needsRefund
                ? 'VNPAY ghi nhận tiền sau khi đơn đã hủy hoặc đã thanh toán trước đó.'
                : $order->cancel_request_reason,
        ]);

        if (! $needsRefund) {
            $this->voucherService->markUsedForOrder($order);
        }

        return [
            'state' => $needsRefund ? 'refund_required' : 'paid',
            'order' => $order->refresh(),
            'payment' => $payment->refresh(),
            'first_success' => $firstSuccess,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{state: string, order: Order, payment: Payment, first_success: bool}
     */
    private function recordFailure(Payment $payment, Order $order, array $data): array
    {
        if ($payment->status === Payment::STATUS_PAID || $order->isPaid()) {
            return [
                'state' => 'paid',
                'order' => $order,
                'payment' => $payment,
                'first_success' => false,
            ];
        }

        $payment->update([
            'status' => Payment::STATUS_FAILED,
            'gateway_transaction_no' => $data['vnp_TransactionNo'] ?? $payment->gateway_transaction_no,
            'response_code' => $data['vnp_ResponseCode'] ?? null,
            'bank_code' => $data['vnp_BankCode'] ?? null,
            'card_type' => $data['vnp_CardType'] ?? null,
            'gateway_payload' => $data,
            'failed_at' => now(),
        ]);

        $hasAnotherAttempt = Payment::query()
            ->where('order_id', $order->id)
            ->where('id', '!=', $payment->id)
            ->where('status', Payment::STATUS_PENDING)
            ->exists();

        if (! $hasAnotherAttempt && $order->status !== Order::STATUS_CANCELLED) {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => Order::PAYMENT_FAILED,
                'cancel_reason' => 'Thanh toán VNPAY không thành công.',
            ]);

            $this->inventoryService->release($order);
            $this->voucherService->releaseForOrder($order);
        }

        return [
            'state' => $hasAnotherAttempt ? 'pending' : 'failed',
            'order' => $order->refresh(),
            'payment' => $payment->refresh(),
            'first_success' => false,
        ];
    }
}
