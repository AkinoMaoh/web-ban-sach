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
            'cancel_reason' => 'nullable|string|max:255'
        ]);

        $order = Order::with('orderDetails')->findOrFail($id);

        $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];

        $currentIndex = array_search($order->status, $statusOrder);
        $newIndex = array_search($request->status, $statusOrder);

        // Chặn hủy đơn khi đang giao hoặc hoàn thành
        if ($request->status === 'cancelled' && in_array($order->status, ['shipping', 'completed'])) {
            return back()->with('error', 'Không thể hủy đơn hàng khi đang giao hoặc đã hoàn thành.');
        }

        // Chặn lùi trạng thái hoặc nhảy cóc
        if ($request->status !== 'cancelled' && $currentIndex !== false && $newIndex !== false) {
            if ($newIndex < $currentIndex) {
                return back()->with('error', 'Không thể chuyển về trạng thái trước đó.');
            }
            if ($newIndex - $currentIndex > 1) {
                return back()->with('error', 'Không thể chuyển nhiều trạng thái. Vui lòng cập nhật lần lượt.');
            }
        }

        // --- BẮT ĐẦU KIỂM TRA SẢN PHẨM BỊ XÓA / HẾT HÀNG (HOẠT ĐỘNG TRÊN MỌI TRẠNG THÁI) ---
        if ($request->status !== 'cancelled') {

            foreach ($order->orderDetails as $detail) {
                $variant = null;
                $product = null;

                // Lấy biến thể, nếu biến thể còn sống thì phải dò tiếp xem sản phẩm cha còn sống không
                if ($detail->product_variant_id) {
                    $variant = productVariants::find($detail->product_variant_id);
                    if ($variant) {
                        $product = products::find($variant->product_id); // <-- BỔ SUNG QUAN TRỌNG
                    }
                }

                $dbCancelReason = "";
                $adminFlashError = "";
                $productName = $detail->product_name . ' (' . $detail->variant_name . ')';

                // TRƯỜNG HỢP 1: Sản phẩm HOẶC Biến thể đã bị xóa khỏi cơ sở dữ liệu
                if (!$variant || !$product) {
                    $dbCancelReason = "Sản phẩm '{$productName}' hiện đã ngừng kinh doanh.";
                    $adminFlashError = "Hệ thống chặn cập nhật: Sản phẩm '{$productName}' đã bị xóa khỏi hệ thống.";
                }
                // TRƯỜNG HỢP 2: Hết hàng / không đủ số lượng (Chỉ kiểm tra khi duyệt đơn: pending -> confirmed)
                elseif ($request->status === 'confirmed' && $order->status === 'pending' && $variant->stock < $detail->quantity) {

                    // Phân biệt rõ 2 tình huống: HẾT HÀNG HẲN (stock = 0) và KHÔNG ĐỦ SỐ LƯỢNG (còn hàng nhưng ít hơn yêu cầu)
                    if ($variant->stock <= 0) {
                        $dbCancelReason = "Sản phẩm '{$productName}' hiện đã hết hàng.";
                        $adminFlashError = "Hệ thống tự động hủy: Sản phẩm '{$productName}' đã HẾT HÀNG (Yêu cầu: {$detail->quantity}, Tồn kho: 0).";
                    } else {
                        $dbCancelReason = "Sản phẩm '{$productName}' hiện không đủ số lượng trong kho (Hết hàng một phần).";
                        $adminFlashError = "Hệ thống tự động hủy: Sản phẩm '{$productName}' KHÔNG ĐỦ SỐ LƯỢNG (Yêu cầu: {$detail->quantity}, Chỉ còn: {$variant->stock}).";
                    }
                }

                // NẾU PHÁT HIỆN LỖI (Bị xóa hoặc Hết hàng/Thiếu kho)
                if ($adminFlashError !== "") {

                    // Nếu đơn đang Chờ xử lý hoặc Đã xác nhận -> Lập tức tự động HỦY ĐƠN
                    if (in_array($order->status, ['pending', 'confirmed'])) {

                        // Nếu đơn đã ở trạng thái "confirmed" thì kho đã bị trừ ở bước duyệt đơn
                        // trước đó (pending -> confirmed). Trước khi hủy, phải HOÀN LẠI kho cho toàn bộ
                        // sản phẩm trong đơn (những sản phẩm/biến thể còn tồn tại trong hệ thống),
                        // tránh tình trạng kho bị trừ oan cho một đơn đã hủy.
                        if ($order->status === 'confirmed') {
                            foreach ($order->orderDetails as $restoreDetail) {
                                if ($restoreDetail->product_variant_id) {
                                    $restoreVariant = productVariants::find($restoreDetail->product_variant_id);
                                    if ($restoreVariant) {
                                        $restoreVariant->stock += $restoreDetail->quantity;
                                        $restoreVariant->save();
                                    }
                                    // Nếu biến thể đã bị xóa thì không còn bản ghi để hoàn kho
                                }
                            }
                        }

                        $order->status = 'cancelled';
                        $order->cancel_reason = $dbCancelReason;
                        $order->save();

                        if ($order->user_id) {
                            Notification::create([
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'message' => "Đơn hàng #{$order->id} của bạn đã bị hủy. Lý do: {$dbCancelReason}",
                                'is_read' => false
                            ]);
                        }
                        return back()->with('error', $adminFlashError . ' Đơn hàng đã tự động bị hủy!');
                    }
                    // Nếu đơn Đang giao (shipping) mà phát hiện sản phẩm bị xóa -> Chặn lại không cho hoàn thành
                    else {
                        return back()->with('error', $adminFlashError . ' Không thể tiếp tục cập nhật trạng thái.');
                    }
                }
            }
        }
        // --- KẾT THÚC KIỂM TRA LỖI SẢN PHẨM ---


        // --- TRỪ KHO (CHỈ CHẠY KHI DUYỆT ĐƠN HỢP LỆ) ---
        if ($request->status === 'confirmed' && $order->status === 'pending') {
            foreach ($order->orderDetails as $detail) {
                $variant = productVariants::find($detail->product_variant_id);
                if ($variant) {
                    $variant->stock -= $detail->quantity;
                    $variant->stock = max(0, $variant->stock);
                    $variant->save();
                }
            }
        }

        // Xử lý nếu ADMIN CHỦ ĐỘNG HỦY TỪ FORM (Nhập tay)
        if ($request->status === 'cancelled') {
            // Nếu admin chủ động hủy đơn đang ở trạng thái "confirmed" (kho đã bị trừ),
            // cũng cần hoàn lại kho tương tự như trường hợp tự động hủy ở trên.
            if ($order->status === 'confirmed') {
                foreach ($order->orderDetails as $restoreDetail) {
                    if ($restoreDetail->product_variant_id) {
                        $restoreVariant = productVariants::find($restoreDetail->product_variant_id);
                        if ($restoreVariant) {
                            $restoreVariant->stock += $restoreDetail->quantity;
                            $restoreVariant->save();
                        }
                    }
                }
            }

            $order->cancel_reason = $request->input('cancel_reason', 'Đơn hàng bị hủy từ hệ thống quản trị.');
        } else {
            $order->cancel_reason = null;
        }

        // Cập nhật trạng thái mới
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

        // Tạo thông báo cho khách hàng khi chuyển trạng thái bình thường
        if ($order->user_id) {
            Notification::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'message' => "Đơn hàng #{$order->id} đã chuyển sang trạng thái: " . $statusVi,
                'is_read' => false
            ]);
        }

        return back()->with('success', 'Đơn hàng #' . $order->id . ' đã được cập nhật thành công.');
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