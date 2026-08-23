<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // 2. Chỉ cho phép ID = 1 HOẶC Email Admin tối cao đi tiếp
        if ((int)$user->id === 1 || $user->email === 'ankinoto20@gmail.com') {
            return $next($request);
        }

        // 3. Nếu là Nhân viên gõ URL -> Đẩy về trang Danh sách sản phẩm kèm thông báo
        return redirect()->route('admin.products')->with('error', 'Bạn không có quyền truy cập vào chức năng này!');
    }
}
