<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    public function category()
    {
        return $this->belongsTo(categories::class, 'category_id');
    }

    public function authors()
    {
        return $this->hasMany(products::class, 'author_id');
    }

    public function publishers()
    {
        return $this->belongsTo(publishers::class, 'publisher_id');
    }

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id ', 'author_id', 'publisher_id', 'name', 'description', 'price', 'stock', 'image', 'status'];
}
