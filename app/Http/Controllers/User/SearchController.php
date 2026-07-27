<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function searchProduct(Request $request)
    {
        $keyword = mb_strtolower(trim($request->keyword), 'UTF-8');

        // 1. Ô tìm kiếm trống -> Trả về danh mục và từ khóa hot
        if (empty($keyword)) {
            $categories = categories::where('status', 1)
                ->limit(4)
                ->get()
                ->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'image' => $cat->image ? asset('uploads/categories/' . $cat->image) : 'https://via.placeholder.com/60x80?text=No+Image'
                    ];
                });

            $hot_keywords = [
                'Conan',
                'Đắc Nhân Tâm',
                'Nhà Giả Kim',
                'Tiểu Thuyết'
            ];

            return response()->json([
                'status' => 'suggestions',
                'categories' => $categories,
                'hot_keywords' => $hot_keywords
            ]);
        }

        // 2. Có từ khóa -> Truy vấn AJAX tìm sản phẩm
        else {
            $products = products::with(['author', 'firstVariant'])
                ->where(DB::raw('LOWER(name)'), 'LIKE', '%' . $keyword . '%')
                ->where('status', 1)
                ->latest()
                ->limit(10)
                ->get();

            return response()->json([
                'status' => 'products',
                'data' => $products
            ]);
        }
    }
}
