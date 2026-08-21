<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'voucher_code',
        'discount_id',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'shipping_fee',
        'status',
        'billing_email',
        'shipping_name',      
        'shipping_phone',     
        'shipping_address',   
        'notes',              
        'payment_method',
        'payment_status',
        'paid_at',
        'payment_reference',
        'cancel_reason',    
    ];

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Đơn hàng thuộc về một người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Đơn hàng có nhiều chi tiết sản phẩm
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }

    public function voucherUsage(): HasOne
    {
        return $this->hasOne(VoucherUsage::class);
    }
}
