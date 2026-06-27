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
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        
        if($cart) {
            foreach ($cart as $id => $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
        }

        return view('User.checkout', compact('cart', 'totalAmount'));
    }

    public function process(Request $request)
    {
        // 1. Nhận dữ liệu gửi lên từ form checkout
        $payment_method = $request->input('payment_method'); 
        $shipping_name = $request->input('shipping_name');
        $shipping_phone = $request->input('shipping_phone');
        $full_address = $request->input('full_address'); 
        $notes = $request->input('order_notes');

        // 2. LẤY GIỎ HÀNG TỪ SESSION (Đồng bộ với code của Trung/Quốc Anh)
        $cart = session()->get('cart');

        // Nếu không có giỏ hàng, đá về trang chủ
        if (!$cart || count($cart) == 0) {
            return redirect()->route('user.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $totalAmount = 0;
        foreach ($cart as $id => $item) {
            $totalAmount += $item['price'] * $item['quantity']; // Tính tổng tiền
        }

        // 3. LƯU VÀO DATABASE
        DB::beginTransaction(); 
        try {
            // Insert vào bảng orders
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => Auth::id(), // ID người dùng
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
            foreach ($cart as $id => $item) {
                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_variant_id' => $id, 
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit(); // Xác nhận lưu thành công

            // 4. KIỂM TRA PHƯƠNG THỨC THANH TOÁN
            if ($payment_method == 'cod') {
                // CHÚ Ý: Mua COD thành công -> Xóa Session giỏ hàng
                session()->forget('cart');

                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công! Chúng tôi sẽ giao hàng sớm nhất.']);
            }
            elseif ($payment_method == 'vnpay') {
                // 1. Lấy cấu hình từ file .env
                $vnp_TmnCode = env('VNP_TMN_CODE');
                $vnp_HashSecret = env('VNP_HASH_SECRET');
                $vnp_Url = env('VNP_URL');
                $vnp_Returnurl = env('VNP_RETURN_URL');

                // 2. Chuẩn bị dữ liệu gửi sang VNPAY
                $vnp_TxnRef = $orderId; 
                $vnp_OrderInfo = "Thanh toan don hang #" . $orderId;
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $totalAmount * 100; 
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $request->ip(); 

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

                return redirect()->to($vnp_Url);
            }

        } catch (\Exception $e) {
            DB::rollBack(); 
            return "Lỗi Database: " . $e->getMessage();
        }
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

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
        $orderId = $inputData['vnp_TxnRef']; 

        if ($secureHash == $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Cập nhật trạng thái Database
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'processing', 
                    'payment_status' => 'Đã thanh toán' 
                ]);

                // CHÚ Ý: Thanh toán VNPAY thành công -> Xóa Session giỏ hàng
                session()->forget('cart');
                
                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Giao dịch thành công!']);
            } else {
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'cancelled'
                ]);
                return redirect()->route('checkout.index')->with('error', 'Giao dịch thất bại hoặc đã bị hủy. Mã lỗi: ' . $inputData['vnp_ResponseCode']);
            }
        } else {
            return "CẢNH BÁO LỖI BẢO MẬT: Chữ ký không hợp lệ! Dữ liệu có thể đã bị sửa đổi.";
        }
    }
}