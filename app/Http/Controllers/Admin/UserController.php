<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index()
    {
        // Chặn Nhân viên (role != 1) truy cập
        if (Auth::check() && Auth::user()->role != 1) {
            return redirect()->route('admin.products')->with('error', 'Bạn không có quyền truy cập trang Quản lý người dùng!');
        }

        $users = User::where('role', 0)
            ->latest()
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    /**
     * Xem chi tiết người dùng
     */
    public function show($id)
    {
        // Chặn Nhân viên (role != 1) truy cập
        if (Auth::check() && Auth::user()->role != 1) {
            return redirect()->route('admin.products')->with('error', 'Bạn không có quyền truy cập trang Quản lý người dùng!');
        }

        $user = User::findOrFail($id);

        $orders = $user->orders()
            ->latest()
            ->paginate(10);

        return view('admin.userShow', compact('user', 'orders'));
    }

    /**
     * Xóa người dùng
     */
    public function destroy(int $id)
    {
        // Chặn Nhân viên (role != 1) thực hiện thao tác xóa
        if (Auth::check() && Auth::user()->role != 1) {
            return redirect()->route('admin.products')->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $user = User::findOrFail($id);

        // Không cho tự xóa chính mình
        if ($user->id == auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Xóa người dùng thành công!');
    }
}
