<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết với bảng users và product_variants
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');

            // Số sao (ví dụ dùng unsignedTinyInteger vì điểm chỉ từ 1-5, rất nhỏ)
            $table->unsignedTinyInteger('rating');



            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
