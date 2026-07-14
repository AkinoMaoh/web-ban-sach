<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\productVariants;
use App\Models\Review;
class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'price',
        'quantity',
        'subtotal',
    ];

    /**
     * Chi tiết thuộc về một đơn hàng
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Chi tiết liên kết tới một biến thể sản phẩm
     */
    public function productVariant()
    {
        return $this->belongsTo(productVariants::class, 'product_variant_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'order_detail_id');
    }

    // Quan hệ với biến thể (để hiển thị tên edition)
    public function variant()
    {
        return $this->belongsTo(productVariants::class, 'product_variant_id');
    }
}
