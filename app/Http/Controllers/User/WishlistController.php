<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    // 1. Hiển thị trang Sách yêu thích
    public function index()
    {
        // DÙNG leftJoin để nếu bảng products bị xóa, dữ liệu wishlist vẫn được giữ lại hiển thị
        $wishlists = DB::table('wishlists')
            ->leftJoin('products', 'wishlists.product_id', '=', 'products.id')
            ->where('wishlists.user_id', Auth::id())
            ->select(
                'wishlists.id as wishlist_id', 
                'wishlists.product_id as original_product_id', // Bắt buộc phải có để lấy được ID kể cả khi sp đã xóa
                'products.id as product_id', 
                'products.name', 
                'products.image'
            )
            ->orderBy('wishlists.id', 'desc')
            ->paginate(12);

        // Lấy giá hiển thị (Lấy giá của phiên bản đầu tiên làm đại diện)
        foreach ($wishlists as $item) {
            if ($item->product_id) {
                $variant = DB::table('product_variants')->where('product_id', $item->product_id)->first();
                $item->price = $variant ? $variant->price : 0;
            } else {
                $item->price = 0; // Sản phẩm đã bị xóa
            }
        }

        return view('User.wishlist', compact('wishlists'));
    }

    // 2. Thả tim / Bỏ thả tim (Dùng cho AJAX không load trang)
    public function toggle(Request $request)
    {
        $productId = $request->product_id;
        $userId = Auth::id();

        // Kiểm tra xem đã tym chưa
        $exists = DB::table('wishlists')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            // Đã tym rồi -> Hủy tym
            DB::table('wishlists')->where('id', $exists->id)->delete();
            return response()->json(['status' => 'removed', 'message' => 'Đã gỡ khỏi danh sách yêu thích!']);
        } else {
            // Chưa tym -> Thêm tym
            DB::table('wishlists')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => now('Asia/Ho_Chi_Minh'),
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);
            return response()->json(['status' => 'added', 'message' => 'Đã thêm vào danh sách yêu thích!']);
        }
    }

    // 3. Xóa khỏi danh sách (Trường hợp gọi load lại trang bằng route GET)
    public function remove($id)
    {
        DB::table('wishlists')->where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Đã gỡ sản phẩm khỏi danh sách.');
    }
}