<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_key', 191);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('reserved');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            // Không khai báo foreign key để tương thích database MyISAM hiện tại.
            $table->index('voucher_id');
            $table->index('user_id');
            $table->index(
                ['voucher_id', 'customer_key', 'status'],
                'voucher_customer_usage_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
    }
};
