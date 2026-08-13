<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'name',
        'status',
    ];

    public function productVariants()
    {
        return $this->hasMany(productVariants::class, 'variant_id');
    }
}
