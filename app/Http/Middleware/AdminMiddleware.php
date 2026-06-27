<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- ĐẢM BẢO CÓ DÒNG IMPORT NÀY
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
        // Kiểm tra nếu đã đăng nhập và cột role là số 1 (Admin) thì cho đi tiếp
        if (Auth::check() && (int)Auth::user()->role === 1) {
            return $next($request);
        }

        // Nếu không phải admin, chặn lại trả về lỗi 403 (Cực kỳ quan trọng, phải nằm ngoài dấu ngoặc } ở trên)
        abort(403, 'Bạn không có quyền truy cập vào khu vực Quản trị!');
    }
}