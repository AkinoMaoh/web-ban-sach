<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Loại giảm giá: trừ tiền thẳng (fixed) hoặc trừ theo % (percent)
            $table->enum('type', ['fixed', 'percent'])->default('fixed')->after('code'); 
            
            // Đổi tên cột discount_value (nếu cần) hoặc giữ nguyên. Ở đây giả sử giữ nguyên.
            
            // Giới hạn giảm tối đa (dành cho loại percent, VD: giảm 10% nhưng max 50k)
            $table->decimal('max_discount_value', 15, 2)->nullable()->after('discount_value'); 
            
            // Điều kiện áp dụng: Đơn hàng tối thiểu (VD: đơn từ 200k mới được dùng)
            $table->decimal('min_order_value', 15, 2)->default(0)->after('max_discount_value'); 
            
            // Tổng số lượt được phép sử dụng của mã này
            $table->integer('usage_limit')->nullable()->after('min_order_value'); 
            
            // Số lượt đã có người sử dụng
            $table->integer('used_count')->default(0)->after('usage_limit'); 
            
            // Trạng thái bật/tắt mã giảm giá
            $table->boolean('is_active')->default(true)->after('used_count'); 
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'type', 
                'max_discount_value', 
                'min_order_value', 
                'usage_limit', 
                'used_count', 
                'is_active'
            ]);
        });
    }
};
