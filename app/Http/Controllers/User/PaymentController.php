<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApplyVoucherRequest;
use App\Http\Requests\User\CheckoutRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\productVariants;
use App\Models\User;
use App\Models\Voucher;
use App\Services\CheckoutCartService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly CheckoutCartService $cartService,
        private readonly VoucherService $voucherService
    ) {
    }

    public function index(Request $request): View
    {
        $snapshot = $this->cartService->selectAndSnapshot($request->query('items'));
        $cart = $snapshot['display_items'];
        $totalAmount = $snapshot['subtotal'];
        $customerKey = Auth::check()
            ? $this->voucherService->customerKey((int) Auth::id(), null)
            : null;

        $vouchers = Voucher::query()
            ->publiclyVisible()
            ->available()
            ->orderBy('end_date')
            ->get()
            ->filter(function (Voucher $voucher) use ($customerKey): bool {
                if ($voucher->usage_limit_per_customer === null || $customerKey === null) {
                    return true;
                }

                return $this->voucherService->customerUsageCount($voucher, $customerKey)
                    < $voucher->usage_limit_per_customer;
            })
            ->values();

        $provinces = DB::table('provinces')->orderBy('name')->get();
        $defaultAddress = Auth::check()
            ? DB::table('user_addresses')
                ->where('user_id', Auth::id())
                ->where('is_default', true)
                ->first()
            : null;

        return view(
            'User.checkout',
            compact('cart', 'totalAmount', 'vouchers', 'provinces', 'defaultAddress')
        );
    }

    public function calculateShippingFee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'ward_code' => ['required', 'string', 'exists:wards,code'],
        ]);

        return response()->json([
            'success' => true,
            'fee' => $this->shippingFee(
                (int) $validated['district_id'],
                (string) $validated['ward_code']
            ),
        ]);
    }

    public function process(CheckoutRequest $request): RedirectResponse|View
    {
        $validated = $request->validated();
        $snapshot = $this->cartService->snapshot();

        if ($snapshot['order_items'] === []) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Giỏ hàng trống hoặc các sản phẩm đã thay đổi.');
        }

        $userId = Auth::id();
        $subtotal = $snapshot['subtotal'];
        $quote = null;

        if (! empty($validated['applied_voucher'])) {
            try {
                $quote = $this->voucherService->quote(
                    $validated['applied_voucher'],
                    $subtotal,
                    $userId,
                    $validated['billing_email']
                );
            } catch (ValidationException $exception) {
                return back()->withErrors($exception->errors())->withInput();
            }
        }

        if ($validated['payment_method'] === 'vnpay'
            && (! env('VNP_TMN_CODE') || ! env('VNP_HASH_SECRET'))) {
            return back()
                ->withInput()
                ->with('error', 'VNPAY chưa được cấu hình. Vui lòng chọn COD hoặc liên hệ quản trị viên.');
        }

        $shippingFee = $this->shippingFee(
            (int) $validated['district_id'],
            (string) $validated['ward_code']
        );
        $discountAmount = $quote['discount_amount'] ?? 0.0;
        $payableSubtotal = max($subtotal - $discountAmount, 0);
        $totalAmount = round($payableSubtotal + $shippingFee, 2);
        $fullAddress = $this->fullAddress($validated);
        $order = null;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $userId,
                'billing_email' => $validated['billing_email'],
                'voucher_id' => $quote['voucher']->id ?? null,
                'voucher_code' => $quote['voucher']->code ?? null,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'shipping_fee' => $shippingFee,
                'status' => 'pending',
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $fullAddress,
                'notes' => $validated['order_notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'vnpay' ? 'pending' : 'unpaid',
            ]);

            if ($quote !== null) {
                $customerKey = $quote['customer_key']
                    ?? $this->voucherService->customerKey($userId, $validated['billing_email']);

                $this->voucherService->reserve(
                    $quote['voucher'],
                    $order,
                    $customerKey,
                    $discountAmount
                );
            }

            $this->createOrderDetails($snapshot['order_items'], $order->id);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            $this->cleanupFailedOrder($order);

            return back()->withErrors($exception->errors())->withInput();
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->cleanupFailedOrder($order);

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Không thể tạo đơn hàng lúc này. Vui lòng thử lại.');
        }

        if ($order->payment_method === 'cod') {
            $this->cartService->clearPurchasedItems($snapshot['order_items'], $userId);
            $this->notifyAdmins(
                $order,
                "Có đơn hàng mới (COD): #{$order->id} từ khách {$order->shipping_name}"
            );

            return view('User.thankyou', [
                'orderId' => $order->id,
                'message' => 'Đặt hàng thành công!',
            ]);
        }

        return redirect()->to($this->vnpayUrl($order, $request));
    }
    public function applyVoucher(ApplyVoucherRequest $request): JsonResponse
    {
        $snapshot = $this->cartService->snapshot();

        if ($snapshot['order_items'] === []) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống hoặc sản phẩm đã thay đổi.',
            ], 422);
        }

        try {
            $quote = $this->voucherService->quote(
                $request->validated('voucher_code'),
                $snapshot['subtotal'],
                Auth::id(),
                $request->validated('billing_email')
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
                'errors' => $exception->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công.',
            'voucher_code' => $quote['voucher']->code,
            'discount_amount' => $quote['discount_amount'],
            'payable_subtotal' => $quote['payable_subtotal'],
            'remaining_uses' => $quote['voucher']->remainingUses(),
        ]);
    }

    public function vnpayReturn(Request $request): RedirectResponse|View
    {
        $inputData = collect($request->all())
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'vnp_'))
            ->all();

        $receivedHash = (string) ($inputData['vnp_SecureHash'] ?? '');
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $secret = (string) env('VNP_HASH_SECRET');

        if ($secret === '') {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'VNPAY chưa được cấu hình trên hệ thống.');
        }

        $calculatedHash = hash_hmac(
            'sha512',
            http_build_query($inputData, '', '&', PHP_QUERY_RFC1738),
            $secret
        );

        if ($receivedHash === '' || ! hash_equals($calculatedHash, $receivedHash)) {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Không thể xác minh chữ ký phản hồi từ VNPAY.');
        }

        $orderId = filter_var($inputData['vnp_TxnRef'] ?? null, FILTER_VALIDATE_INT);
        $order = $orderId ? Order::with('orderDetails')->find($orderId) : null;

        if (! $order || $order->payment_method !== 'vnpay') {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Không tìm thấy đơn hàng VNPAY phù hợp.');
        }

        $receivedAmount = (int) ($inputData['vnp_Amount'] ?? 0);
        $expectedAmount = (int) round((float) $order->total_amount * 100);

        if ($receivedAmount !== $expectedAmount) {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Số tiền VNPAY trả về không khớp với đơn hàng.');
        }

        if (($inputData['vnp_ResponseCode'] ?? null) === '00') {
            if ($order->status === 'cancelled' && $order->payment_status !== 'paid') {
                return redirect()
                    ->route('checkout.index')
                    ->with('error', 'Đơn hàng này đã bị hủy trước khi thanh toán được xác nhận.');
            }

            $firstSuccessfulCallback = $order->payment_status !== 'paid';

            if ($firstSuccessfulCallback) {
                DB::transaction(function () use ($order, $inputData): void {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'payment_reference' => $inputData['vnp_TransactionNo'] ?? null,
                    ]);

                    $this->voucherService->markUsedForOrder($order);
                });

                $this->notifyAdmins(
                    $order,
                    "Có đơn hàng mới (đã thanh toán VNPAY): #{$order->id} từ khách {$order->shipping_name}"
                );
            }

            $this->cartService->clearPurchasedItems(
                $order->orderDetails->map(fn ($detail) => [
                    'product_variant_id' => (int) $detail->product_variant_id,
                    'price' => (float) $detail->price,
                    'quantity' => (int) $detail->quantity,
                ])->all(),
                $order->user_id
            );

            return view('User.thankyou', [
                'orderId' => $order->id,
                'message' => 'Giao dịch thành công!',
            ]);
        }

        if ($order->payment_status !== 'paid' && $order->status !== 'cancelled') {
            DB::transaction(function () use ($order, $inputData): void {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'payment_reference' => $inputData['vnp_TransactionNo'] ?? null,
                    'cancel_reason' => 'Thanh toán VNPAY không thành công.',
                ]);

                $this->voucherService->releaseForOrder($order);
            });
        }

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Giao dịch thất bại hoặc bạn đã hủy thanh toán.');
    }

    /**
     * @param array<int, array{product_variant_id: int, price: float, quantity: int}> $orderItems
     */
    private function createOrderDetails(array $orderItems, int $orderId): void
    {
        foreach ($orderItems as $item) {
            $variant = DB::table('product_variants')
                ->where('id', $item['product_variant_id'])
                ->lockForUpdate()
                ->first();

            if (! $variant || $variant->stock < $item['quantity']) {
                throw new \RuntimeException(
                    'Một sản phẩm đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng.'
                );
            }

            $variantModel = productVariants::with(['product', 'variant'])
                ->findOrFail($item['product_variant_id']);

            DB::table('order_details')->insert([
                'order_id' => $orderId,
                'product_variant_id' => $variantModel->id,
                'product_name' => $variantModel->product->name,
                'variant_name' => $variantModel->variant?->name ?? 'Mặc định',
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'image' => $variantModel->product->image,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function cleanupFailedOrder(?Order $order): void
    {
        if (! $order || ! Order::query()->whereKey($order->id)->exists()) {
            return;
        }

        $this->voucherService->releaseForOrder($order);

        Order::query()->whereKey($order->id)->update([
            'status' => 'cancelled',
            'cancel_reason' => 'Hệ thống không thể hoàn tất quá trình tạo đơn.',
        ]);
    }

    private function shippingFee(int $districtId, string $wardCode): float
    {
        if (! env('GHN_API_TOKEN') || ! env('GHN_SHOP_ID') || ! env('STORE_DISTRICT_ID')) {
            return 30000;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->retry(2, 200)
                ->withHeaders([
                    'Token' => env('GHN_API_TOKEN'),
                    'ShopId' => env('GHN_SHOP_ID'),
                ])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
                    'from_district_id' => (int) env('STORE_DISTRICT_ID'),
                    'to_district_id' => $districtId,
                    'to_ward_code' => $wardCode,
                    'weight' => 1000,
                    'service_type_id' => 2,
                ]);

            if ($response->successful() && is_numeric($response->json('data.total'))) {
                return max((float) $response->json('data.total'), 0);
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return 30000;
    }

    private function fullAddress(array $validated): string
    {
        $location = DB::table('wards')
            ->join('districts', 'wards.district_id', '=', 'districts.id')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->where('wards.code', $validated['ward_code'])
            ->where('districts.id', $validated['district_id'])
            ->where('provinces.id', $validated['province_id'])
            ->select([
                'wards.name as ward_name',
                'districts.name as district_name',
                'provinces.name as province_name',
            ])
            ->first();

        if (! $location) {
            throw ValidationException::withMessages([
                'ward_code' => 'Địa chỉ giao hàng không hợp lệ.',
            ]);
        }

        return implode(', ', [
            $validated['specific_address'],
            $location->ward_name,
            $location->district_name,
            $location->province_name,
        ]);
    }

    private function vnpayUrl(Order $order, Request $request): string
    {
        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => env('VNP_TMN_CODE'),
            'vnp_Amount' => (int) round((float) $order->total_amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => "Thanh toán đơn hàng #{$order->id}",
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => route('vnpay.return'),
            'vnp_TxnRef' => $order->id,
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC1738);
        $secureHash = hash_hmac('sha512', $query, (string) env('VNP_HASH_SECRET'));

        return 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?'
            .$query
            .'&vnp_SecureHash='
            .$secureHash;
    }

    private function notifyAdmins(Order $order, string $message): void
    {
        User::query()
            ->where('role', 1)
            ->each(function (User $admin) use ($order, $message): void {
                Notification::create([
                    'user_id' => $admin->id,
                    'order_id' => $order->id,
                    'message' => $message,
                    'is_read' => false,
                    'target_url' => route('admin.orders.edit', $order->id),
                ]);
            });
    }
}
