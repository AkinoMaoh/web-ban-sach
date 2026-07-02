<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ người dùng xịn.
     */
    public function edit(Request $request): View
    {
        // Sửa đường dẫn trỏ về file view giao diện mới tạo
        return view('User.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật thông tin hồ sơ và đổi mật khẩu.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Cập nhật Name và Email (chỉ lấy các trường đã qua validate)
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 2. Xử lý đổi mật khẩu nếu người dùng có nhập vào ô mật khẩu mới
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', 'min:8'],
            ], [
                'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
                'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            ]);

            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Trả về kèm session 'success' đúng với file Blade đang chờ hiển thị
        return Redirect::route('profile.edit')->with('success', 'Cập nhật thông tin hồ sơ tài khoản thành công!');
    }

    /**
     * Xóa tài khoản người dùng (Giữ nguyên mặc định).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}