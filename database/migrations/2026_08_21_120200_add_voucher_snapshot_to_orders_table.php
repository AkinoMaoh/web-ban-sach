<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('voucher_code', 100)->nullable()->after('voucher_id');
            $table->decimal('subtotal_amount', 15, 2)->default(0)->after('discount_id');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('subtotal_amount');
            $table->string('payment_status', 20)->default('unpaid')->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->string('payment_reference', 100)->nullable()->after('paid_at');

            $table->index('voucher_id');
            $table->index('payment_status');
        });

        // Các đơn cũ không có snapshot voucher: lấy tiền hàng từ tổng trừ phí giao hàng.
        DB::table('orders')->orderBy('id')->chunkById(200, function ($orders): void {
            foreach ($orders as $order) {
                $shippingFee = (float) ($order->shipping_fee ?? 0);

                DB::table('orders')->where('id', $order->id)->update([
                    'subtotal_amount' => max((float) $order->total_amount - $shippingFee, 0),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['voucher_id']);
            $table->dropIndex(['payment_status']);
            $table->dropColumn([
                'voucher_code',
                'subtotal_amount',
                'discount_amount',
                'payment_status',
                'paid_at',
                'payment_reference',
            ]);
        });
    }
};
