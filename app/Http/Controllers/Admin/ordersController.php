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

    if ($request->filled('keyword')) {
        $query->where('shipping_phone', 'like', $request->keyword . '%');
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

        // BLOCK 1: Kiểm tra riêng điều kiện Hủy - Không cho hủy nếu đang giao hoặc hoàn thành
        if ($request->status === 'cancelled' && in_array($order->status, ['shipping', 'completed'])) {
            return back()->with('error', 'Không thể hủy đơn hàng khi đang giao hoặc đã hoàn thành.');
        }

        // BLOCK 2: Kiểm tra logic nhảy cóc & cập nhật lùi (Bỏ qua nếu chọn 'cancelled')
        if ($request->status !== 'cancelled' && $currentIndex !== false && $newIndex !== false) {
            
            if ($newIndex < $currentIndex) {
                return back()->with('error', 'Không thể chuyển về trạng thái trước đó.');
            }
            
            if ($newIndex - $currentIndex > 1) {
                return back()->with('error', 'Không thể nhảy cóc trạng thái. Vui lòng cập nhật lần lượt.');
            }
        }

        $order->status = $request->status;
        $order->save();

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
        $orders = Order::where('shipping_phone', 'like', $request->keyword . '%')
            ->select('shipping_phone')
            ->distinct()
            ->limit(5)
            ->get();

        return response()->json($orders);
    }
}