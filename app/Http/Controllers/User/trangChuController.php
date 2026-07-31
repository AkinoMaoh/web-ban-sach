<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;
use Carbon\Carbon;



class trangChuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh mục, tác giả, nhà xuất bản để hiển thị lên thanh bộ lọc (Sidebar)
        $categories = categories::where('status', 1)->get();
        $authors = authors::all();
        $publishers = publishers::all();
        $product5 = products::with('firstVariant')
            ->where('status', 1)
            ->orderByDesc('id')
            ->take(5)
            ->get();
        $topIds = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->where('orders.status', 'completed')
            ->select(
                'product_variants.product_id',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy('product_variants.product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->pluck('product_id');

        $topSanPham = Products::with('firstVariant')
            ->whereIn('id', $topIds)
            ->get();

        // 2. Khởi tạo Query lấy sản phẩm đang hoạt động
        $query = products::with('firstVariant')
            ->where('status', 1);

        // Kiểm tra điều kiện ràng buộc tác giả (Nếu bạn có setup quan hệ 'author' trong model products)
        if (method_exists(products::class, 'author')) {
            $query->whereHas('author', function ($q) {
                $q->where('status', 1);
            });
        }

        // 7. Thực thi lấy dữ liệu kèm phân trang (15 sản phẩm/trang) và giữ tham số trên URL
        $products = $query->inRandomOrder()->take(15)->get();
        $banners = Banner::where('status', 1)
            ->where('position', 'home')
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();

        return view('User.index', compact(
            'products',
            'categories',
            'authors',
            'publishers',
            'product5',
            'topSanPham',
            'banners'
        ));
    }

}
