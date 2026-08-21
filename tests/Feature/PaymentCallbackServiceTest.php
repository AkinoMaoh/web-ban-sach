<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Services\InventoryReservationService;
use App\Services\PaymentCallbackService;
use App\Services\VoucherService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentCallbackServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
        parent::tearDown();
    }

    public function test_success_callback_is_idempotent(): void
    {
        $inventory = $this->mock(InventoryReservationService::class);
        $inventory->shouldNotReceive('release');
        $voucher = $this->mock(VoucherService::class);
        $voucher->shouldReceive('markUsedForOrder')->once()->andReturn(true);
        $service = app(PaymentCallbackService::class);
        [$order, $payment] = $this->pendingPayment();
        $verified = $this->verified($order, $payment, true);

        $first = $service->processVnpay($verified);
        $second = $service->processVnpay($verified);

        $this->assertTrue($first['first_success']);
        $this->assertFalse($second['first_success']);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_failed_callback_releases_order_resources(): void
    {
        $inventory = $this->mock(InventoryReservationService::class);
        $inventory->shouldReceive('release')->once()->andReturn(true);
        $voucher = $this->mock(VoucherService::class);
        $voucher->shouldReceive('releaseForOrder')->once()->andReturn(true);
        $service = app(PaymentCallbackService::class);
        [$order, $payment] = $this->pendingPayment();

        $result = $service->processVnpay($this->verified($order, $payment, false));

        $this->assertSame('failed', $result['state']);
        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(Payment::STATUS_FAILED, $payment->fresh()->status);
    }

    public function test_late_payment_after_expiry_requests_refund(): void
    {
        $inventory = $this->mock(InventoryReservationService::class);
        $inventory->shouldNotReceive('release');
        $voucher = $this->mock(VoucherService::class);
        $voucher->shouldNotReceive('markUsedForOrder');
        $service = app(PaymentCallbackService::class);
        [$order, $payment] = $this->pendingPayment();
        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'payment_status' => Order::PAYMENT_EXPIRED,
            'stock_released_at' => now(),
        ]);
        $payment->update(['status' => Payment::STATUS_EXPIRED]);

        $result = $service->processVnpay($this->verified($order, $payment, true));

        $this->assertSame('refund_required', $result['state']);
        $this->assertSame(Order::PAYMENT_PAID, $order->fresh()->payment_status);
        $this->assertSame(Order::REFUND_REQUESTED, $order->fresh()->refund_status);
    }

    /** @return array{Order, Payment} */
    private function pendingPayment(): array
    {
        $order = Order::query()->create([
            'billing_email' => 'reader@example.com',
            'total_amount' => 125000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'vnpay',
            'payment_status' => Order::PAYMENT_PENDING,
            'payment_expires_at' => now()->addMinutes(15),
            'refund_status' => Order::REFUND_NONE,
        ]);
        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'vnpay',
            'amount' => 125000,
            'status' => Payment::STATUS_PENDING,
            'transaction_ref' => 'TXN-'.$order->id,
            'currency' => 'VND',
            'attempt' => 1,
        ]);

        return [$order, $payment];
    }

    /** @return array{payment: Payment, order: Order, data: array<string, mixed>, successful: bool} */
    private function verified(Order $order, Payment $payment, bool $successful): array
    {
        return [
            'payment' => $payment,
            'order' => $order,
            'successful' => $successful,
            'data' => [
                'vnp_ResponseCode' => $successful ? '00' : '24',
                'vnp_TransactionStatus' => $successful ? '00' : '02',
                'vnp_TransactionNo' => $successful ? '99887766' : '0',
                'vnp_BankCode' => 'NCB',
                'vnp_CardType' => 'ATM',
            ],
        ];
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
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamp('cancel_requested_at')->nullable();
            $table->string('cancel_request_reason')->nullable();
            $table->string('cancel_reason')->nullable();
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
