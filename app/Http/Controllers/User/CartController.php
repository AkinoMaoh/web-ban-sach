<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        // Lấy dữ liệu giỏ hàng (DB hoặc Session)
        $cartItems = Auth::check() 
            ? Cart::where('user_id', Auth::id())->with('variant.product')->get()
            : collect(session('cart', []))->map(function($item) {
                // Ép kiểu mảng để đảm bảo tính an toàn
                $item = (array) $item; 
                
                // Lớp bảo vệ: Tự động tìm key 'variant_id' hoặc 'id', nếu không có thì gán null
                $variantId = $item['variant_id'] ?? ($item['id'] ?? null);
                
                return (object) [
                    'variant_id' => $variantId,
                    'quantity'   => $item['quantity'] ?? 1,
                    'variant'    => $variantId ? ProductVariants::with('product')->find($variantId) : null
                ];
            });

        // Tính toán trạng thái cho từng item
        foreach ($cartItems as $item) {
            if ($item->variant) {
                // Kiểm tra hết hàng hoặc quá số lượng
                $item->is_out_of_stock = ($item->variant->stock <= 0 || $item->quantity > $item->variant->stock);
                $item->unit_price = $item->variant->price;
                $item->subtotal = $item->variant->price * $item->quantity;
            } else {
                $item->is_out_of_stock = true;
                $item->unit_price = 0;
                $item->subtotal = 0;
            }
        }

        return view('User.cart', compact('cartItems'));
    }

    public function updateCart(Request $request)
    {
        $qty = (int)$request->quantity;
        if ($qty < 1) $qty = 1;

        $oldId = $request->old_variant_id;
        $newId = $request->product_variant_id;

        if (Auth::check()) {
            if ($oldId != $newId) {
                Cart::where('user_id', Auth::id())->where('product_variant_id', $oldId)->delete();
                Cart::updateOrCreate(['user_id' => Auth::id(), 'product_variant_id' => $newId], ['quantity' => $qty]);
            } else {
                Cart::where('user_id', Auth::id())->where('product_variant_id', $oldId)->update(['quantity' => $qty]);
            }
        } else {
            $cart = session('cart', []);
            if ($oldId != $newId) {
                unset($cart[$oldId]);
                $cart[$newId] = ['variant_id' => $newId, 'quantity' => $qty];
            } else {
                $cart[$newId]['quantity'] = $qty;
            }
            session(['cart' => $cart]);
        }

        return response()->json(['success' => true]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $vId = $request->product_variant_id;
        $qty = (int)$request->quantity;

        if (Auth::check()) {
            $existingItem = Cart::where('user_id', Auth::id())->where('product_variant_id', $vId)->first();
            if ($existingItem) {
                $existingItem->increment('quantity', $qty);
            } else {
                Cart::create(['user_id' => Auth::id(), 'product_variant_id' => $vId, 'quantity' => $qty]);
            }
        } else {
            $cart = session('cart', []);
            $cart[$vId] = [
                'variant_id' => $vId,
                'quantity' => ($cart[$vId]['quantity'] ?? 0) + $qty
            ];
            session(['cart' => $cart]);
        }

        if ($request->action_type === 'buy_now') {
            return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
        }

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function remove($id)
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->where('product_variant_id', $id)->delete();
        } else {
            $cart = session('cart', []);
            unset($cart[$id]);
            session(['cart' => $cart]);
        }
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}