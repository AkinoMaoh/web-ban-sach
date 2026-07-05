<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware // CHÍNH XÁC PHẢI LÀ USERMIDDLEWARE
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Nếu tài khoản mang quyền ADMIN (role == 1) cố tình vào các trang của USER thường
        if (Auth::check() && (int)Auth::user()->role === 1) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản Admin không thể truy cập giao diện hoặc tính năng của Người dùng!');
        }

        return $next($request);
    }
}