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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Tiêu đề banner
            $table->string('title');

            // Mô tả
            $table->text('description')->nullable();

            // Ảnh banner
            $table->string('image');

            // Link chuyển hướng
            $table->string('link')->nullable();

            // Vị trí hiển thị
            $table->enum('position', [
                'home',
                'category',
            ])->default('home');

            // Thứ tự hiển thị
            $table->integer('sort_order')->default(0);

            // Trạng thái
            $table->boolean('status')->default(true);

            // Thời gian hiển thị
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
