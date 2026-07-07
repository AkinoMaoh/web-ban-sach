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
        $cartItems = Auth::check() 
            ? Cart::where('user_id', Auth::id())->with('variant.product')->get()
            : collect(session('cart', []))->map(fn($item) => (object) [
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'variant' => ProductVariants::find($item['variant_id'])
            ]);

        // Kiểm tra tồn kho cho từng sản phẩm
        foreach ($cartItems as $item) {
            if ($item->variant) {
                $item->is_out_of_stock = ($item->variant->stock <= 0 || $item->quantity > $item->variant->stock);
            } else {
                $item->is_out_of_stock = true;
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
        $vId = $request->product_variant_id;
        $qty = (int)$request->quantity;

        if (Auth::check()) {
            Cart::updateOrCreate(
                ['user_id' => Auth::id(), 'product_variant_id' => $vId],
                ['quantity' => DB::raw("quantity + {$qty}")]
            );
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
        return back()->with('success', 'Đã xóa sản phẩm!');
    }
}