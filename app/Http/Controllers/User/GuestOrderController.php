<?php

namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Response;

class GuestOrderController extends Controller
{
    public function show(string $orderNumber, string $token): Response
    {
        $order = Order::query()
            ->with('orderDetails')
            ->where('order_number', $orderNumber)
            ->where('tracking_token', $token)
            ->firstOrFail();

        return response()
            ->view('User.order_tracking', compact('order'))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('Referrer-Policy', 'no-referrer');
    }
    // Hàm 1: Hiển thị giao diện Form
        public function showTrackForm()
        {
            return view('User.track_order_form'); // Trỏ đến file view ở Bước 4
        }

        // Hàm 2: Xử lý khi khách bấm nút Tra cứu
        public function processTrackForm(Request $request)
        {
            $request->validate([
                'order_number' => 'required|string',
                'shipping_phone' => 'required|string'
            ], [
                'order_number.required' => 'Vui lòng nhập mã đơn hàng.',
                'shipping_phone.required' => 'Vui lòng nhập số điện thoại.'
            ]);

            // Tìm đơn hàng khớp CẢ HAI điều kiện (Mã + SĐT)
            $order = Order::where('order_number', $request->order_number)
                        ->where('shipping_phone', $request->shipping_phone)
                        ->first();

            // Nếu nhập sai mã hoặc SĐT
            if (!$order) {
                return redirect()->back()->with('error', 'Không tìm thấy đơn hàng! Vui lòng kiểm tra lại Mã đơn và Số điện thoại.');
            }

            // Nếu tìm thấy, chuyển hướng thẳng sang trang Chi tiết có Token bảo mật mà bạn đã làm hôm qua
            return redirect()->route('order.track', [
                'orderNumber' => $order->order_number,
                'token' => $order->tracking_token
            ]);
        }
}
