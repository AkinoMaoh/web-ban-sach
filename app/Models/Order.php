<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
use App\Models\User;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';

    // Bổ sung các trường giao hàng vào đây
    protected $fillable = [
        'user_id',
        'voucher_id',
        'discount_id',
        'total_amount',
        'status',
        'shipping_name',      
        'shipping_phone',     
        'shipping_address',   
        'notes',              
        'payment_method',     
    ];

    /**
     * Đơn hàng thuộc về một người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Đơn hàng có nhiều chi tiết sản phẩm
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}