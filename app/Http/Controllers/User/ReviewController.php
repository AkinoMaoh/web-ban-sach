<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'order_detail_id' => 'required|exists:order_details,id',
            'rating'          => 'required|integer|min:1|max:5',
            'comment'         => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        // Eager load variant để lấy edition
        $orderDetail = OrderDetail::with('variant')->findOrFail($request->order_detail_id);

        // Kiểm tra đã tồn tại review cho đơn hàng này chưa
        if (Review::where('order_detail_id', $request->order_detail_id)->exists()) {
            return back()->with('error', 'Bạn đã đánh giá đơn hàng này rồi!');
        }

        Review::create([
            'user_id'         => $user->id,
            'user_name'       => $user->name,
            'product_id'      => $request->product_id,
            'order_detail_id' => $request->order_detail_id,
            'variant_name'    => $orderDetail->variant->edition ?? 'Mặc định', // Đã sửa lỗi lấy sai tên phân loại
            'rating'          => $request->rating,
            'comment'         => $request->comment,
            'is_buyer'        => true
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }

    public function update(Request $request, $id) 
    {
        $r = Review::findOrFail($id);
        
        // Đã sửa !== thành != để tránh lỗi so sánh khác kiểu (String vs Int)
        if ($r->user_id != Auth::id()) {
            return back()->with('error', 'Không có quyền!');
        }
        
        $r->update(['rating' => $request->rating, 'comment' => $request->comment]);
        return back()->with('success', 'Đã cập nhật!');
    }

    public function destroy($id) 
    {
        $r = Review::findOrFail($id);
        
        // Đã sửa !== thành !=
        if ($r->user_id != Auth::id()) {
            return back()->with('error', 'Không có quyền!');
        }
        
        $r->delete();
        return back()->with('success', 'Đã xóa!');
    }

    public function toggleLike($id) 
    {
        $r = Review::findOrFail($id);
        $userId = Auth::id();
        
        // Đã viết lại logic Like/Dislike thủ công phù hợp với quan hệ One-to-Many
        $existingLike = $r->likes()->where('user_id', $userId)->first();
        
        if ($existingLike) {
            $existingLike->delete();
            $status = 'unliked';
        } else {
            $r->likes()->create(['user_id' => $userId]);
            $status = 'liked';
        }
        
        return response()->json([
            'status' => $status, 
            'likesCount' => $r->likes()->count()
        ]);
    }
}