<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách đơn hàng của User đang đăng nhập, load kèm chi tiết
        $orders = Order::with('orderDetails')
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'desc')
                    ->paginate(10);

                    
        foreach ($orders as $order) {
            $order->chi_tiet = $order->orderDetails;
        }

        return view('User.history', compact('orders'));
    }

    public function show($id)
    {
        // 1. Lấy thông tin đơn hàng
        $order = Order::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->first();

        // Kiểm tra bảo mật URL
        if (!$order) {
            return redirect()->route('user.history')->with('error', 'Đơn hàng không tồn tại hoặc không thuộc quyền sở hữu của bạn!');
        }

        // 2. Lấy chi tiết đơn hàng (Dùng Model OrderDetail)
        // Vẫn load kèm productVariant.product để check xem sản phẩm CÒN KINH DOANH KHÔNG (phục vụ nút Đánh giá)
        // Cột `image` mặc định đã được lấy ra cùng bảng order_details
        $orderDetails = OrderDetail::with('productVariant.product')
                            ->where('order_id', $id)
                            ->get();

        return view('User.order_detail', compact('order', 'orderDetails'));
    }

    public function cancel($id)
    {
        // Dùng Eloquent Model 
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$order || !in_array($order->status, ['pending'])) {
            return back()->with('error', 'Đơn hàng này không thể hủy!');
        }

        $order->status = 'cancelled';
        $order->save();
        
        return back()->with('success', 'Đơn hàng đã được hủy!');
    }
}