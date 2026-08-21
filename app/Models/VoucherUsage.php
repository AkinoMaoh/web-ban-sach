<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherUsage extends Model
{
    public const STATUS_RESERVED = 'reserved';

    public const STATUS_USED = 'used';

    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'voucher_id',
        'order_id',
        'user_id',
        'customer_key',
        'discount_amount',
        'status',
        'reserved_at',
        'used_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'reserved_at' => 'datetime',
            'used_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
