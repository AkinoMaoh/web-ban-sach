<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use Illuminate\Support\Facades\DB;

class trangChuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh mục, tác giả, nhà xuất bản để hiển thị lên thanh bộ lọc (Sidebar)
        $categories = categories::where('status', 1)->get();
        $authors = authors::all();
        $publishers = publishers::all();
        $product5 = products::where('status', 1)
            ->orderByDesc('id') // Sắp xếp theo ID giảm dần (ID lớn nhất lên đầu)
            ->take(5)           // Lấy 5 sản phẩm
            ->get();
        $topSanPham = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.price',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.image',
                'products.price'
            )
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 2. Khởi tạo Query lấy sản phẩm đang hoạt động
        $query = products::where('status', 1);

        // Kiểm tra điều kiện ràng buộc tác giả (Nếu bạn có setup quan hệ 'author' trong model products)
        if (method_exists(products::class, 'author')) {
            $query->whereHas('author', function ($q) {
                $q->where('status', 1);
            });
        }

        // 3. XỬ LÝ TÌM KIẾM THEO TỪ KHÓA (Gộp từ hàm search cũ qua để đồng bộ với View)
        if ($request->filled('keyword')) {
            $query->where('name', 'like', $request->keyword . '%');
        }

        // 4. XỬ LÝ LỌC THEO TÁC GIẢ 
        if ($request->filled('author')) {
            $query->where('author_id', $request->author);
        }

        // 5. XỬ LÝ LỌC THEO NHÀ XUẤT BẢN
        if ($request->filled('publisher')) {
            $query->where('publisher_id', $request->publisher);
        }

        // 6. XỬ LÝ LỌC THEO KHOẢNG GIÁ
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // 7. Thực thi lấy dữ liệu kèm phân trang (12 sản phẩm/trang) và giữ tham số trên URL
        $products = $query->paginate(15)->appends($request->query());

        $bannerBooks = products::where('status', 1)
            ->latest()
            ->take(5)
            ->get();

        return view('User.index', compact(
            'products',
            'categories',
            'authors',
            'publishers',
            'bannerBooks',
            'product5',
            'topSanPham'
        ));
    }

    // Tìm kiếm hiển thị JS bên dưới ------------------------------------------
    public function searchProduct(Request $request)
    {
        $keyword = $request->keyword;

        $products = products::where('name', 'like', $keyword . '%')
            ->where('status', 1)
            ->limit(5)
            ->get();

        return response()->json($products);
    }
    // ------------------------------------------------------------------------
}
