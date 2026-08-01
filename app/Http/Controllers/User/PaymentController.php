<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\User; 

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
                $variantIds = array_keys($sessionCart);

                if ($itemIds) {
                    $idsArray = explode(',', $itemIds);
                    $variantIds = array_intersect($variantIds, $idsArray);
                    session()->put('checkout_item_ids', $variantIds);
                }

                if (!empty($variantIds)) {
                    $variants = DB::table('product_variants')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->whereIn('product_variants.id', $variantIds)
                        ->select('product_variants.*', 'products.name')
                        ->get();

                    foreach ($variants as $variant) {
                        $vid = $variant->id;
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
        $vouchers = \App\Models\Voucher::where(function($query) {
        $query->whereNull('end_date')->orWhere('end_date', '>=', now());
        })->get();

        return view('User.checkout', compact('cart', 'totalAmount', 'vouchers'));
    }

    public function process(Request $request)
    {
        // 1. CHUẨN HÓA SỐ ĐIỆN THOẠI & EMAIL KÈM TIỀN SHIP VÀ VOUCHER
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'billing_email' => 'required|email|max:255',
            'full_address' => 'required|string',
            'shipping_fee' => 'required|numeric|min:0',
            'applied_voucher' => 'nullable|string', // Thêm dòng này để nhận mã giảm giá
        ], [
            'shipping_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_phone.regex' => 'Số điện thoại không hợp lệ.',
            'billing_email.required' => 'Vui lòng nhập địa chỉ Email.',
            'billing_email.email' => 'Địa chỉ Email không đúng định dạng.',
            'full_address.required' => 'Vui lòng nhập đầy đủ địa chỉ.',
        ]);

        $payment_method = $request->input('payment_method');
        $shipping_name = $validated['shipping_name'];
        $shipping_phone = $validated['shipping_phone'];
        $billing_email = $validated['billing_email'];
        $full_address = $validated['full_address'];
        $notes = $request->input('order_notes');
        $voucherCode = $request->input('applied_voucher'); // Lấy mã voucher từ input ẩn

        $totalAmount = 0;
        $realCart = [];
        $userId = Auth::check() ? Auth::id() : null;
        $checkoutItemIds = session()->get('checkout_item_ids');

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

        // --- BẢO MẬT: KIỂM TRA VÀ TÍNH LẠI VOUCHER Ở BACKEND ---
        $discountAmount = 0;
        $voucherId = null;

        if (!empty($voucherCode)) {
            // Tìm voucher trong database
            $voucher = \App\Models\Voucher::where('code', $voucherCode)->where('is_active', true)->first();

            // Kiểm tra xem voucher có hợp lệ và tổng tiền hàng có đạt mức tối thiểu không
            if ($voucher && $totalAmount >= $voucher->min_order_value) {
                $voucherId = $voucher->id; // Lấy ID để lưu vào cột voucher_id

                // Tính số tiền được giảm
                if ($voucher->type == 'percent') {
                    $discountAmount = ($totalAmount * $voucher->discount_value) / 100;
                    if ($voucher->max_discount_value && $discountAmount > $voucher->max_discount_value) {
                        $discountAmount = $voucher->max_discount_value;
                    }
                } else {
                    $discountAmount = $voucher->discount_value;
                }

                // Không cho phép giảm quá tổng tiền hàng
                if ($discountAmount > $totalAmount) {
                    $discountAmount = $totalAmount;
                }
            }
        }

        // Trừ tiền giảm giá ra khỏi tổng tiền hàng
        $totalAmount -= $discountAmount;
        if ($totalAmount < 0) $totalAmount = 0;

        // --- CỘNG TIỀN SHIP BÊN JS ĐẨY SANG VÀO TỔNG ĐƠN ---
        $shipping_fee = $validated['shipping_fee'];
        $totalAmount += $shipping_fee; 

        DB::beginTransaction(); 
        try {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'billing_email' => $billing_email,
                'total_amount' => $totalAmount,
                'shipping_fee' => $shipping_fee, 
                'status' => 'pending',
                'shipping_name' => $shipping_name,
                'shipping_phone' => $shipping_phone,
                'shipping_address' => $full_address,
                'notes' => $notes,
                'payment_method' => $payment_method,
                'voucher_id' => $voucherId, // <--- LƯU ID VOUCHER VÀO ĐÂY (Khớp với cột trong DB của bạn)
                'created_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            // --- NẾU DÙNG VOUCHER THÀNH CÔNG, TĂNG SỐ LƯỢT DÙNG LÊN 1 ---
            if ($voucherId) {
                \App\Models\Voucher::where('id', $voucherId)->increment('used_count');
            }

            // --- GỌI HÀM XỬ LÝ KHO ---
            $this->handleOrderAndStock($realCart, $orderId);

            DB::commit();

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
                        foreach ($checkoutItemIds as $vid) { unset($sessionCart[$vid]); }
                        session()->put('cart', $sessionCart);
                        session()->forget('checkout_item_ids');
                    } else {
                        session()->forget('cart');
                    }
                }

                $admins = User::where('role', 1)->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id'    => $admin->id,
                        'message'    => "Có đơn hàng mới (COD): #{$orderId} từ khách {$shipping_name}",
                        'is_read'    => false,
                        'target_url' => url('/admin/orders/' . $orderId)
                    ]);
                }

                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công!']);
            } 
            elseif ($payment_method == 'vnpay') {
                // --- LOGIC TẠO LINK VNPAY ---
                $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_Returnurl = url('/vnpay-return'); 
                $vnp_TmnCode = env('VNP_TMN_CODE'); 
                $vnp_HashSecret = env('VNP_HASH_SECRET'); 

                $vnp_TxnRef = $orderId; 
                $vnp_OrderInfo = "Thanh toán đơn hàng #" . $orderId;
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $totalAmount * 100; // VNPAY luôn nhân 100
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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
                    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                return redirect()->to($vnp_Url); 
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }
    }

    private function handleOrderAndStock($realCart, $orderId)
    {
        foreach ($realCart as $item) {
            $variant = DB::table('product_variants')
                ->where('id', $item['product_variant_id'])
                ->lockForUpdate()
                ->first();

            if (!$variant || $variant->stock < $item['quantity']) {
                throw new \Exception('Sản phẩm có ID ' . $item['product_variant_id'] . ' đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng!');
            }

            $variantModel = \App\Models\ProductVariants::with('product')->findOrFail($item['product_variant_id']);

            DB::table('order_details')->insert([
                'order_id'           => $orderId,
                'product_variant_id' => $variantModel->id,
                'product_name'       => $variantModel->product->name,
                'variant_name'       => $variantModel->edition,
                'price'              => $variantModel->price,
                'quantity'           => $item['quantity'],
                'subtotal'           => $variantModel->price * $item['quantity'],
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::table('product_variants')
                ->where('id', $item['product_variant_id'])
                ->decrement('stock', $item['quantity']);
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
                    
                    $admins = User::where('role', 1)->get();
                    foreach ($admins as $admin) {
                        Notification::create([
                            'user_id'    => $admin->id,
                            'message'    => "Có đơn hàng mới (Đã thanh toán VNPAY): #{$orderId} từ khách {$order->shipping_name}",
                            'is_read'    => false,
                            'target_url' => url('/admin/orders/' . $orderId) // Lưu cứng link chi tiết đơn hàng cho admin
                        ]);
                    }

                    if ($order->user_id) {
                        $boughtVariants = DB::table('order_details')
                            ->where('order_id', $orderId)
                            ->pluck('product_variant_id');

                        DB::table('carts')
                            ->where('user_id', $order->user_id)
                            ->whereIn('product_variant_id', $boughtVariants)
                            ->delete();
                    } else {
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
    public function applyVoucher(\Illuminate\Http\Request $request)
    {
        $code = $request->voucher_code;
        $totalOrder = $request->total_order; // Tổng tiền đơn hàng gửi lên từ giao diện

        // 1. Tìm voucher trong DB
        $voucher = \App\Models\Voucher::where('code', $code)->where('is_active', true)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã bị khóa!']);
        }

        // 2. Kiểm tra thời gian
        $now = now();
        if ($voucher->start_date && $now->lt($voucher->start_date)) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa đến thời gian sử dụng!']);
        }
        if ($voucher->end_date && $now->gt($voucher->end_date)) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);
        }

        // 3. Kiểm tra số lượng giới hạn
        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']);
        }

        // 4. Kiểm tra điều kiện đơn tối thiểu
        if ($totalOrder < $voucher->min_order_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt mức tối thiểu ' . number_format($voucher->min_order_value) . 'đ để áp dụng mã này!']);
        }

        // 5. Tính số tiền được giảm
        $discountAmount = 0;
        if ($voucher->type == 'percent') {
            $discountAmount = ($totalOrder * $voucher->discount_value) / 100;
            // Kiểm tra giới hạn giảm tối đa (nếu có)
            if ($voucher->max_discount_value && $discountAmount > $voucher->max_discount_value) {
                $discountAmount = $voucher->max_discount_value;
            }
        } else {
            $discountAmount = $voucher->discount_value;
        }

        // Chống giảm âm tiền (VD: đơn 50k mà mã giảm hẳn 100k)
        if ($discountAmount > $totalOrder) {
            $discountAmount = $totalOrder;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $discountAmount,
            'voucher_code' => $voucher->code
        ]);
    }
}