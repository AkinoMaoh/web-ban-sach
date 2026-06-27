<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $itemIds = $request->query('items'); // Lấy ID các sách được tick chọn từ URL (VD: ?items=4)

        // Query lấy giỏ hàng từ Database, kết nối để lấy giá tiền và tên sách
        $query = DB::table('carts')
            ->join('product_variants', 'carts.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('carts.user_id', $userId)
            ->select('carts.*', 'product_variants.price', 'products.name');

        // Nếu khách có chọn cụ thể vài cuốn sách, thì chỉ lấy những cuốn đó
        if ($itemIds) {
            $idsArray = explode(',', $itemIds);
            $query->whereIn('carts.id', $idsArray);
            
            // Lưu mảng ID này vào session tạm để hàm process() bên dưới biết đường mà thanh toán
            session()->put('checkout_item_ids', $idsArray);
        }

        $cartItems = $query->get();

        $totalAmount = 0;
        $cart = [];
        
        foreach ($cartItems as $item) {
            $totalAmount += $item->price * $item->quantity;
            $cart[$item->id] = [
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ];
        }

        return view('User.checkout', compact('cart', 'totalAmount'));
    }

    public function process(Request $request)
    {
        $userId = Auth::id();
        $payment_method = $request->input('payment_method'); 
        $shipping_name = $request->input('shipping_name');
        $shipping_phone = $request->input('shipping_phone');
        $full_address = $request->input('full_address'); 
        $notes = $request->input('order_notes');

        // Lấy lại các ID giỏ hàng đã tick chọn
        $checkoutItemIds = session()->get('checkout_item_ids');

        $query = DB::table('carts')
            ->join('product_variants', 'carts.product_variant_id', '=', 'product_variants.id')
            ->where('carts.user_id', $userId)
            ->select('carts.*', 'product_variants.price');

        if ($checkoutItemIds) {
            $query->whereIn('carts.id', $checkoutItemIds);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.index')->with('error', 'Giỏ hàng của bạn đang trống hoặc đã được thanh toán!');
        }

        $totalAmount = 0;
        $realCart = [];

        foreach ($cartItems as $item) {
            $totalAmount += $item->price * $item->quantity;
            $realCart[] = [
                'product_variant_id' => $item->product_variant_id, 
                'price' => $item->price,
                'quantity' => $item->quantity,
            ];
        }

        DB::beginTransaction(); 
        try {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'status' => 'pending', 
                'shipping_name' => $shipping_name,
                'shipping_phone' => $shipping_phone,
                'shipping_address' => $full_address,
                'notes' => $notes,
                'payment_method' => $payment_method,
                'created_at' => now(),
            ]);

            foreach ($realCart as $item) {
                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_variant_id' => $item['product_variant_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit(); 

            // NẾU KHÁCH CHỌN TIỀN MẶT (COD)
            if ($payment_method == 'cod') {
                if ($checkoutItemIds) {
                    DB::table('carts')->whereIn('id', $checkoutItemIds)->delete();
                    session()->forget('checkout_item_ids');
                } else {
                    DB::table('carts')->where('user_id', $userId)->delete();
                }

                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công! Chúng tôi sẽ giao hàng sớm nhất.']);
            }
            // NẾU KHÁCH CHỌN VNPAY
            elseif ($payment_method == 'vnpay') {
                $vnp_TmnCode = env('VNP_TMN_CODE');
                $vnp_HashSecret = env('VNP_HASH_SECRET');
                $vnp_Url = env('VNP_URL');
                $vnp_Returnurl = env('VNP_RETURN_URL');

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
                $query_string = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query_string .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query_string;
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
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'processing'
                ]);

                // XÓA SẢN PHẨM KHỎI GIỎ HÀNG SAU KHI VNPAY THÀNH CÔNG
                $order = DB::table('orders')->where('id', $orderId)->first();
                if ($order) {
                    $boughtVariants = DB::table('order_details')
                        ->where('order_id', $orderId)
                        ->pluck('product_variant_id');
                        
                    DB::table('carts')
                        ->where('user_id', $order->user_id)
                        ->whereIn('product_variant_id', $boughtVariants)
                        ->delete();
                }
                
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