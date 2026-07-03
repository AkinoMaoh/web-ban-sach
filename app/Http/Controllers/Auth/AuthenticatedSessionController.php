<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // Thư viện dùng để quăng lỗi ra ngoài giao diện form

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
        // 1. Tiến hành xác thực email và mật khẩu dựa trên cấu hình mặc định của Breeze
        $request->authenticate();

        // 2. CHỐT CHẶN: Nếu hệ thống nhận diện tài khoản vừa đăng nhập là ADMIN (role == 1)
        if ((int)Auth::user()->role === 1) {
            
            // Lập tức đăng xuất tài khoản Admin này ra khỏi phiên làm việc của người dùng
            Auth::logout();

            // Xóa sạch session và làm mới token hiện tại để đảm bảo an toàn tuyệt đối
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Đẩy ngược về lại form đăng nhập và hiển thị thông báo lỗi màu đỏ ngay tại ô nhập Email
            throw ValidationException::withMessages([
                'email' => 'Tài khoản Admin không được phép đăng nhập tại đây! Vui lòng truy cập trang đăng nhập dành riêng cho Quản trị viên.',
            ]);
        }

        // 3. Nếu tài khoản đăng nhập là người dùng thường (role khác 1), cho phép tạo session làm việc
        $request->session()->regenerate();

        // Điều hướng User thường về thẳng trang chủ mua sách của bạn
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