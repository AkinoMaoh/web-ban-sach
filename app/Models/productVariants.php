<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class productVariants extends Model
{
    protected $table = 'product_variants';
    protected $fillable = ['product_id', 'edition', 'is_signed', 'series_name', 'volume_number', 'sku', 'price', 'stock', 'created_at', 'updated_at'];
}
