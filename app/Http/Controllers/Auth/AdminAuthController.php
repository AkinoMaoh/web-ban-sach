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
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    // 2. Xử lý Đăng Nhập Admin (Cập nhật chặn tài khoản chưa duyệt (0) hoặc đã khóa (2))
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // Kiểm tra xem có phải tài khoản Quản trị/Nhân viên không
            if ((int)Auth::user()->role === 1) {

                // Kiểm tra trạng thái tài khoản
                if ((int)Auth::user()->is_active === 0) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => 'Tài khoản Admin/Nhân viên của bạn đang chờ phê duyệt!',
                    ]);
                }

                if ((int)Auth::user()->is_active === 2) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Quản trị viên cấp cao!',
                    ]);
                }

                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            throw ValidationException::withMessages(['email' => 'Tài khoản này không có quyền Quản trị!']);
        }

        throw ValidationException::withMessages(['email' => __('auth.failed')]);
    }

    // 3. Hiển thị Form Đăng Ký Admin
    public function showRegisterForm()
    {
        return view('auth.admin-register');
    }

    // 4. Xử lý Đăng Ký Admin
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 1,
            'is_active' => 0, // Mặc định là 0 (Chờ duyệt)
        ]);

        return redirect()->route('admin.login')->with('status', 'Đăng ký Admin thành công! Vui lòng đợi Quản trị viên cấp cao phê duyệt tài khoản.');
    }

    // 5. Đăng xuất Admin
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ HỒ SƠ ADMIN
    |--------------------------------------------------------------------------
    */

    // 6. Hiển thị trang Hồ sơ Admin
    public function editProfile()
    {
        $admin = Auth::user();
        return view('Admin.profile', compact('admin'));
    }

    // 7. Xử lý Cập nhật Hồ sơ Admin
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã có người sử dụng trong hệ thống.',
            'password.min' => 'Mật khẩu mới phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Xác nhận lại mật khẩu mới không khớp.'
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin hồ sơ Admin thành công!');
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ TÀI KHOẢN NHÂN VIÊN/ADMIN KHÁC
    |--------------------------------------------------------------------------
    */

    // 8. Hiển thị danh sách Admin Chờ Duyệt và Các Admin Khác
    public function manageAdmins(Request $request)
    {
        $keyword = trim($request->input('keyword'));
        $currentUserId = Auth::id(); // Lấy ID của Super Admin đang đăng nhập

        // 1. Danh sách nhân viên/admin đang chờ duyệt (is_active = 0, loại trừ chính mình)
        $pendingAdmins = User::where('role', 1)
            ->where('id', '!=', $currentUserId)
            ->where('is_active', 0)
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%")
                        ->orWhere('phone', 'LIKE', "%{$keyword}%");
                });
            })
            ->get();

        // 2. Danh sách nhân viên/admin đã duyệt/khóa (is_active khác 0, loại trừ chính mình)
        $activeAdmins = User::where('role', 1)
            ->where('id', '!=', $currentUserId)
            ->where('is_active', '!=', 0)
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%")
                        ->orWhere('phone', 'LIKE', "%{$keyword}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['keyword' => $keyword]);

        return view('admin.manage-admins', compact('pendingAdmins', 'activeAdmins', 'keyword'));
    }

    /**
     * Gợi ý Autocomplete cho trang quản lý Admin/Nhân viên
     */
    public function autocompleteAdmins(Request $request)
    {
        $keyword = trim($request->input('query'));
        $currentUserId = Auth::id(); // Lấy ID tài khoản hiện tại để loại trừ

        if (empty($keyword)) {
            return response()->json([]);
        }

        // Lấy tối đa 5 admin/nhân viên khớp với từ khóa (không gồm tài khoản đang đăng nhập)
        $suggestions = User::where('role', 1)
            ->where('id', '!=', $currentUserId)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
            })
            ->select('id', 'name', 'email', 'phone', 'is_active as status')
            ->limit(5)
            ->get();

        return response()->json($suggestions);
    }

    // 9. Phê duyệt tài khoản Admin mới
    public function approveAdmin($id)
    {
        $admin = User::findOrFail($id);
        $admin->is_active = 1; // Duyệt tài khoản -> Chuyển thành 1 (Hoạt động)
        $admin->save();

        return redirect()->back()->with('success', 'Đã phê duyệt kích hoạt tài khoản cho Admin: ' . $admin->name);
    }

    // 10. Từ chối / Xóa yêu cầu đăng ký Admin
    public function rejectAdmin($id)
    {
        $admin = User::findOrFail($id);
        $adminName = $admin->name;
        $admin->delete();

        return redirect()->back()->with('success', 'Đã hủy bỏ và xóa yêu cầu đăng ký của: ' . $adminName);
    }

    // 11. BẬT / TẮT TRẠNG THÁI (KHÓA / MỞ KHÓA TÀI KHOẢN ADMIN)
    public function toggleStatus($id)
    {
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        $admin = User::findOrFail($id);

        // Ép kiểu int để so sánh chính xác 100%
        $currentStatus = (int) $admin->is_active;

        if ($currentStatus === 1) {
            $admin->is_active = 2; // Đang 1 -> Đổi thành 2 (Khóa)
            $msg = "Đã KHÓA tài khoản thành công!";
        } else {
            $admin->is_active = 1; // Đang 2 (hoặc khác 1) -> Đổi thành 1 (Mở khóa)
            $msg = "Đã MỞ KHÓA tài khoản thành công!";
        }

        $admin->save();

        return redirect()->back()->with('success', $msg);
    }
}
