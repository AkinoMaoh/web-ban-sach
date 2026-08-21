<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApplyVoucherRequest;
use App\Http\Requests\User\CheckoutRequest;
use App\Http\Requests\User\ShippingFeeRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Voucher;
use App\Services\CheckoutAddressService;
use App\Services\CheckoutCartService;
use App\Services\CheckoutService;
use App\Services\PaymentCallbackService;
use App\Services\OrderNotificationService;
use App\Services\ShippingService;
use App\Services\VnpayService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly CheckoutCartService $cartService,
        private readonly VoucherService $voucherService,
        private readonly ShippingService $shippingService,
        private readonly CheckoutAddressService $addressService,
        private readonly CheckoutService $checkoutService,
        private readonly VnpayService $vnpayService,
        private readonly PaymentCallbackService $paymentCallbackService,
        private readonly OrderNotificationService $notificationService
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
        $addresses = $this->addressService->addressesForUser(Auth::id());
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $checkoutToken = old('checkout_token')
            ?: session()->get('checkout_token')
            ?: (string) Str::uuid();

        session()->put('checkout_token', $checkoutToken);

        return view('User.checkout', compact(
            'cart',
            'totalAmount',
            'vouchers',
            'provinces',
            'addresses',
            'defaultAddress',
            'checkoutToken'
        ));
    }

    public function calculateShippingFee(ShippingFeeRequest $request): JsonResponse
    {
        $snapshot = $this->cartService->snapshot();

        if ($snapshot['order_items'] === []) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống hoặc sản phẩm đã thay đổi.',
            ], 422);
        }

        $validated = $request->validated();
        $quote = $this->shippingService->quote(
            (int) $validated['province_id'],
            (int) $validated['district_id'],
            (string) $validated['ward_code'],
            $snapshot['order_items']
        );

        return response()->json(['success' => true] + $quote);
    }

    public function process(CheckoutRequest $request): RedirectResponse|View
    {
        $validated = $request->validated();
        $userId = Auth::id();

        if ($validated['payment_method'] === 'vnpay' && ! $this->vnpayService->isConfigured()) {
            return back()
                ->withInput()
                ->with('error', 'VNPAY chưa được cấu hình. Vui lòng chọn COD hoặc liên hệ quản trị viên.');
        }

        try {
            $existing = $this->checkoutService->findExisting($validated, $userId);

            if ($existing !== null) {
                return $this->continueCheckout($existing, $request);
            }

            $snapshot = $this->cartService->snapshot();

            if ($snapshot['order_items'] === []) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Giỏ hàng trống hoặc các sản phẩm đã thay đổi.');
            }

            $result = $this->checkoutService->create($validated, $snapshot, $userId);

            return $this->continueCheckout($result, $request);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Không thể tạo đơn hàng lúc này. Vui lòng thử lại.');
        }
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
        try {
            $verified = $this->vnpayService->verifyCallback($request->all());
            $result = $this->paymentCallbackService->processVnpay($verified);
        } catch (ValidationException $exception) {
            session()->forget('checkout_token');

            return redirect()
                ->route('checkout.index')
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('checkout.index')
                ->with('error', 'Không thể xử lý phản hồi VNPAY lúc này.');
        }

        if ($result['state'] === 'paid') {
            $this->clearOrderCart($result['order']);
            session()->forget('checkout_token');

            if ($result['first_success']) {
                $this->notificationService->notifyAdmins(
                    $result['order'],
                    "Có đơn hàng mới (đã thanh toán VNPAY): {$result['order']->order_number}"
                );
                $this->notificationService->sendOrderConfirmation($result['order']);
            }

            return view('User.thankyou', [
                'order' => $result['order']->load('orderDetails'),
                'message' => 'Giao dịch thành công!',
                'clearCheckoutDraft' => true,
            ]);
        }

        if ($result['state'] === 'refund_required') {
            session()->forget('checkout_token');

            return view('User.thankyou', [
                'order' => $result['order'],
                'message' => 'Đã nhận thanh toán. Hệ thống đã chuyển giao dịch sang quy trình hoàn tiền.',
                'clearCheckoutDraft' => true,
            ]);
        }

        session()->forget('checkout_token');

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Giao dịch thất bại hoặc bạn đã hủy thanh toán.');
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        try {
            $verified = $this->vnpayService->verifyCallback($request->all());
            $result = $this->paymentCallbackService->processVnpay($verified);

            if ($result['state'] === 'paid') {
                $this->clearOrderCart($result['order']);

                if ($result['first_success']) {
                    $this->notificationService->notifyAdmins(
                        $result['order'],
                        "Có đơn hàng mới (đã thanh toán VNPAY): {$result['order']->order_number}"
                    );
                    $this->notificationService->sendOrderConfirmation($result['order']);
                }
            }

            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        } catch (ValidationException) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Signature']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['RspCode' => '99', 'Message' => 'Unknown Error']);
        }
    }

    /** @param array{order: Order, payment: Payment|null, created: bool} $result */
    private function continueCheckout(array $result, Request $request): RedirectResponse|View
    {
        $order = $result['order'];

        if ($order->payment_status === Order::PAYMENT_PAID) {
            $this->clearOrderCart($order);
            session()->forget('checkout_token');

            return view('User.thankyou', [
                'order' => $order->load('orderDetails'),
                'message' => 'Đơn hàng đã được thanh toán thành công!',
                'clearCheckoutDraft' => true,
            ]);
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            session()->forget('checkout_token');

            return back()
                ->withInput()
                ->with('error', 'Đơn hàng của phiên thanh toán này đã bị hủy. Vui lòng thử lại.');
        }

        if ($order->payment_method === 'cod') {
            $this->clearOrderCart($order);
            session()->forget('checkout_token');

            if ($result['created']) {
                $this->notificationService->notifyAdmins(
                    $order,
                    "Có đơn hàng mới (COD): {$order->order_number} từ khách {$order->shipping_name}"
                );
                $this->notificationService->sendOrderConfirmation($order);
            }

            return view('User.thankyou', [
                'order' => $order->load('orderDetails'),
                'message' => 'Đặt hàng thành công!',
                'clearCheckoutDraft' => true,
            ]);
        }

        if (! $result['payment']) {
            return back()
                ->withInput()
                ->with('error', 'Không tìm thấy phiên thanh toán VNPAY còn hiệu lực.');
        }

        return redirect()->to($this->vnpayService->paymentUrl(
            $result['payment'],
            $request->ip(),
            route('vnpay.return')
        ));
    }

    private function clearOrderCart(Order $order): void
    {
        $order->loadMissing('orderDetails');

        $this->cartService->clearPurchasedItems(
            $order->orderDetails->map(fn ($detail) => [
                'product_variant_id' => (int) $detail->product_variant_id,
                'price' => (float) $detail->price,
                'quantity' => (int) $detail->quantity,
            ])->all(),
            $order->user_id
        );
    }

}
