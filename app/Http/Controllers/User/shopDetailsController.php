<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class shopDetailsController extends Controller
{
    public function index($id)
    {
        // 1. Lấy dữ liệu sản phẩm cùng các quan hệ cần thiết
        $product = products::with(['variants', 'author', 'publishers', 'category', 'reviews.user'])->findOrFail($id);
        
        // 2. Lấy sách cùng tác giả (liên quan)
        $relatedProducts = products::where('author_id', $product->author_id)
                                  ->where('id', '!=', $id)
                                  ->take(6)
                                  ->get();

        // 3. Tính toán stats (đảm bảo hàm này đã có trong Model Review)
        $stats = Review::getProductRatingStats($id);
        $avgRating = $stats['avg'];
        $totalReviews = $stats['total'];
        $ratingPercentages = $stats['percentages'];

        // 4. Trả về view
        return view('User.shop-details', compact(
            'product',
            'relatedProducts',
            'avgRating',
            'totalReviews',
            'ratingPercentages'
        ));
    }
}