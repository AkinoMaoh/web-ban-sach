<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

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

        $review = Review::findOrFail($id);
        
        // Cập nhật câu trả lời của admin
        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        return back()->with('success', 'Đã gửi phản hồi thành công!');
    }

    // Admin xóa đánh giá (nếu vi phạm)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công!');
    }
}