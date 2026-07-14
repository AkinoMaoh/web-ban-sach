<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewManagerController extends Controller
{
    /**
     * Hiển thị danh sách đánh giá
     */
    public function index(Request $request)
    {
        // Khởi tạo query và lấy kèm User, Product
        $query = Review::with(['user', 'product'])->latest();

        // Tích hợp Lọc theo Số sao (Nếu có chọn trên giao diện)
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        // Phân trang (mỗi trang 15 bình luận)
        $reviews = $query->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Admin xóa đánh giá vi phạm
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xóa bình luận vi phạm thành công!');
    }
}