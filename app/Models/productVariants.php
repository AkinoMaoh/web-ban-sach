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
        'stock'
    ];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
