<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Notification;

class ordersController extends Controller
{
    public function index(Request $request)
    {
    $query = Order::with('user');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('phone')) {
        $query->where('shipping_phone', 'like', $request->phone . '%');
    }

    $orders = $query->orderBy('created_at', 'desc')->paginate(15);

    return view('admin.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'orderDetails.productVariant.product',
        ])->findOrFail($id);

        return view('admin.ordershow', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with('user', 'orderDetails.productVariant.product')->findOrFail($id);

        return view('admin.orderedit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);

        $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];
        $currentIndex = array_search($order->status, $statusOrder);
        $newIndex = array_search($request->status, $statusOrder);

        if ($request->status !== 'cancelled' && $currentIndex !== false && $newIndex < $currentIndex) {
            return back()->with('error', 'Không thể chuyển về trạng thái trước đó.');
        }

        $order->status = $request->status;
        $order->save();

        // Mảng mapping tiếng Việt tại đây luôn
        $statusLabels = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        $statusVi = $statusLabels[$order->status] ?? $order->status;

        if ($order->user_id) {
            Notification::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id, 
                'message' => "Đơn hàng #{$order->id} đã chuyển sang trạng thái: " . $statusVi,
                'is_read' => false
            ]);
        }

        return redirect()->route('admin.orders')
            ->with('success', 'Đơn hàng #' . $order->id . ' đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->orderDetails()->delete();
        $order->delete();

        return redirect()->route('admin.orders')
            ->with('success', 'Đơn hàng #' . $id . ' đã được xóa thành công.');
    }

    // Tìm kiếm đơn hàng sđt bằng AJAX
    public function search(Request $request)
    {
        $orders = Order::with('user')
            ->where('shipping_phone', 'like', $request->phone . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }
}