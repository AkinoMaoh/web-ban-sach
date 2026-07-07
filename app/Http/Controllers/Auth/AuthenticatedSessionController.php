<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use App\Models\Cart;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Xác thực thông tin đăng nhập từ Request
        $request->authenticate();

        // 2. CHỐT CHẶN: Nếu tài khoản là Admin (role == 1)
        if ((int)Auth::user()->role === 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Tài khoản Admin không được phép đăng nhập tại đây!',
            ]);
        }

        // 3. ĐỒNG BỘ GIỎ HÀNG: Chuyển từ Session sang Database
        if (session()->has('cart')) {
            foreach (session('cart') as $variantId => $item) {
                // $item['quantity'] là số lượng
                Cart::updateOrCreate(
                    [
                        'user_id' => Auth::id(), 
                        'product_variant_id' => $variantId
                    ],
                    [
                        'quantity' => DB::raw("quantity + " . (int)$item['quantity'])
                    ]
                );
            }
            // Xóa sạch session sau khi đồng bộ
            session()->forget('cart');
        }

        // 4. Tạo session làm việc
        $request->session()->regenerate();

        // 5. Điều hướng về trang chủ
        return redirect()->route('user.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}