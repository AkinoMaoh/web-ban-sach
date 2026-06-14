<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['brand_id', 'publisher_id', 'name', 'description', 'price', 'stock', 'image', 'status'];
}
