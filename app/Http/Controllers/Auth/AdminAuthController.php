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

    /*
    |--------------------------------------------------------------------------
    | TÍNH NĂNG BỔ SUNG: QUẢN LÝ HỒ SƠ ADMIN
    |--------------------------------------------------------------------------
    */

    // 6. Hiển thị trang Hồ sơ Admin
    public function editProfile() {
        $admin = Auth::user(); // Lấy thông tin admin hiện tại đang đăng nhập
        return view('Admin.profile', compact('admin')); // Trỏ tới file view bạn sẽ tạo ở resources/views/Admin/profile.blade.php
    }

    // 7. Xử lý Cập nhật Hồ sơ Admin
    public function updateProfile(Request $request) {
        $admin = Auth::user();

        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            // Loại trừ kiểm tra unique email của chính id tài khoản admin này
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed', // Mật khẩu mới không bắt buộc điền
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã có người sử dụng trong hệ thống.',
            'password.min' => 'Mật khẩu mới phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Xác nhận lại mật khẩu mới không khớp.'
        ]);

        // Gán giá trị mới
        $admin->name = $request->name;
        $admin->email = $request->email;

        // Nếu admin điền mật khẩu mới thì tiến hành mã hóa Hash và thay đổi
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin hồ sơ Admin thành công!');
    }

    // Thêm hàm hiển thị danh sách và xử lý phê duyệt vào cuối class AdminAuthController

// 1. Hiển thị danh sách Admin Chờ Duyệt và Admin Đã Duyệt
public function manageAdmins()
{
    // Lấy ra danh sách admin đang chờ duyệt (is_active = 0)
    $pendingAdmins = User::where('role', 1)->where('is_active', 0)->get();

    // Lấy ra danh sách admin đã hoạt động (is_active = 1), loại trừ chính bản thân người đang đăng nhập
    $activeAdmins = User::where('role', 1)->where('is_active', 1)->where('id', '!=', Auth::id())->get();

    return view('Admin.manage-admins', compact('pendingAdmins', 'activeAdmins'));
}

// 2. Xử lý Duyệt tài khoản Admin mới
public function approveAdmin($id)
{
    $admin = User::findOrFail($id);
    
    // Đổi trạng thái từ 0 sang 1 để kích hoạt tài khoản
    $admin->is_active = 1;
    $admin->save();

    return redirect()->back()->with('success', 'Đã phê duyệt kích hoạt tài khoản cho Admin: ' . $admin->name);
}

// 3. Xử lý Từ chối / Xóa tài khoản Admin (Nếu thông tin đăng ký không hợp lệ)
public function rejectAdmin($id)
{
    $admin = User::findOrFail($id);
    $adminName = $admin->name;
    $admin->delete(); // Xóa tài khoản khỏi hệ thống

    return redirect()->back()->with('success', 'Đã hủy bỏ và xóa yêu cầu đăng ký của: ' . $adminName);
}
}