<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class productVariants extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'edition',
        'sku',
        'price',
        'sale_price',
        'discount_percent',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
    public function getDiscountPercentAttribute()
    {
        if (
            is_null($this->sale_price) ||
            $this->sale_price <= 0 ||
            $this->sale_price >= $this->price
        ) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
