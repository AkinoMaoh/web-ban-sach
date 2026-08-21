<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    private VoucherService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTables();
        $this->service = app(VoucherService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('vouchers');

        parent::tearDown();
    }

    public function test_quote_rejects_an_expired_voucher(): void
    {
        $voucher = $this->voucher([
            'code' => 'EXPIRED',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
        ]);

        try {
            $this->service->quote($voucher->code, 300000, 10, null);
            $this->fail('Expired voucher should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame('Mã giảm giá đã hết hạn.', $exception->errors()['voucher_code'][0]);
        }
    }

    public function test_quote_uses_server_subtotal_and_percent_cap(): void
    {
        $voucher = $this->voucher([
            'type' => 'percent',
            'discount_value' => 20,
            'max_discount_value' => 50000,
        ]);

        $quote = $this->service->quote($voucher->code, 400000, 10, null);

        $this->assertSame(50000.0, $quote['discount_amount']);
        $this->assertSame(350000.0, $quote['payable_subtotal']);
    }

    public function test_releasing_order_returns_global_and_customer_usage(): void
    {
        $voucher = $this->voucher([
            'usage_limit' => 1,
            'usage_limit_per_customer' => 1,
        ]);
        $order = Order::create([
            'billing_email' => 'reader@example.com',
            'voucher_id' => $voucher->id,
            'voucher_code' => $voucher->code,
            'subtotal_amount' => 300000,
            'discount_amount' => 50000,
            'shipping_fee' => 30000,
            'total_amount' => 280000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);
        $customerKey = $this->service->customerKey(null, $order->billing_email);

        $this->service->reserve($voucher, $order, $customerKey, 50000);

        $this->assertSame(1, $voucher->fresh()->used_count);
        $this->assertTrue($this->service->releaseForOrder($order));
        $this->assertSame(0, $voucher->fresh()->used_count);

        $quote = $this->service->quote($voucher->code, 300000, null, $order->billing_email);
        $this->assertSame(50000.0, $quote['discount_amount']);
    }

    private function voucher(array $attributes = []): Voucher
    {
        return Voucher::create(array_merge([
            'code' => 'BOOK50',
            'name' => 'Voucher test',
            'type' => 'fixed',
            'discount_value' => 50000,
            'max_discount_value' => null,
            'min_order_value' => 100000,
            'usage_limit' => 10,
            'usage_limit_per_customer' => null,
            'used_count' => 0,
            'is_active' => true,
            'is_public' => true,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ], $attributes));
    }

    private function createTables(): void
    {
        Schema::create('vouchers', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('fixed');
            $table->decimal('discount_value', 15, 2);
            $table->decimal('max_discount_value', 15, 2)->nullable();
            $table->decimal('min_order_value', 15, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_number')->nullable()->unique();
            $table->uuid('checkout_token')->nullable()->unique();
            $table->string('tracking_token', 64)->nullable()->unique();
            $table->string('billing_email')->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->string('voucher_code')->nullable();
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending');
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('voucher_usages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_key');
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->string('status')->default('reserved');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }
}
