<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra nếu chưa đăng nhập thì bắt quay xe về trang login admin ngay
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // 2. Kiểm tra nếu không phải admin (role khác 1) -> Đăng xuất và báo lỗi
        if ((int)Auth::user()->role !== 1) {
            Auth::logout();
            abort(403, 'Bạn không có quyền truy cập vào khu vực Quản trị!');
        }

        // 3. KIỂM TRA TRẠNG THÁI PHÊ DUYỆT (is_active phải bằng 1)
        if ((int)Auth::user()->is_active !== 1) {
            Auth::logout(); // Đăng xuất session tạm thời của tài khoản này ra ngay lập tức
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Đẩy về trang đăng nhập kèm thông báo lỗi cụ thể
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Tài khoản Admin của bạn đang chờ phê duyệt. Vui lòng quay lại sau!'
            ]);
        }

        // Nếu vượt qua toàn bộ 3 chốt chặn trên thì mới cho phép đi tiếp
        return $next($request);
    }
}