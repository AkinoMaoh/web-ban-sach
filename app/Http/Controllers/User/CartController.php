<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductVariant;
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
                return (object) [
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'variant' => ProductVariant::with('product')->find($item['variant_id'])
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
        // Chốt chặn: Số lượng tối thiểu là 1
        $qty = (int)$request->quantity;
        if ($qty < 1) $qty = 1;

        $oldId = $request->old_variant_id;
        $newId = $request->product_variant_id;

        if (Auth::check()) {
            // Xử lý DB
            if ($oldId != $newId) {
                // Xóa cái cũ, thêm cái mới
                Cart::where('user_id', Auth::id())->where('product_variant_id', $oldId)->delete();
                Cart::updateOrCreate(['user_id' => Auth::id(), 'product_variant_id' => $newId], ['quantity' => $qty]);
            } else {
                Cart::where('user_id', Auth::id())->where('product_variant_id', $oldId)->update(['quantity' => $qty]);
            }
        } else {
            // Xử lý Session
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

        // Lưu vào DB hoặc Session
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

        // Điều hướng: Nếu mua ngay thì sang giỏ hàng, nếu không thì quay lại trang cũ
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