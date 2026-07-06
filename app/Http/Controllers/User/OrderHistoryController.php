<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderHistoryController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách đơn hàng của User đang đăng nhập
        $orders = Order::where('user_id', Auth::id())
                    ->orderBy('id', 'desc')
                    ->paginate(10);

        // 2. Lấy chi tiết sản phẩm cho từng đơn hàng (Dùng DB::table giống phong cách của bạn)
        foreach ($orders as $order) {
            $order->chi_tiet = DB::table('order_details')
                ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->where('order_details.order_id', $order->id)
                ->select('order_details.*', 'products.name as product_name', 'products.image as product_image')
                ->get();
        }

        return view('User.history', compact('orders'));
    }

    public function show($id)
    {
        // 1. Lấy thông tin đơn hàng (phải đảm bảo đơn này của đúng User đang đăng nhập)
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        // Nếu cố tình nhập ID bậy bạ trên thanh URL
        if (!$order) {
            return redirect()->route('user.history')->with('error', 'Đơn hàng không tồn tại hoặc không thuộc quyền sở hữu của bạn!');
        }

        // 2. Lấy danh sách sản phẩm trong đơn hàng đó
        $orderDetails = DB::table('order_details')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('order_details.order_id', $id)
            ->select('order_details.*', 'products.name as product_name', 'products.image as product_image', 'product_variants.edition')
            ->get();

        return view('User.order_detail', compact('order', 'orderDetails'));
    }

    public function cancel($id)
    {
        $order = DB::table('orders')->where('id', $id)->where('user_id', Auth::id())->first();

        if (!$order || !in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Đơn hàng này không thể hủy!');
        }

        DB::table('orders')->where('id', $id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Đơn hàng đã được hủy!');
    }
}