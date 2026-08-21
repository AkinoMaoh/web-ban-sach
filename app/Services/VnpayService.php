<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VnpayService
{
    public function isConfigured(): bool
    {
        return (string) config('services.vnpay.tmn_code') !== ''
            && (string) config('services.vnpay.hash_secret') !== ''
            && (string) config('services.vnpay.payment_url') !== '';
    }

    public function createAttempt(Order $order): Payment
    {
        $attempt = (int) Payment::query()->where('order_id', $order->id)->max('attempt') + 1;
        $transactionRef = Str::upper(
            substr($order->order_number.'-'.$attempt.'-'.Str::random(8), 0, 64)
        );

        return Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'vnpay',
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PENDING,
            'transaction_ref' => $transactionRef,
            'currency' => 'VND',
            'attempt' => $attempt,
        ]);
    }

    public function latestPendingAttempt(Order $order): ?Payment
    {
        return Payment::query()
            ->where('order_id', $order->id)
            ->where('status', Payment::STATUS_PENDING)
            ->latest('id')
            ->first();
    }

    public function paymentUrl(Payment $payment, string $clientIp, string $returnUrl): string
    {
        if (! $this->isConfigured()) {
            $this->fail('VNPAY chưa được cấu hình trên hệ thống.');
        }

        $payment->loadMissing('order');
        $order = $payment->order;
        $createdAt = $payment->created_at ?? now();
        $expiresAt = $order->payment_expires_at
            ?? $createdAt->copy()->addMinutes($this->expiryMinutes());

        $inputData = [
            'vnp_Version' => (string) config('services.vnpay.version', '2.1.0'),
            'vnp_TmnCode' => (string) config('services.vnpay.tmn_code'),
            'vnp_Amount' => (int) round((float) $payment->amount * 100),
            'vnp_Command' => (string) config('services.vnpay.command', 'pay'),
            'vnp_CreateDate' => $createdAt->format('YmdHis'),
            'vnp_CurrCode' => (string) config('services.vnpay.currency', 'VND'),
            'vnp_ExpireDate' => $expiresAt->format('YmdHis'),
            'vnp_IpAddr' => $clientIp,
            'vnp_Locale' => (string) config('services.vnpay.locale', 'vn'),
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->order_number,
            'vnp_OrderType' => (string) config('services.vnpay.order_type', 'billpayment'),
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $payment->transaction_ref,
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC1738);

        return rtrim((string) config('services.vnpay.payment_url'), '?')
            .'?'.$query
            .'&vnp_SecureHash='.$this->sign($inputData);
    }

    /**
     * @param array<string, mixed> $requestData
     * @return array{payment: Payment, order: Order, data: array<string, mixed>, successful: bool}
     */
    public function verifyCallback(array $requestData): array
    {
        if (! $this->isConfigured()) {
            $this->fail('VNPAY chưa được cấu hình trên hệ thống.');
        }

        $inputData = collect($requestData)
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'vnp_'))
            ->all();

        $receivedHash = (string) ($inputData['vnp_SecureHash'] ?? '');
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        if ($receivedHash === '' || ! hash_equals($this->sign($inputData), $receivedHash)) {
            $this->fail('Không thể xác minh chữ ký phản hồi từ VNPAY.');
        }

        if ((string) ($inputData['vnp_TmnCode'] ?? '') !== (string) config('services.vnpay.tmn_code')) {
            $this->fail('Mã website VNPAY không khớp.');
        }

        $transactionRef = (string) ($inputData['vnp_TxnRef'] ?? '');
        $payment = Payment::query()
            ->with('order')
            ->where('transaction_ref', $transactionRef)
            ->first();

        if (! $payment || ! $payment->order || $payment->payment_method !== 'vnpay') {
            $this->fail('Không tìm thấy giao dịch VNPAY phù hợp.');
        }

        $receivedAmount = (int) ($inputData['vnp_Amount'] ?? 0);
        $expectedAmount = (int) round((float) $payment->amount * 100);

        if ($receivedAmount !== $expectedAmount) {
            $this->fail('Số tiền VNPAY trả về không khớp với đơn hàng.');
        }

        $responseCode = (string) ($inputData['vnp_ResponseCode'] ?? '');
        $transactionStatus = (string) ($inputData['vnp_TransactionStatus'] ?? $responseCode);

        return [
            'payment' => $payment,
            'order' => $payment->order,
            'data' => $inputData,
            'successful' => $responseCode === '00' && $transactionStatus === '00',
        ];
    }

    public function expiryMinutes(): int
    {
        return max((int) config('services.vnpay.expiry_minutes', 15), 5);
    }

    /** @param array<string, mixed> $inputData */
    public function sign(array $inputData): string
    {
        ksort($inputData);

        return hash_hmac(
            'sha512',
            http_build_query($inputData, '', '&', PHP_QUERY_RFC1738),
            (string) config('services.vnpay.hash_secret')
        );
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['payment' => $message]);
    }
}
