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

            // Sản phẩm
            $table->unsignedBigInteger('product_id');

            // Biến thể lấy từ bảng variants
            $table->unsignedBigInteger('variant_id');

            // Mã SKU
            $table->string('sku', 100)->unique()->nullable();

            // Giá của biến thể
            $table->decimal('price', 15, 2);

            // Giá sau giảm
            $table->decimal('sale_price', 15, 2)->nullable();

            // % giảm
            $table->integer('discount_percent')->default(0);

            // Tồn kho
            $table->integer('stock')->default(0);

            $table->timestamps();

            // Khóa ngoại sản phẩm
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // Khóa ngoại biến thể
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('cascade');

            // Một sản phẩm không được có cùng một biến thể 2 lần
            $table->unique(['product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
