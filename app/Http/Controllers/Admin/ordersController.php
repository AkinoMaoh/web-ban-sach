<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ordersController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Đã sửa lại đường dẫn trả về file orders.blade.php
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
        $order = Order::with('user','orderDetails.productVariant.product')->findOrFail($id);

        return view('admin.orderedit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

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

    public function toggleStatus($id)
    {
        $order = Order::findOrFail($id);

        $flow = [
            'pending'   => 'confirmed',
            'confirmed' => 'shipping',
            'shipping'  => 'completed',
        ];

        if (isset($flow[$order->status])) {
            $order->status = $flow[$order->status];
            $order->save();

            return redirect()->route('admin.orders')
                ->with('success', 'Trạng thái đơn hàng #' . $order->id . ' đã được cập nhật.');
        }

        return redirect()->route('admin.orders')
            ->with('error', 'Không thể chuyển trạng thái cho đơn hàng này.');
    }
}