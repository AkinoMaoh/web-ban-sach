<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Services\OrderLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ordersController extends Controller
{
    public function __construct(private readonly OrderLifecycleService $lifecycleService)
    {
    }

    public function index(Request $request): View
    {
        $query = Order::query()->with(['user', 'orderDetails']);

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));
            $query->where(function ($subQuery) use ($keyword): void {
                $subQuery
                    ->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('shipping_phone', 'like', "%{$keyword}%")
                    ->orWhere('billing_email', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = $this->findOrder($id);

        return view('admin.ordershow', compact('order'));
    }

    public function edit(int $id): View
    {
        $order = $this->findOrder($id);

        return view('admin.orderedit', compact('order'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_SHIPPING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])],
            'cancel_reason' => ['nullable', 'required_if:status,cancelled', 'string', 'max:500'],
        ]);
        $order = $this->findOrder($id);
        $newStatus = $validated['status'];

        if ($newStatus === $order->status) {
            return back()->with('success', 'Trạng thái đơn hàng không thay đổi.');
        }

        try {
            if ($newStatus === Order::STATUS_CANCELLED) {
                $result = $this->lifecycleService->requestCancellation(
                    $order,
                    $validated['cancel_reason']
                );

                if ($result === 'refund_requested') {
                    $this->notifyCustomer(
                        $order,
                        "Đơn {$order->order_number} đang chờ xử lý hoàn tiền trước khi hủy."
                    );

                    return back()->with(
                        'success',
                        'Đã tạo yêu cầu hoàn tiền. Chỉ đánh dấu đã hoàn tiền sau khi giao dịch thực tế hoàn tất.'
                    );
                }

                $order->refresh();
            } else {
                $this->assertNextStatus($order, $newStatus);

                if ($newStatus === Order::STATUS_COMPLETED) {
                    $order = $this->lifecycleService->complete($order);
                } else {
                    $order->update(['status' => $newStatus, 'cancel_reason' => null]);
                }
            }
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        $this->notifyCustomer(
            $order,
            "Đơn {$order->order_number} đã chuyển sang trạng thái: ".$this->statusLabel($order->status)
        );

        return back()->with('success', "Đơn {$order->order_number} đã được cập nhật.");
    }

    public function markRefunded(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'refund_reference' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $order = $this->lifecycleService->markRefunded(
                $id,
                $validated['refund_reference'] ?? null
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        $this->notifyCustomer(
            $order,
            "Đơn {$order->order_number} đã hoàn tiền và được hủy thành công."
        );

        return back()->with('success', 'Đã xác nhận hoàn tiền, hoàn tồn kho và hoàn lượt voucher.');
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));
        $orders = Order::query()
            ->where(function ($query) use ($keyword): void {
                $query
                    ->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('shipping_phone', 'like', "%{$keyword}%");
            })
            ->select(['id', 'order_number', 'shipping_phone'])
            ->latest('id')
            ->limit(8)
            ->get();

        return response()->json($orders);
    }

    private function findOrder(int $id): Order
    {
        return Order::query()
            ->with(['user', 'orderDetails', 'payments', 'inventoryReservations'])
            ->findOrFail($id);
    }

    private function assertNextStatus(Order $order, string $newStatus): void
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'Không thể khôi phục đơn đã hủy bằng thao tác đổi trạng thái.',
            ]);
        }

        if ($order->refund_status !== Order::REFUND_NONE) {
            throw ValidationException::withMessages([
                'status' => 'Đơn hàng đang trong quy trình hoàn tiền.',
            ]);
        }

        $nextStatuses = [
            Order::STATUS_PENDING => Order::STATUS_CONFIRMED,
            Order::STATUS_CONFIRMED => Order::STATUS_SHIPPING,
            Order::STATUS_SHIPPING => Order::STATUS_COMPLETED,
        ];

        if (($nextStatuses[$order->status] ?? null) !== $newStatus) {
            throw ValidationException::withMessages([
                'status' => 'Vui lòng cập nhật đơn hàng lần lượt theo đúng quy trình.',
            ]);
        }

        if ($newStatus === Order::STATUS_CONFIRMED
            && $order->payment_method === 'vnpay'
            && ! $order->isPaid()) {
            throw ValidationException::withMessages([
                'payment' => 'Không thể xác nhận đơn VNPAY chưa thanh toán.',
            ]);
        }
    }

    private function notifyCustomer(Order $order, string $message): void
    {
        if (! $order->user_id) {
            return;
        }

        Notification::query()->create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'message' => $message,
            'is_read' => false,
            'target_url' => route('user.orderHistory.show', $order->id),
        ]);
    }

    private function statusLabel(string $status): string
    {
        return [
            Order::STATUS_PENDING => 'Chờ xác nhận',
            Order::STATUS_CONFIRMED => 'Đã xác nhận',
            Order::STATUS_SHIPPING => 'Đang giao hàng',
            Order::STATUS_COMPLETED => 'Đã hoàn thành',
            Order::STATUS_CANCELLED => 'Đã hủy',
        ][$status] ?? $status;
    }
}
