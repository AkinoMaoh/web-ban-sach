<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\ProductVariants; // Giả sử bạn có model này

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'variant_id' => 'required', // Bắt buộc chọn phiên bản
            'quantity' => 'required|integer|min:1',
        ]);

        $product = products::findOrFail($request->product_id);
        // Lấy thông tin biến thể để kiểm tra giá và tồn kho
        $variant = ProductVariants::findOrFail($request->variant_id);

        // Logic giỏ hàng (Sử dụng Session)
        $cart = session()->get('cart', []);

        // Tạo key duy nhất cho sản phẩm + biến thể
        $cartKey = $product->id . '_' . $variant->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $variant->price,
                "image" => $product->image,
                "edition" => $variant->edition
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }
}
