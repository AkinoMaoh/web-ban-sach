<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Notification;
use App\Models\User;
use App\Services\OrderLifecycleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderHistoryController extends Controller
{
    public function __construct(private readonly OrderLifecycleService $lifecycleService)
    {
    }

    public function index()
    {
        // 1. Lấy danh sách đơn hàng của User đang đăng nhập, load kèm chi tiết
        $orders = Order::with('orderDetails')
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'desc')
                    ->paginate(10);

                    
        foreach ($orders as $order) {
            $order->chi_tiet = $order->orderDetails;
        }

        return view('User.history', compact('orders'));
    }

    public function show($id)
    {
        // 1. Lấy thông tin đơn hàng
        $order = Order::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->first();

        // Kiểm tra bảo mật URL
        if (!$order) {
            return redirect()->route('user.orderHistory')->with('error', 'Đơn hàng không tồn tại hoặc không thuộc quyền sở hữu của bạn!');
        }

        // 2. Lấy chi tiết đơn hàng (Dùng Model OrderDetail)
        // Vẫn load kèm productVariant.product để check xem sản phẩm CÒN KINH DOANH KHÔNG (phục vụ nút Đánh giá)
        // Cột `image` mặc định đã được lấy ra cùng bảng order_details
        $orderDetails = OrderDetail::with('productVariant.product')
                            ->where('order_id', $id)
                            ->get();

        return view('User.order_detail', compact('order', 'orderDetails'));
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();

        if (! $order || $order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Đơn hàng này không thể hủy!');
        }

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);
        $reason = $validated['cancel_reason'] ?? 'Khách hàng yêu cầu hủy đơn.';

        try {
            $result = $this->lifecycleService->requestCancellation($order, $reason);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        if ($result === 'refund_requested') {
            User::query()->where('role', 1)->each(function (User $admin) use ($order): void {
                Notification::query()->create([
                    'user_id' => $admin->id,
                    'order_id' => $order->id,
                    'message' => "Khách yêu cầu hủy và hoàn tiền đơn {$order->order_number}.",
                    'is_read' => false,
                    'target_url' => route('admin.orders.edit', $order->id),
                ]);
            });

            return back()->with(
                'success',
                'Đã gửi yêu cầu hủy và hoàn tiền. Quản trị viên sẽ kiểm tra giao dịch.'
            );
        }

        return back()->with('success', 'Đơn hàng đã được hủy và tồn kho đã được hoàn lại!');
    }
}
