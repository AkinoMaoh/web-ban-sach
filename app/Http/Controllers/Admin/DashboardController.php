<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Comment;
use App\Models\products;
use App\Models\productVariants;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * HÀM CHÍNH GỌI GIAO DIỆN DASHBOARD
     */
    public function index(Request $request) 
    {
        $hienTai = Carbon::now();

        // Lần lượt gọi các hàm xử lý bên dưới
        $taiChinh   = $this->getThongKeTaiChinh($hienTai);
        $donHang    = $this->getThongKeDonHang();
        $sanPham    = $this->getThongKeSanPham();
        $khachHang  = $this->getThongKeKhachHang($hienTai);
        $bieuDo     = $this->getDuLieuBieuDo($hienTai, $request); 

     
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
        $sapHetHang = ProductVariants::with('product') // Lấy kèm thông tin sản phẩm cha
                ->where('stock', '<', 5)
                ->take(5)
                ->get();

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

        // Khách VIP (Chi nhiều tiền nhất)
        $khachVIP = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->select('users.name', 'users.email', DB::raw('SUM(orders.total_amount) as total_spent'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();

        return compact('khachMoiThang',  'khachVIP');
    }

    /*
    |--------------------------------------------------------------------------
    | 5. NHÓM BIỂU ĐỒ TRỰC QUAN (CHARTS)
    |--------------------------------------------------------------------------
    */
    private function getDuLieuBieuDo($hienTai, $request) // <-- Nhận thêm $request vào hàm
    {
        $bieuDoDoanhThu = [];
        $bieuDoDoanhThuLabels = [];

        // 5.1 Xử lý Biểu đồ đường (Doanh thu)
        if ($request->filled('start_date') || $request->filled('end_date')) {
            // NẾU CÓ CHỌN NGÀY LỌC
            $query = Order::where('status', 'completed');

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Sắp xếp tăng dần theo thời gian
            $orders = $query->orderBy('created_at', 'asc')->get();

            // Nhóm kết quả theo ngày/tháng
            $revenueByDate = $orders->groupBy(function($order) {
                return Carbon::parse($order->created_at)->format('d/m/Y');
            });

            foreach ($revenueByDate as $date => $dayOrders) {
                $bieuDoDoanhThuLabels[] = $date;
                $bieuDoDoanhThu[] = $dayOrders->sum('total_amount');
            }

            // Phòng trường hợp lọc nhưng không có đơn nào
            if (empty($bieuDoDoanhThuLabels)) {
                $bieuDoDoanhThuLabels = ['Không có dữ liệu'];
                $bieuDoDoanhThu = [0];
            }

        } else {
            // NẾU KHÔNG LỌC (MẶC ĐỊNH: Lấy 12 tháng)
            for ($i = 1; $i <= 12; $i++) {
                $bieuDoDoanhThu[] = Order::where('status', 'completed')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $hienTai->year)
                    ->sum('total_amount');
            }
            $bieuDoDoanhThuLabels = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        }

        // 5.2 Xử lý Biểu đồ tròn (Tỷ trọng theo danh mục)
        $queryDanhMuc = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed');

        // Áp dụng luôn bộ lọc ngày cho biểu đồ Tròn (nếu có lọc thì dữ liệu 2 chart sẽ đồng nhất)
        if ($request->filled('start_date')) {
            $queryDanhMuc->whereDate('orders.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $queryDanhMuc->whereDate('orders.created_at', '<=', $request->end_date);
        }

        $bieuDoDanhMuc = $queryDanhMuc->select('categories.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('categories.id', 'categories.name')
            ->pluck('total_sold', 'name')
            ->toArray();

        // Trả thêm $bieuDoDoanhThuLabels ra view
        return compact('bieuDoDoanhThu', 'bieuDoDoanhThuLabels', 'bieuDoDanhMuc');
    }
}