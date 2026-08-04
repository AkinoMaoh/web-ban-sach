<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Notification;
use App\Models\products;
use App\Models\productVariants;
use Illuminate\Http\Request;

class ordersController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderDetails']);

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
        $order = Order::with(['user', 'orderDetails'])->findOrFail($id);
        
        return view('admin.ordershow', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'orderDetails'])->findOrFail($id);
        
        return view('admin.orderedit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
        ]);

        $order = Order::with('orderDetails')->findOrFail($id);

        $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];
        
        $currentIndex = array_search($order->status, $statusOrder);
        $newIndex = array_search($request->status, $statusOrder);

        if ($request->status === 'cancelled' && in_array($order->status, ['shipping', 'completed'])) {
            return back()->with('error', 'Không thể hủy đơn hàng khi đang giao hoặc đã hoàn thành.');
        }

        if ($request->status !== 'cancelled' && $currentIndex !== false && $newIndex !== false) {
            if ($newIndex < $currentIndex) {
                return back()->with('error', 'Không thể chuyển về trạng thái trước đó.');
            }
            if ($newIndex - $currentIndex > 1) {
                return back()->with('error', 'Không thể nhảy cóc trạng thái. Vui lòng cập nhật lần lượt.');
            }
        }

        // --- BẮT ĐẦU XỬ LÝ TRỪ TỒN KHO ---
        // Chỉ trừ số lượng ở bảng productVariants khi chuyển từ pending sang confirmed
        if ($request->status === 'confirmed' && $order->status === 'pending') {
            
            // Bước 1: KIỂM TRA TỒN KHO TẤT CẢ SẢN PHẨM TRONG ĐƠN HÀNG TRƯỚC
            foreach ($order->orderDetails as $detail) {
                $variant = productVariants::find($detail->product_variant_id);
                
                // Nếu sản phẩm không tồn tại hoặc số lượng yêu cầu lớn hơn tồn kho hiện tại
                if (!$variant || $variant->stock < $detail->quantity) {
                    
                    // Lập tức Hủy đơn hàng
                    $order->status = 'cancelled';
                    $order->save();

                    // Gửi thông báo cho khách hàng biết đơn bị hủy do hết hàng
                    if ($order->user_id) {
                        Notification::create([
                            'user_id' => $order->user_id,
                            'order_id' => $order->id, 
                            'message' => "Đơn hàng #{$order->id} của bạn đã bị hủy do một hoặc nhiều sản phẩm không đủ số lượng trong kho.",
                            'is_read' => false
                        ]);
                    }

                    // Trả về thông báo lỗi cho Admin
                    return redirect()->route('admin.orders')
                        ->with('error', 'Lỗi: Sản phẩm trong kho không đủ số lượng. Đơn hàng #' . $order->id . ' đã TỰ ĐỘNG BỊ HỦY!');
                }
            }

            // Bước 2: NẾU BƯỚC 1 THÀNH CÔNG (Đủ hàng), MỚI TIẾN HÀNH TRỪ KHO THỰC TẾ
            foreach ($order->orderDetails as $detail) {
                $variant = productVariants::find($detail->product_variant_id);
                if ($variant) {
                    $variant->stock -= $detail->quantity;
                    $variant->stock = max(0, $variant->stock); // Đảm bảo không bao giờ bị âm
                    $variant->save();
                }
            }
        }
        // --- KẾT THÚC XỬ LÝ TỒN KHO ---

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

        // Tạo thông báo cho khách hàng với các trạng thái bình thường
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