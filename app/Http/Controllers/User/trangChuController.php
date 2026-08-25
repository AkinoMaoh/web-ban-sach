<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class trangChuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Danh mục đang hoạt động
        $categories = categories::where('status', 1)->get();

        // 2. Top 5 sản phẩm mới nhất
        $product5 = products::with('firstVariant')
            ->where('status', 1)
            ->latest('id')
            ->take(5)
            ->get();

        // 3. Top 5 sản phẩm bán chạy nhất (Bảo toàn thứ tự từ SQL)
        $topIds = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->where('orders.status', 'completed')
            ->select('product_variants.product_id', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('product_variants.product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->pluck('product_id')
            ->toArray();

        $topSanPham = collect();
        if (!empty($topIds)) {
            $idsString = implode(',', $topIds);
            $topSanPham = products::with('firstVariant')
                ->whereIn('id', $topIds)
                ->orderByRaw("FIELD(id, {$idsString})")
                ->get();
        }

        // 4. Danh sách sản phẩm hiển thị trên trang chủ
        $products = products::with('firstVariant')
            ->where('status', 1)
            ->whereHas('author', function ($q) {
                $q->where('status', 1);
            })
            ->inRandomOrder()
            ->take(15)
            ->get();

        // 5. Banner trang chủ đang hoạt động
        $now = now();
        $banners = Banner::where('status', 1)
            ->where('position', 'home')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('sort_order')
            ->get();

        return view('User.index', compact(
            'products',
            'categories',
            'product5',
            'topSanPham',
            'banners'
        ));
    }
}
