<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('order_number', 32)->nullable()->unique();
            $table->uuid('checkout_token')->nullable()->unique();
            $table->string('tracking_token', 64)->nullable()->unique();

            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('ward_code', 32)->nullable();
            $table->string('specific_address', 500)->nullable();

            $table->string('shipping_provider', 30)->default('ghn');
            $table->string('shipping_service', 50)->nullable();
            $table->unsignedInteger('shipping_weight')->default(0);

            $table->timestamp('payment_expires_at')->nullable();
            $table->timestamp('stock_reserved_at')->nullable();
            $table->timestamp('stock_released_at')->nullable();
            $table->timestamp('cancel_requested_at')->nullable();
            $table->string('cancel_request_reason', 500)->nullable();
            $table->string('refund_status', 20)->default('none');

            $table->index(['payment_status', 'payment_expires_at'], 'orders_payment_expiry_index');
            $table->index(['status', 'refund_status'], 'orders_refund_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex('orders_payment_expiry_index');
            $table->dropIndex('orders_refund_status_index');
            $table->dropUnique(['order_number']);
            $table->dropUnique(['checkout_token']);
            $table->dropUnique(['tracking_token']);
            $table->dropColumn([
                'order_number',
                'checkout_token',
                'tracking_token',
                'province_id',
                'district_id',
                'ward_code',
                'specific_address',
                'shipping_provider',
                'shipping_service',
                'shipping_weight',
                'payment_expires_at',
                'stock_reserved_at',
                'stock_released_at',
                'cancel_requested_at',
                'cancel_request_reason',
                'refund_status',
            ]);
        });
    }
};
