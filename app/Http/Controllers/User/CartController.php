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
            'variant_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = products::findOrFail($request->product_id);
        $variant = ProductVariants::findOrFail($request->variant_id);

        // Kiểm tra tồn kho
        if ($request->quantity > $variant->stock) {
            return redirect()->back()->withErrors([
                'quantity' => 'Số lượng vượt quá tồn kho. Hiện chỉ còn ' . $variant->stock . ' sản phẩm.'
            ]);
        }

        $cart = session()->get('cart', []);

        $cartKey = $product->id . '_' . $variant->id;

        if (isset($cart[$cartKey])) {

            $newQuantity = $cart[$cartKey]['quantity'] + $request->quantity;

            // Kiểm tra tổng số lượng trong giỏ không vượt tồn kho
            if ($newQuantity > $variant->stock) {
                return redirect()->back()->withErrors([
                    'quantity' => 'Tổng số lượng trong giỏ vượt quá tồn kho. Hiện chỉ còn ' . $variant->stock . ' sản phẩm.'
                ]);
            }

            $cart[$cartKey]['quantity'] = $newQuantity;
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
