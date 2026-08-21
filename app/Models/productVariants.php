<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class productVariants extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'variant_id',
        'sku',
        'price',
        'sale_price',
        'discount_percent',
        'stock',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'weight' => 'integer',
        ];
    }

    // product_variants thuộc về một sản phẩm
    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }

    // product_variants thuộc về một loại biến thể
    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    public function inventoryReservations(): HasMany
    {
        return $this->hasMany(InventoryReservation::class, 'product_variant_id');
    }

    // Tự tính % giảm
    public function getDiscountPercentAttribute()
    {
        if (
            is_null($this->sale_price) ||
            $this->sale_price <= 0 ||
            $this->sale_price >= $this->price
        ) {
            return 0;
        }

        return round(
            (($this->price - $this->sale_price) / $this->price) * 100
        );
    }
}
