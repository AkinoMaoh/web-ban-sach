<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers; // Đã use đúng Class model publishers

class trangChuController extends Controller
{
    public function index(Request $request) // Nhận dữ liệu dữ liệu lọc từ View gửi lên
    {
        // 1. Lấy danh mục, tác giả, nhà xuất bản để hiển thị lên thanh bộ lọc (Sidebar)
        $categories = categories::where('status', 1)->get();
        $authors = authors::all();    // Đã sửa thành all() để tránh lỗi Unknown column 'status'
        $publishers = publishers::all(); // Đã sửa thành all() để tránh lỗi Unknown column 'status'

        // 2. Khởi tạo Query lọc sản phẩm (Giữ nguyên các điều kiện ban đầu của bạn)
        $query = products::where('status', 1)
            ->whereHas('author', function ($q) {
                $q->where('status', 1);
            });

        // 3. XỬ LÝ LỌC THEO TÁC GIẢ (Nếu người dùng chọn)
        if ($request->filled('author')) {
            $query->where('author_id', $request->author); // Đảm bảo cột khóa ngoại trong bảng products của bạn là author_id
        }

        // 4. XỬ LÝ LỌC THEO NHÀ XUẤT BẢN (Nếu người dùng chọn)
        if ($request->filled('publisher')) {
            $query->where('publisher_id', $request->publisher); // Đảm bảo cột khóa ngoại trong bảng products của bạn là publisher_id
        }

        // 5. XỬ LÝ LỌC THEO KHOẢNG GIÁ
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // 6. Thực thi lấy dữ liệu kèm phân trang (ví dụ 12 sản phẩm/trang) và giữ lại các tham số lọc trên URL khi chuyển trang
        $products = $query->paginate(12)->appends($request->query());

        return view('User.index', compact(
            'products',
            'categories',
            'authors',
            'publishers'
        ));
    }

    // Tìm kiếm sản phẩm theo từ khóa
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $categories = categories::where('status', 1)->get();

        $products = products::where('name', 'like', '%' . $keyword . '%')
            ->get();

        return view('User.index', compact(
            'products',
            'categories',
            'keyword'
        ));
    }

    // Xem chi tiết sản phẩm
    public function productDetails($id)
    {
        $product = products::with('variants')->findOrFail($id);

        $categories = categories::where('status', 1)->get();

        return view('User.shop-details', compact(
            'product',
            'categories'
        ));
    }
}