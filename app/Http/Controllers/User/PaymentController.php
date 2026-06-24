<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        return view('User.checkout');
    }

    public function process(Request $request)
    {
        // 1. Nhận dữ liệu gửi lên từ form checkout
        $payment_method = $request->input('payment_method'); 
        $shipping_name = $request->input('shipping_name');
        $shipping_phone = $request->input('shipping_phone');
        $full_address = $request->input('full_address'); // Lấy địa chỉ JS đã ghép nối
        $notes = $request->input('order_notes');

        // 2. TẠO MOCK DATA GIỎ HÀNG (Dữ liệu ảo)
        // Khi nào có giỏ hàng thật, bạn chỉ việc thay cái này bằng Session::get('cart')
        $mockCart = [
            ['product_variant_id' => 1, 'price' => 150000, 'quantity' => 1],
            ['product_variant_id' => 2, 'price' => 200000, 'quantity' => 1]
        ];
        $totalAmount = 350000;

        // 3. LƯU VÀO DATABASE
        DB::beginTransaction(); // Khởi tạo Transaction để an toàn dữ liệu
        try {
            // Insert vào bảng orders
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'pending', 
                'shipping_name' => $shipping_name,
                'shipping_phone' => $shipping_phone,
                'shipping_address' => $full_address,
                'notes' => $notes,
                'payment_method' => $payment_method,
                'created_at' => now(),
            ]);

            // Dùng Order ID vừa tạo để Insert chi tiết vào order_details
            foreach ($mockCart as $item) {
                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_variant_id' => $item['product_variant_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit(); // Xác nhận lưu thành công

            // 4. KIỂM TRA PHƯƠNG THỨC THANH TOÁN
            if ($payment_method == 'cod') {
                return "Tuyệt vời! Đã thanh toán tiền mặt.<br> Mã đơn: $orderId.<br> Địa chỉ giao: $full_address";
            } 
            elseif ($payment_method == 'vnpay') {
                // Bạn sẽ chèn code gọi API VNPAY vào đây ở bước sau
                return "Đã tạo đơn $orderId. Đang chuẩn bị chuyển hướng qua VNPAY...";
            }

        } catch (\Exception $e) {
            DB::rollBack(); // Trả lại như cũ nếu có lỗi
            return "Lỗi Database: " . $e->getMessage();
        }
    }
}