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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Liên kết với bảng users
            
            $table->string('receiver_name'); // Tên người nhận hàng (có thể khác tên user)
            $table->string('receiver_phone', 20); // SĐT người nhận hàng
            
            // Các cột liên kết với GHN
            $table->integer('province_id');
            $table->integer('district_id');
            $table->string('ward_code');
            
            // Số nhà, tên đường, ngõ ngách...
            $table->string('specific_address'); 
            
            // Đánh dấu đây có phải là địa chỉ mặc định không
            $table->boolean('is_default')->default(false); 
            
            $table->timestamps();

            // Khai báo khóa ngoại
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('restrict');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('restrict');
            $table->foreign('ward_code')->references('code')->on('wards')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};