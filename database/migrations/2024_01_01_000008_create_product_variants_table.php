<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('edition', 100)->default('Standard');
            $table->string('sku', 100)->unique()->nullable();

            $table->decimal('price', 15, 2);

            // Giá sau giảm
            $table->decimal('sale_price', 10, 2)->nullable();

            // % giảm (tự tính)
            $table->integer('discount_percent')->default(0);

            $table->integer('stock')->default(0);

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
