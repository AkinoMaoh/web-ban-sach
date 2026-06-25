<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
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
    // public function productVariant()
    // {
    //     return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    // }
}