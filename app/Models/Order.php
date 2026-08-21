<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\OrderDetail;
use App\Models\User;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_SHIPPING = 'shipping';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_EXPIRED = 'expired';

    public const PAYMENT_REFUNDED = 'refunded';

    public const REFUND_NONE = 'none';

    public const REFUND_REQUESTED = 'requested';

    public const REFUND_PENDING = 'pending';

    public const REFUND_COMPLETED = 'completed';

    protected $table = 'orders';
    protected $primaryKey = 'id';

    // Bổ sung các trường giao hàng vào đây
    protected $fillable = [
        'user_id',
        'order_number',
        'checkout_token',
        'tracking_token',
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
        'province_id',
        'district_id',
        'ward_code',
        'specific_address',
        'shipping_provider',
        'shipping_service',
        'shipping_weight',
        'notes',              
        'payment_method',
        'payment_status',
        'paid_at',
        'payment_reference',
        'payment_expires_at',
        'stock_reserved_at',
        'stock_released_at',
        'cancel_requested_at',
        'cancel_request_reason',
        'refund_status',
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
            'payment_expires_at' => 'datetime',
            'stock_reserved_at' => 'datetime',
            'stock_released_at' => 'datetime',
            'cancel_requested_at' => 'datetime',
            'shipping_weight' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->order_number ??= 'DH'.now()->format('ymd').Str::upper(Str::random(8));
            $order->tracking_token ??= hash('sha256', Str::uuid()->toString().Str::random(40));
        });
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function inventoryReservations(): HasMany
    {
        return $this->hasMany(InventoryReservation::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function requiresRefund(): bool
    {
        return $this->isPaid() && $this->refund_status !== self::REFUND_COMPLETED;
    }

    public function canBeCancelledImmediately(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isPaid();
    }
}
