<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Hiển thị giao diện thiết lập mật khẩu mới
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Xử lý lưu mật khẩu mới từ link reset email
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'token.required' => 'Mã token xác nhận không hợp lệ.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
        ]);

        // 2. Thực hiện đổi mật khẩu
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // 3. Nếu đổi mật khẩu thành công
        if ($status === Password::PASSWORD_RESET) {
            // Đăng xuất ngay lập tức để tránh middleware 'guest' đá về trang khác mà mất thông báo
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
        }

        // 4. Nếu thất bại, chuyển hướng kèm lỗi Tiếng Việt
        $errorMessage = match ($status) {
            Password::INVALID_TOKEN => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng lấy lại link mới.',
            Password::INVALID_USER => 'Không tìm thấy tài khoản tương ứng với địa chỉ email này.',
            default => 'Không thể đặt lại mật khẩu. Vui lòng thử lại.',
        };

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }
}
