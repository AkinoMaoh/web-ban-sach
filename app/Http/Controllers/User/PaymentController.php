<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Nhớ thêm dòng này để dùng DB Query Builder

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
            // 4. KIỂM TRA PHƯƠNG THỨC THANH TOÁN
            if ($payment_method == 'cod') {
                // Nếu cần thiết, bạn có thể xóa giỏ hàng (session) ở đây trước khi chuyển trang
                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công! Chúng tôi sẽ giao hàng sớm nhất.']);
            }
            elseif ($payment_method == 'vnpay') {
            // 1. Lấy cấu hình từ file .env
            $vnp_TmnCode = env('VNP_TMN_CODE');
            $vnp_HashSecret = env('VNP_HASH_SECRET');
            $vnp_Url = env('VNP_URL');
            $vnp_Returnurl = env('VNP_RETURN_URL');

            // 2. Chuẩn bị dữ liệu gửi sang VNPAY
            $vnp_TxnRef = $orderId; // Dùng luôn ID đơn hàng vừa tạo làm mã giao dịch
            $vnp_OrderInfo = "Thanh toan don hang #" . $orderId;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $totalAmount * 100; // VNPAY bắt buộc nhân 100 (VD: 350.000đ thành 35000000)
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $request->ip(); // Lấy IP của khách hàng

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef
            );

            // 3. Sắp xếp dữ liệu và tạo chuỗi mã hóa bảo mật (Hash)
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            // 4. Chuyển hướng người dùng sang trang thanh toán của VNPAY
            return redirect()->to($vnp_Url);
        }

        } catch (\Exception $e) {
            DB::rollBack(); // Trả lại như cũ nếu có lỗi
            return "Lỗi Database: " . $e->getMessage();
        }
    }
    public function vnpayReturn(Request $request)
    {
        // 1. Lấy mã bí mật từ file .env
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        
        // 2. Lấy toàn bộ dữ liệu VNPAY trả về trên thanh URL
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        // 3. Tách cái chữ ký an toàn (vnp_SecureHash) ra để kiểm tra
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // 4. Thuật toán tạo lại chữ ký để đối chiếu (Chống hacker sửa dữ liệu)
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // 5. Kiểm tra kết quả
        $orderId = $inputData['vnp_TxnRef']; // Mã đơn hàng nãy mình gửi đi

        if ($secureHash == $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // MÃ 00 LÀ THÀNH CÔNG -> Cập nhật Database
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'processing', // Hoặc trạng thái nào team bạn quy định
                    'payment_status' => 'Đã thanh toán' // Giả sử bạn có cột này
                ]);
                
                // Trả về trang giao diện cảm ơn
                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Giao dịch thành công!']);
            } else {
                // CÁC MÃ LỖI KHÁC (Khách hủy giao dịch, thẻ hết tiền...)
                // Cập nhật trạng thái đơn hàng thành Hủy (tùy chọn)
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'cancelled'
                ]);

                // Đẩy về trang checkout và báo lỗi
                return redirect()->route('checkout.index')->with('error', 'Giao dịch thất bại hoặc đã bị hủy. Mã lỗi: ' . $inputData['vnp_ResponseCode']);
            }
        } else {
            return "CẢNH BÁO LỖI BẢO MẬT: Chữ ký không hợp lệ! Dữ liệu có thể đã bị sửa đổi.";
        }
    }
}