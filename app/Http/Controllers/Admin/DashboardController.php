<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Comment;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * HÀM CHÍNH GỌI GIAO DIỆN DASHBOARD
     */
    public function index()
    {
        $hienTai = Carbon::now();

        // Lần lượt gọi các hàm xử lý bên dưới (Code cực sạch và dễ hiểu)
        $taiChinh   = $this->getThongKeTaiChinh($hienTai);
        $donHang    = $this->getThongKeDonHang();
        $sanPham    = $this->getThongKeSanPham();
        $khachHang  = $this->getThongKeKhachHang($hienTai);
        $bieuDo     = $this->getDuLieuBieuDo($hienTai);

        // Gộp tất cả các mảng dữ liệu lại thành 1 mảng lớn để truyền sang View
        $duLieuTruyenSangView = array_merge($taiChinh, $donHang, $sanPham, $khachHang, $bieuDo);

        return view('admin.dashboard', $duLieuTruyenSangView);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. NHÓM TÀI CHÍNH (KPIs)
    |--------------------------------------------------------------------------
    */
    private function getThongKeTaiChinh($hienTai)
    {
        $doanhThuHomNay = Order::where('status', 'completed')
            ->whereDate('created_at', $hienTai->today())
            ->sum('total_amount');

        $doanhThuThangNay = Order::where('status', 'completed')
            ->whereMonth('created_at', $hienTai->month)
            ->whereYear('created_at', $hienTai->year)
            ->sum('total_amount');

        // Lấy tháng trước để so sánh tăng trưởng
        $thangTruoc = $hienTai->copy()->subMonth();
        $doanhThuThangTruoc = Order::where('status', 'completed')
            ->whereMonth('created_at', $thangTruoc->month)
            ->whereYear('created_at', $thangTruoc->year)
            ->sum('total_amount');
        
        $tangTruong = 0;
        if ($doanhThuThangTruoc > 0) {
            $tangTruong = round((($doanhThuThangNay - $doanhThuThangTruoc) / $doanhThuThangTruoc) * 100);
        } elseif ($doanhThuThangNay > 0) {
            $tangTruong = 100; // Nếu tháng trước 0 đồng mà tháng này có tiền -> tăng 100%
        }

        return compact('doanhThuHomNay', 'doanhThuThangNay', 'tangTruong');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. NHÓM ĐƠN HÀNG (VẬN HÀNH)
    |--------------------------------------------------------------------------
    */
    private function getThongKeDonHang()
    {
        return [
            'donMoi'       => Order::where('status', 'pending')->count(),
            'donDangGiao'  => Order::where('status', 'shipping')->count(),
            'donThanhCong' => Order::where('status', 'completed')->count(),
            'donDaHuy'     => Order::where('status', 'cancelled')->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 3. NHÓM SẢN PHẨM & KHO HÀNG
    |--------------------------------------------------------------------------
    */
    private function getThongKeSanPham()
    {
        // Query Top 5 sản phẩm bán chạy nhất
        $topSanPham = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Sản phẩm sắp hết hàng (kho < 5)
        // Sản phẩm sắp hết hàng (kho < 5)
        $sapHetHang = products::where('stock', '<', 5)->take(5)->get();

        return compact('topSanPham', 'sapHetHang');
    }

    /*
    |--------------------------------------------------------------------------
    | 4. NHÓM KHÁCH HÀNG & TƯƠNG TÁC
    |--------------------------------------------------------------------------
    */
    private function getThongKeKhachHang($hienTai)
    {
        $khachMoiThang = User::where('role', 0)
            ->whereMonth('created_at', $hienTai->month)
            ->count();

        $phanHoiMoi = Comment::whereMonth('created_at', $hienTai->month)->count();
        
        // Khách VIP (Chi nhiều tiền nhất)
        $khachVIP = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->select('users.name', 'users.email', DB::raw('SUM(orders.total_amount) as total_spent'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();

        return compact('khachMoiThang', 'phanHoiMoi', 'khachVIP');
    }

    /*
    |--------------------------------------------------------------------------
    | 5. NHÓM BIỂU ĐỒ TRỰC QUAN (CHARTS)
    |--------------------------------------------------------------------------
    */
    private function getDuLieuBieuDo($hienTai)
    {
        // 5.1 Biểu đồ đường (12 tháng)
        $bieuDoDoanhThu = [];
        for ($i = 1; $i <= 12; $i++) {
            $bieuDoDoanhThu[] = Order::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $hienTai->year)
                ->sum('total_amount');
        }

        // 5.2 Biểu đồ tròn (Tỷ trọng theo danh mục)
        $bieuDoDanhMuc = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->select('categories.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('categories.id', 'categories.name')
            ->pluck('total_sold', 'name')
            ->toArray();

        return compact('bieuDoDoanhThu', 'bieuDoDanhMuc');
    }
}