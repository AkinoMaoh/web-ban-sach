<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;

class shopDetailsController extends Controller
{
    public function index($id)
    {
        // 1. Lấy thông tin sản phẩm theo ID
        $product = products::findOrFail($id);

        // 2. Lấy danh mục, tác giả, nhà xuất bản để hiển thị lên thanh bộ lọc (Sidebar)
        $categories = categories::where('status', 1)->get();
        $authors = authors::all();
        $publishers = publishers::all();
        $relatedProducts = products::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->take(4)
            ->get();

        return view('User.shop-details', compact(
            'product',
            'categories',
            'authors',
            'publishers',
            'relatedProducts'
        ));
    }
}
