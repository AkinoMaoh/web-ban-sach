<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Notification;

class ReviewManagerController extends Controller
{
    // Hiển thị danh sách đánh giá
    public function index()
    {
        // Lấy danh sách đánh giá mới nhất, kèm theo thông tin user và product
        $reviews = Review::with(['user', 'product'])->latest()->paginate(10);
        
        return view('admin.reviews', compact('reviews'));
    }

    // Admin trả lời đánh giá
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ], [
            'admin_reply.required' => 'Vui lòng nhập nội dung trả lời.'
        ]);

        // Eager load orderDetail và product để lấy thông tin tạo thông báo
        $review = Review::with(['orderDetail', 'product'])->findOrFail($id);
        
        // 1. Cập nhật câu trả lời của admin
        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        // 2. TẠO THÔNG BÁO CHO KHÁCH HÀNG (CLIENT)
        if ($review->user_id) {
            $productName = $review->product->name ?? 'sản phẩm';
            
            Notification::create([
                'user_id'    => $review->user_id,
                'order_id'   => $review->orderDetail->order_id ?? 0,
                'message'    => "Shop vừa phản hồi đánh giá của bạn về sách: {$productName}",
                'is_read'    => false,
                // Thêm dòng này: Gắn link trực tiếp tới sản phẩm đó
                'target_url' => route('user.productDetails', $review->product_id) 
            ]);
        }

        return back()->with('success', 'Đã gửi phản hồi và thông báo cho khách hàng!');
    }

    // Admin xóa đánh giá (nếu vi phạm)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công!');
    }
}