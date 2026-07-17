<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 1. Hiển thị danh sách người dùng (có phân trang)
    public function index()
    {
        // Lấy danh sách user mới nhất, phân trang 10 user/trang
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    // 2. Xóa tài khoản người dùng
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();

        return back()->with('success', 'Xóa người dùng thành công!');
    }
}
