<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    // 1. Hiển thị Form Đăng Nhập Admin
    public function showLoginForm() {
        return view('auth.admin-login');
    }

    // 2. Xử lý Đăng Nhập Admin (Có kiểm tra trạng thái kích hoạt)
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // Đăng nhập thành công -> Kiểm tra quyền xem có phải admin không (So sánh bằng số 1)
            if ((int)Auth::user()->role === 1) {
                
                // KIỂM TRA XEM ĐÃ ĐƯỢC DUYỆT CHƯA
                if ((int)Auth::user()->is_active === 0) {
                    Auth::logout(); // Đăng xuất ngay lập tức
                    throw ValidationException::withMessages([
                        'email' => 'Tài khoản Admin của bạn đang chờ phê duyệt. Vui lòng quay lại sau!',
                    ]);
                }

                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Nếu không phải Admin -> Đăng xuất ngay và báo lỗi
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'Tài khoản này không có quyền Quản trị!']);
        }

        throw ValidationException::withMessages(['email' => __('auth.failed')]);
    }

    // 3. Hiển thị Form Đăng Ký Admin
    public function showRegisterForm() {
        return view('auth.admin-register');
    }

    // 4. Xử lý Đăng Ký Admin (Mặc định bắt chờ admin cũ duyệt)
    public function register(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Tạo tài khoản và để trạng thái is_active = 0 (Chờ duyệt)
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 1,          // ĐỔI THÀNH SỐ 1: Cố định quyền quản trị trong Database kiểu INT
            'is_active' => 0,     // Mặc định là 0 (Chờ duyệt)
        ]);

        // Đá về trang đăng nhập kèm thông báo màu xanh lá
        return redirect()->route('admin.login')->with('status', 'Đăng ký Admin thành công! Vui lòng đợi Quản trị viên cấp cao phê duyệt tài khoản.');
    }

    // 5. Đăng xuất Admin
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}