<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Hiển thị giao diện gửi yêu cầu đặt lại mật khẩu (Quên mật khẩu)
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Xử lý gửi link đặt lại mật khẩu tới email người dùng
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Xác thực địa chỉ email đầu vào
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        // Thực hiện gửi link khôi phục mật khẩu từ Password Broker
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Trả về thông báo tương ứng với kết quả
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu vào email của bạn!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
