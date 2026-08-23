<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng (Đã bổ sung Logic tìm kiếm)
     */
    public function index(Request $request)
    {
        // Chặn Nhân viên (role != 1) truy cập
        if (Auth::check() && Auth::user()->role != 1) {
            return redirect()->route('admin.products')->with('error', 'Bạn không có quyền truy cập trang Quản lý người dùng!');
        }

        $query = User::where('role', 0);

        // Lọc theo từ khóa tìm kiếm nếu có
        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
            });
        }

        $users = $query->latest()->paginate(10);

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

    /**
     * API Gợi ý Autocomplete
     */
    public function autocomplete(Request $request)
    {
        $keyword = trim($request->input('query'));

        if (empty($keyword)) {
            return response()->json([]);
        }

        // Lấy tối đa 5 khách hàng khớp với từ khóa
        $suggestions = User::where('role', 0)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
            })
            ->select('id', 'name', 'email', 'phone')
            ->limit(5)
            ->get();

        return response()->json($suggestions);
    }
}
