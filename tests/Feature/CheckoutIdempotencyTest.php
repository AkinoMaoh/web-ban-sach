<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutIdempotencyTest extends TestCase
{
    private CheckoutService $service;

    protected function setUp(): void
    {
        parent::setUp();

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
            $table->string('refund_status')->default('none');
            $table->timestamps();
        });

        $this->service = app(CheckoutService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('orders');
        parent::tearDown();
    }

    public function test_same_checkout_token_resumes_the_existing_order(): void
    {
        $token = (string) Str::uuid();
        $order = $this->order($token, 'reader@example.com');

        $result = $this->service->findExisting([
            'checkout_token' => $token,
            'billing_email' => 'reader@example.com',
        ], null);

        $this->assertNotNull($result);
        $this->assertFalse($result['created']);
        $this->assertSame($order->id, $result['order']->id);
        $this->assertSame(1, Order::query()->count());
    }

    public function test_guest_cannot_resume_another_email_checkout(): void
    {
        $token = (string) Str::uuid();
        $this->order($token, 'owner@example.com');

        $this->expectException(ValidationException::class);
        $this->service->findExisting([
            'checkout_token' => $token,
            'billing_email' => 'attacker@example.com',
        ], null);
    }

    private function order(string $token, string $email): Order
    {
        return Order::query()->create([
            'checkout_token' => $token,
            'billing_email' => $email,
            'total_amount' => 100000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'payment_status' => Order::PAYMENT_UNPAID,
            'refund_status' => Order::REFUND_NONE,
        ]);
    }
}
