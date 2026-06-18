<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\productVariants;

class products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
        'author_id',
        'publisher_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(categories::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(authors::class, 'author_id');
    }

    public function publishers()
    {
        return $this->belongsTo(publishers::class, 'publisher_id');
    }

    // Thêm đoạn này
    public function variants()
    {
        return $this->hasMany(productVariants::class, 'product_id');
    }
}
