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
    $keyword = trim($request->keyword ?? '');

    // Khi chưa nhập từ khóa
    if ($keyword === '') {

        $categories = categories::where('status', 1)
            ->limit(4)
            ->get()
            ->map(function ($category) {

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image
                        ? asset('uploads/categories/' . $category->image)
                        : null,
                ];

            });

        $hotKeywords = [
            'Conan',
            'Đắc Nhân Tâm',
            'Nhà Giả Kim',
            'Tiểu Thuyết'
        ];

        return response()->json([
            'status' => 'suggestions',
            'categories' => $categories,
            'hot_keywords' => $hotKeywords
        ]);
    }

    // Đã nhập từ khóa
    $products = products::where('name', 'like', '%' . $keyword . '%')
    ->where('status', 1)
    ->orderBy('id', 'desc')
    ->limit(8)
    ->get();

    return response()->json([
        'status' => 'products',
        'data' => $products
    ]);
}
}
