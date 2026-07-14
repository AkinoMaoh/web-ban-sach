<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\products;
use App\Models\OrderDetail;
use App\Models\ReviewLike;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_detail_id',
        'user_name',
        'variant_name',
        'rating',
        'comment',
        'is_buyer'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(products::class); }
    public function orderDetail() { return $this->belongsTo(OrderDetail::class); }
    
    // Các hàm phục vụ like/dislike
    public function likes() { return $this->hasMany(ReviewLike::class); }
    
    public function isLikedByAuthUser() { 
        return $this->likes()->where('user_id', auth()->id())->exists(); 
    }
    
    public function likesCount() { 
        return $this->likes()->count(); 
    }

    public static function getProductRatingStats($productId)
    {
        $reviews = self::where('product_id', $productId)->get();
        $total = $reviews->count();
        $avg = $total > 0 ? round($reviews->avg('rating'), 1) : 0;

        $percentages = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $reviews->where('rating', $i)->count();
            $percentages[$i] = $total > 0 ? round(($count / $total) * 100) : 0;
        }

        return [
            'avg' => $avg,
            'total' => $total,
            'percentages' => $percentages
        ];
    }
}