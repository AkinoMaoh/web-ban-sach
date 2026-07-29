<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function redirect($id)
    {
        $n = Notification::findOrFail($id);

        // 1. Đánh dấu đã đọc
        $n->update(['is_read' => 1]);

        // 2. Xử lý ưu tiên nếu có sẵn target_url
        if (!empty($n->target_url)) {
            return redirect()->to($n->target_url);
        }

        // 3. Logic xử lý cũ cho đơn hàng / đánh giá (Dành cho các thông báo cũ không có target_url)
        $message = mb_strtolower($n->message, 'UTF-8');

        // Luồng cho Admin (role == 1)
        if (Auth::user()->role == 1) {
            if (str_contains($message, 'đánh giá')) {
                return redirect()->route('admin.reviews.index');
            }
            if ($n->order_id) {
                return redirect('/admin/orders/' . $n->order_id);
            }
        } 
        // Luồng cho Khách hàng (role == 2)
        else {
            if (str_contains($message, 'phản hồi')) {
                $review = Review::where('user_id', $n->user_id)
                    ->whereHas('orderDetail', function ($q) use ($n) {
                        $q->where('order_id', $n->order_id);
                    })->latest()->first();

                if ($review) {
                    return redirect()->to(route('user.productDetails', $review->product_id) . '#review-' . $review->id);
                }
            }
            if ($n->order_id) {
                return redirect('/order-history/' . $n->order_id);
            }
        }

        return back();
    }

    public function destroy($id)
    {
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back();
    }

    public function readAll()
    {
        Notification::where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return back();
    }
}