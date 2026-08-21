<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Services\InventoryReservationService;
use App\Services\OrderLifecycleService;
use App\Services\VoucherService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderLifecycleServiceTest extends TestCase
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

    public function test_repeated_refund_request_keeps_the_existing_request(): void
    {
        $inventory = $this->mock(InventoryReservationService::class);
        $inventory->shouldNotReceive('release');
        $voucher = $this->mock(VoucherService::class);
        $voucher->shouldNotReceive('releaseForOrder');
        $service = app(OrderLifecycleService::class);
        $order = $this->paidOrder(Order::REFUND_REQUESTED);
        $order->update([
            'cancel_requested_at' => now()->subMinute(),
            'cancel_request_reason' => 'Yeu cau dau tien',
        ]);

        $result = $service->requestCancellation($order, 'Yeu cau gui lai');

        $this->assertSame('refund_requested', $result);
        $this->assertSame('Yeu cau dau tien', $order->fresh()->cancel_request_reason);
    }

    public function test_admin_cannot_confirm_a_refund_without_a_request(): void
    {
        $this->mock(InventoryReservationService::class)->shouldNotReceive('release');
        $this->mock(VoucherService::class)->shouldNotReceive('releaseForOrder');
        $service = app(OrderLifecycleService::class);
        $order = $this->paidOrder(Order::REFUND_NONE);

        $this->expectException(ValidationException::class);
        $service->markRefunded($order, 'REF-INVALID');
    }

    public function test_admin_can_finish_a_requested_refund_once(): void
    {
        $this->mock(InventoryReservationService::class)
            ->shouldReceive('release')
            ->once()
            ->andReturn(true);
        $this->mock(VoucherService::class)
            ->shouldReceive('releaseForOrder')
            ->once()
            ->andReturn(true);
        $service = app(OrderLifecycleService::class);
        $order = $this->paidOrder(Order::REFUND_REQUESTED);
        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'payment_method' => 'vnpay',
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PAID,
        ]);

        $refunded = $service->markRefunded($order, 'REF-123');

        $this->assertSame(Order::STATUS_CANCELLED, $refunded->status);
        $this->assertSame(Order::PAYMENT_REFUNDED, $refunded->payment_status);
        $this->assertSame(Order::REFUND_COMPLETED, $refunded->refund_status);
        $this->assertSame(Payment::STATUS_REFUNDED, $payment->fresh()->status);
    }

    private function paidOrder(string $refundStatus): Order
    {
        return Order::query()->create([
            'billing_email' => 'reader@example.com',
            'total_amount' => 125000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'vnpay',
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => now(),
            'refund_status' => $refundStatus,
        ]);
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
            $table->string('payment_status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
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
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }
}
