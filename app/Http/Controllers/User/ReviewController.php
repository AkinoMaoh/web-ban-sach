<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderDetail;
use App\Models\User; 
use App\Models\Notification; 
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
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Giới hạn ảnh 5MB
        ]);

        $user = Auth::user();
        
        // Load variant để lấy edition
        $orderDetail = OrderDetail::with('variant')->findOrFail($request->order_detail_id);

        // Kiểm tra đã tồn tại review cho đơn hàng này chưa
        if (Review::where('order_detail_id', $request->order_detail_id)->exists()) {
            return back()->with('error', 'Bạn đã đánh giá đơn hàng này rồi!');
        }

        // ==============================================================
        // ĐOẠN KHAI BÁO BỊ THIẾU CỦA BẠN NẰM Ở ĐÂY:
        // Khởi tạo $imageName = null, nếu khách có up ảnh thì mới đổi tên
        // ==============================================================
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/reviews'), $imageName);
        }
        // ==============================================================

        // 1. Lưu đánh giá vào database
        Review::create([
            'user_id'         => $user->id,
            'user_name'       => $user->name,
            'product_id'      => $request->product_id,
            'order_detail_id' => $request->order_detail_id,
            'variant_name'    => $orderDetail->variant->edition ?? 'Mặc định', 
            'rating'          => $request->rating,
            'comment'         => $request->comment,
            'image'           => $imageName, // BÂY GIỜ GỌI BIẾN NÀY SẼ KHÔNG BỊ LỖI NỮA
            'is_buyer'        => true
        ]);

        // 2. THÔNG BÁO CHO ADMIN 
        $admins = User::where('role', 1)->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'message'    => "Khách hàng {$user->name} vừa đánh giá {$request->rating} sao cho đơn hàng #{$orderDetail->order_id}",
                'is_read'    => false,
                'target_url' => route('admin.reviews.index') 
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }

    public function update(Request $request, $id) 
    {
        $r = Review::findOrFail($id);
        
        if ($r->user_id != Auth::id()) {
            return back()->with('error', 'Không có quyền!');
        }
        
        $r->update(['rating' => $request->rating, 'comment' => $request->comment]);
        return back()->with('success', 'Đã cập nhật!');
    }

    public function destroy($id) 
    {
        $r = Review::findOrFail($id);
        
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