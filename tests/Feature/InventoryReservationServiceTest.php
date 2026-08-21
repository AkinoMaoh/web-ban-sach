<?php

namespace Tests\Feature;

use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\productVariants;
use App\Services\InventoryReservationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryReservationServiceTest extends TestCase
{
    private InventoryReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
        $this->service = app(InventoryReservationService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('inventory_reservations');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('orders');
        parent::tearDown();
    }

    public function test_reserve_and_release_are_idempotent(): void
    {
        $variant = $this->variant(5);
        $order = $this->order();

        $this->service->reserve($order, [[
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]]);

        $this->assertSame(3, $variant->fresh()->stock);
        $this->assertSame(1, InventoryReservation::query()->count());
        $this->assertTrue($this->service->release($order));
        $this->assertSame(5, $variant->fresh()->stock);
        $this->assertFalse($this->service->release($order));
        $this->assertSame(5, $variant->fresh()->stock);
    }

    public function test_failed_reservation_rolls_back_every_item(): void
    {
        $available = $this->variant(5);
        $insufficient = $this->variant(1);
        $order = $this->order();

        try {
            $this->service->reserve($order, [
                ['product_variant_id' => $available->id, 'quantity' => 2],
                ['product_variant_id' => $insufficient->id, 'quantity' => 2],
            ]);
            $this->fail('Reservation should reject insufficient stock.');
        } catch (ValidationException) {
            $this->assertSame(5, $available->fresh()->stock);
            $this->assertSame(1, $insufficient->fresh()->stock);
            $this->assertSame(0, InventoryReservation::query()->count());
        }
    }

    public function test_consumed_stock_is_not_returned(): void
    {
        $variant = $this->variant(4);
        $order = $this->order();
        $this->service->reserve($order, [[
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]]);

        $this->assertTrue($this->service->consume($order));
        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertFalse($this->service->release($order));
        $this->assertSame(1, $variant->fresh()->stock);
    }

    private function variant(int $stock): productVariants
    {
        return productVariants::query()->create([
            'product_id' => 1,
            'variant_id' => 1,
            'sku' => uniqid('SKU'),
            'price' => 100000,
            'stock' => $stock,
            'weight_grams' => 500,
        ]);
    }

    private function order(): Order
    {
        return Order::query()->create([
            'billing_email' => 'reader@example.com',
            'total_amount' => 100000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'payment_status' => Order::PAYMENT_UNPAID,
            'refund_status' => Order::REFUND_NONE,
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
            $table->string('refund_status')->default('none');
            $table->timestamp('stock_reserved_at')->nullable();
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight_grams')->default(500);
            $table->timestamps();
        });

        Schema::create('inventory_reservations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedInteger('quantity');
            $table->string('status')->default('reserved');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'product_variant_id']);
        });
    }
}
