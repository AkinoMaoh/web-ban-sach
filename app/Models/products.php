<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\productVariants;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use App\Models\Review;

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

    public function variants()
    {
        return $this->hasMany(productVariants::class, 'product_id');
    }

    function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->orderBy('created_at', 'desc');
    }
    public function firstVariant()
    {
        return $this->hasOne(productVariants::class, 'product_id')
            ->oldest('id');
    }
}
