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
            'shipping_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'billing_email' => 'required|email|max:255',
            'full_address' => 'required|string',
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

            // --- GỌI HÀM MỚI TÁCH RA ---
            $this->handleOrderAndStock($realCart, $orderId);

            DB::commit();

            // ... (Code xử lý thanh toán COD / VNPAY giữ nguyên) ...
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
                return view('User.thankyou', ['orderId' => $orderId, 'message' => 'Đặt hàng thành công!']);
            } elseif ($payment_method == 'vnpay') {
                // ... (Phần VNPAY của ông giữ nguyên) ...
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
            // 1. Lock và kiểm tra kho
            $variant = DB::table('product_variants')
                ->where('id', $item['product_variant_id'])
                ->lockForUpdate()
                ->first();

            if (!$variant || $variant->stock < $item['quantity']) {
                throw new \Exception('Sản phẩm có ID ' . $item['product_variant_id'] . ' đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng!');
            }

            // 2. Lấy thông tin model để insert detail
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

            // 3. Trừ kho
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
