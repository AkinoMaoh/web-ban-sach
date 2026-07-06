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
        $cart = [];
        $totalAmount = 0;
        $itemIds = $request->query('items');

        // LUỒNG 1: KHÁCH ĐÃ ĐĂNG NHẬP (Lấy từ Database)
        if (Auth::check()) {
            $userId = Auth::id();
            $query = DB::table('carts')
                ->join('product_variants', 'carts.product_variant_id', '=', 'product_variants.id')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->where('carts.user_id', $userId)
                ->select('carts.*', 'product_variants.price', 'products.name');

            if ($itemIds) {
                $idsArray = explode(',', $itemIds);
                $query->whereIn('carts.id', $idsArray);
                session()->put('checkout_item_ids', $idsArray);
            }

            $cartItems = $query->get();

            foreach ($cartItems as $item) {
                $totalAmount += $item->price * $item->quantity;
                $cart[$item->id] = [
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'variant_id' => $item->product_variant_id
                ];
            }
        }
        // LUỒNG 2: KHÁCH VÃNG LAI (Lấy từ Session)
        else {
            $sessionCart = session()->get('cart', []);

            if (!empty($sessionCart)) {
                // Lấy tất cả mã biến thể (variant_id) đang có trong session
                $variantIds = array_keys($sessionCart);

                // Lọc theo các món khách vừa tick chọn (nếu có)
                if ($itemIds) {
                    $idsArray = explode(',', $itemIds);
                    $variantIds = array_intersect($variantIds, $idsArray);
                    session()->put('checkout_item_ids', $variantIds);
                }

                if (!empty($variantIds)) {
                    // TRUY VẤN DB ĐỂ LẤY GIÁ THẬT (Chống lỗi thiếu Price từ Session)
                    $variants = DB::table('product_variants')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->whereIn('product_variants.id', $variantIds)
                        ->select('product_variants.*', 'products.name')
                        ->get();

                    foreach ($variants as $variant) {
                        $vid = $variant->id;
                        // An toàn: nếu session thiếu số lượng thì gán mặc định là 1
                        $quantity = isset($sessionCart[$vid]['quantity']) ? $sessionCart[$vid]['quantity'] : 1;

                        $totalAmount += $variant->price * $quantity;
                        $cart[$vid] = [
                            'name' => $variant->name,
                            'price' => $variant->price,
                            'quantity' => $quantity,
                            'variant_id' => $vid
                        ];
                    }
                }
            }
        }

        return view('User.checkout', compact('cart', 'totalAmount'));
    }

    public function process(Request $request)
    {
        // 1. CHUẨN HÓA SỐ ĐIỆN THOẠI & EMAIL
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            // Bắt buộc 10 số, bắt đầu bằng số 0 (Chuẩn Việt Nam)
            'shipping_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'billing_email' => 'required|email|max:255',
            'full_address' => 'required|string',
        ], [
            'shipping_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_phone.regex' => 'Số điện thoại không hợp lệ. Phải gồm 10 chữ số và bắt đầu bằng số 0 (VD: 098...).',
            'billing_email.required' => 'Vui lòng nhập địa chỉ Email.',
            'billing_email.email' => 'Địa chỉ Email không đúng định dạng (VD: ten@gmail.com).',
            'full_address.required' => 'Vui lòng nhập đầy đủ địa chỉ.',
        ]);

        $payment_method = $request->input('payment_method');
        $shipping_name = $validated['shipping_name'];
        $shipping_phone = $validated['shipping_phone'];
        $billing_email = $validated['billing_email'];
        $full_address = $validated['full_address'];
        $notes = $request->input('order_notes');

        $totalAmount = 0;
        $realCart = [];
        $userId = Auth::check() ? Auth::id() : null;
        $checkoutItemIds = session()->get('checkout_item_ids');

        // ... (Khúc lấy dữ liệu giỏ hàng giữ nguyên) ...
        if (Auth::check()) {
            $query = DB::table('carts')
                ->join('product_variants', 'carts.product_variant_id', '=', 'product_variants.id')
                ->where('carts.user_id', $userId)
                ->select('carts.*', 'product_variants.price');

            if ($checkoutItemIds) {
                $query->whereIn('carts.id', $checkoutItemIds);
            }

            $cartItems = $query->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('user.index')->with('error', 'Giỏ hàng trống hoặc đã thanh toán!');
            }

            foreach ($cartItems as $item) {
                $totalAmount += $item->price * $item->quantity;
                $realCart[] = [
                    'product_variant_id' => $item->product_variant_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];
            }
        } else {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('user.index')->with('error', 'Giỏ hàng trống!');
            }

            $variantIds = $checkoutItemIds ? $checkoutItemIds : array_keys($sessionCart);

            $variants = DB::table('product_variants')->whereIn('id', $variantIds)->get();
            if ($variants->isEmpty()) {
                return redirect()->route('user.index')->with('error', 'Sản phẩm không hợp lệ!');
            }

            foreach ($variants as $variant) {
                $vid = $variant->id;
                $quantity = isset($sessionCart[$vid]['quantity']) ? $sessionCart[$vid]['quantity'] : 1;
                $totalAmount += $variant->price * $quantity;
                $realCart[] = [
                    'product_variant_id' => $vid,
                    'price' => $variant->price,
                    'quantity' => $quantity,
                ];
            }
        }

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'billing_email' => $billing_email,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_name' => $shipping_name,
                'shipping_phone' => $shipping_phone,
                'shipping_address' => $full_address,
                'notes' => $notes,
                'payment_method' => $payment_method,
                'created_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            foreach ($realCart as $item) {
                // 2. KIỂM TRA SỐ LƯỢNG KHO TRƯỚC KHI TRỪ
                $variant = DB::table('product_variants')->where('id', $item['product_variant_id'])->lockForUpdate()->first();

                if (!$variant || $variant->stock < $item['quantity']) {
                    DB::rollBack(); // Hủy toàn bộ giao dịch nếu có 1 món hết hàng
                    return redirect()->route('checkout.index')->with('error', 'Sản phẩm có ID ' . $item['product_variant_id'] . ' đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng!');
                }

                $variant = \App\Models\ProductVariants::with('product')
                    ->findOrFail($item['product_variant_id']);

                DB::table('order_details')->insert([
                    'order_id'           => $orderId,
                    'product_variant_id' => $variant->id,
                    'product_name'       => $variant->product->name,
                    'variant_name'       => $variant->edition,
                    'price'              => $variant->price,
                    'quantity'           => $item['quantity'],
                    'subtotal'           => $variant->price * $item['quantity'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                // 3. TRỪ SỐ LƯỢNG TRONG KHO (product_variants)
                DB::table('product_variants')
                    ->where('id', $item['product_variant_id'])
                    ->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // ... (Khúc xử lý COD và VNPAY bên dưới giữ nguyên y hệt) ...
            if ($payment_method == 'cod') {
                if (Auth::check()) {
                    if ($checkoutItemIds) {
                        DB::table('carts')->whereIn('id', $checkoutItemIds)->delete();
                        session()->forget('checkout_item_ids');
                    } else {
                        DB::table('carts')->where('user_id', $userId)->delete();
                    }
                } else {
                    if ($checkoutItemIds) {
                        $sessionCart = session()->get('cart', []);
                        foreach ($checkoutItemIds as $vid) {
                            unset($sessionCart[$vid]);
                        }
                        session()->put('cart', $sessionCart);
                        session()->forget('checkout_item_ids');
                    } else {
                        session()->forget('cart');
                    }
                }
                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công!']);
            } elseif ($payment_method == 'vnpay') {
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

                $order = DB::table('orders')->where('id', $orderId)->first();
                if ($order) {
                    if ($order->user_id) {
                        $boughtVariants = DB::table('order_details')
                            ->where('order_id', $orderId)
                            ->pluck('product_variant_id');

                        DB::table('carts')
                            ->where('user_id', $order->user_id)
                            ->whereIn('product_variant_id', $boughtVariants)
                            ->delete();
                    } else {
                        // Khách vãng lai: Xóa các món đã mua khỏi session
                        $checkoutItemIds = session()->get('checkout_item_ids');
                        if ($checkoutItemIds) {
                            $sessionCart = session()->get('cart', []);
                            foreach ($checkoutItemIds as $vid) {
                                unset($sessionCart[$vid]);
                            }
                            session()->put('cart', $sessionCart);
                            session()->forget('checkout_item_ids');
                        } else {
                            session()->forget('cart');
                        }
                    }
                }

                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Giao dịch thành công!']);
            } else {
                // 1. Cập nhật đơn hàng thành Đã hủy
                DB::table('orders')->where('id', $orderId)->update([
                    'status' => 'cancelled'
                ]);

                // 2. [QUAN TRỌNG] CỘNG TRẢ LẠI SỐ LƯỢNG VÀO KHO
                $cancelledItems = DB::table('order_details')->where('order_id', $orderId)->get();
                foreach ($cancelledItems as $item) {
                    DB::table('product_variants')
                        ->where('id', $item->product_variant_id)
                        ->increment('stock', $item->quantity);
                }

                return redirect()->route('checkout.index')->with('error', 'Giao dịch thất bại hoặc bạn đã hủy thanh toán. Hàng hóa đã được hoàn lại kho.');
            }
        } else {
            return "CẢNH BÁO LỖI BẢO MẬT: Chữ ký không hợp lệ!";
        }
    }
}
