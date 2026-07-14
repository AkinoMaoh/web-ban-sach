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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_detail_id'); // Liên kết tới chi tiết đơn hàng
            $table->string('user_name');      // Lưu tên người dùng tại thời điểm đánh giá
            $table->string('variant_name');   // Lưu tên phiên bản tại thời điểm đánh giá
            $table->tinyInteger('rating');    // Số sao
            $table->text('comment');          // Nội dung nhận xét
            $table->boolean('is_buyer')->default(false); // Tích xanh đã mua
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};