<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
 {
     Schema::table('orders', function (Blueprint $table) {
         // Thêm các cột giao hàng, cho phép null để phòng hờ
         $table->string('shipping_name')->nullable()->after('status');
         $table->string('shipping_phone')->nullable()->after('shipping_name');
         $table->string('shipping_address')->nullable()->after('shipping_phone');
         $table->text('notes')->nullable()->after('shipping_address');
         $table->string('payment_method')->nullable()->after('notes');
     });
 }

 public function down(): void
 {
     Schema::table('orders', function (Blueprint $table) {
         $table->dropColumn(['shipping_name', 'shipping_phone', 'shipping_address', 'notes', 'payment_method']);
     });
 }
};
