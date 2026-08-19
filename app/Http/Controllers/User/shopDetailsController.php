<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class shopDetailsController extends Controller
{
    public function index($id)
    {
        // 1. Lấy sản phẩm cùng các quan hệ cần thiết
        $product = products::with([
            'images',
            'variants.variant',
            'author',
            'publishers',
            'category',
            'reviews.user'
        ])->findOrFail($id);

        // 2. Lấy sách cùng tác giả
        $relatedProducts = products::with('firstVariant')
            ->where('author_id', $product->author_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->take(6)
            ->get();
        
        // 3. Lấy sách liên quan cùng danh mục
        $relatedCategoryProducts = products::with('firstVariant')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->take(6)
            ->get();

        // 4. Thống kê đánh giá
        $stats = Review::getProductRatingStats($id);

        $avgRating = $stats['avg'];
        $totalReviews = $stats['total'];
        $ratingPercentages = $stats['percentages'];

        // 5. Lấy danh sách Voucher còn hiệu lực
        $vouchers = \App\Models\Voucher::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->get();

        // 6. LẤY DANH SÁCH ID SẢN PHẨM ĐÃ THÊM VÀO WISHLIST
        $wishlistIds = [];
        if (Auth::check()) {
            $wishlistIds = DB::table('wishlists')
                ->where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        // 7. Trả về view kèm wishlistIds
        return view('User.shop-details', compact(
            'product',
            'relatedProducts',
            'relatedCategoryProducts',
            'avgRating',
            'totalReviews',
            'ratingPercentages',
            'vouchers',
            'wishlistIds' // <--- Đã thêm biến này
        ));
    }
}
