<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Services\VnpayService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VnpayServiceTest extends TestCase
{
    private VnpayService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
        config([
            'services.vnpay.payment_url' => 'https://sandbox.vnpay.test/pay',
            'services.vnpay.tmn_code' => 'TESTCODE',
            'services.vnpay.hash_secret' => 'test-secret',
            'services.vnpay.version' => '2.1.0',
            'services.vnpay.command' => 'pay',
            'services.vnpay.order_type' => 'billpayment',
            'services.vnpay.locale' => 'vn',
            'services.vnpay.currency' => 'VND',
            'services.vnpay.expiry_minutes' => 15,
        ]);
        $this->service = app(VnpayService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
        parent::tearDown();
    }

    public function test_payment_url_uses_separate_transaction_reference(): void
    {
        $order = $this->order();
        $payment = $this->service->createAttempt($order);
        $url = $this->service->paymentUrl($payment, '127.0.0.1', 'https://book.test/vnpay-return');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame($payment->transaction_ref, $query['vnp_TxnRef']);
        $this->assertNotSame((string) $order->id, $query['vnp_TxnRef']);
        $this->assertSame('12500000', (string) $query['vnp_Amount']);
        $this->assertArrayHasKey('vnp_ExpireDate', $query);
        $this->assertArrayHasKey('vnp_SecureHash', $query);
    }

    public function test_callback_verifies_signature_amount_and_merchant(): void
    {
        $payment = $this->service->createAttempt($this->order());
        $data = $this->callbackData($payment);

        $verified = $this->service->verifyCallback($data);

        $this->assertTrue($verified['successful']);
        $this->assertSame($payment->id, $verified['payment']->id);
    }

    public function test_callback_rejects_a_signed_wrong_amount(): void
    {
        $payment = $this->service->createAttempt($this->order());
        $data = $this->callbackData($payment, 100);

        $this->expectException(ValidationException::class);
        $this->service->verifyCallback($data);
    }

    private function order(): Order
    {
        return Order::query()->create([
            'billing_email' => 'reader@example.com',
            'total_amount' => 125000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'vnpay',
            'payment_status' => Order::PAYMENT_PENDING,
            'payment_expires_at' => now()->addMinutes(15),
            'refund_status' => Order::REFUND_NONE,
        ]);
    }

    /** @return array<string, mixed> */
    private function callbackData(Payment $payment, ?int $amount = null): array
    {
        $data = [
            'vnp_TmnCode' => 'TESTCODE',
            'vnp_TxnRef' => $payment->transaction_ref,
            'vnp_Amount' => $amount ?? (int) round((float) $payment->amount * 100),
            'vnp_ResponseCode' => '00',
            'vnp_TransactionStatus' => '00',
            'vnp_TransactionNo' => '99887766',
            'vnp_BankCode' => 'NCB',
        ];
        $data['vnp_SecureHash'] = $this->service->sign($data);

        return $data;
    }

    private function createTables(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->nullable()->unique();
            $table->uuid('checkout_token')->nullable()->unique();
            $table->string('tracking_token', 64)->nullable()->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('billing_email')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('status');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_expires_at')->nullable();
            $table->string('refund_status')->default('none');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->string('transaction_ref')->nullable()->unique();
            $table->string('gateway_transaction_no')->nullable()->unique();
            $table->string('response_code')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('card_type')->nullable();
            $table->string('currency')->default('VND');
            $table->unsignedSmallInteger('attempt')->default(1);
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }
}
