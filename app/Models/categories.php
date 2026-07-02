<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(products::class, 'category_id');
    }
}
