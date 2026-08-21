<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReservation extends Model
{
    public const STATUS_RESERVED = 'reserved';

    public const STATUS_CONSUMED = 'consumed';

    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'status',
        'reserved_at',
        'consumed_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_at' => 'datetime',
            'consumed_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(productVariants::class, 'product_variant_id');
    }
}
