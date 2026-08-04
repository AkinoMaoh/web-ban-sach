<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index()
    {
        $users = User::where('role', 0)
            ->latest()
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    /**
     * Xóa người dùng
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        // Không cho tự xóa chính mình
        if ($user->id == auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Xóa người dùng thành công!');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.userShow', compact('user'));
    }
}
