<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ người dùng.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // 1. Lấy danh sách toàn bộ Tỉnh/Thành phố
        $provinces = DB::table('provinces')->orderBy('name', 'asc')->get();

        // 2. Lấy địa chỉ của User từ bảng user_addresses
        $defaultAddress = DB::table('user_addresses')
            ->where('user_id', $user->id)
            ->first();

        return view('User.profile', compact('user', 'provinces', 'defaultAddress'));
    }

    /**
     * Cập nhật thông tin hồ sơ và địa chỉ.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Validate dữ liệu
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['required', 'regex:/^(0)[0-9]{9}$/'],
            'gender'           => ['nullable', 'in:male,female'], // Đã validate
            'province_id'      => ['required'],
            'district_id'      => ['required'],
            'ward_code'        => ['required'],
            'specific_address' => ['required', 'string', 'max:500'],
        ], [
            // Các câu thông báo lỗi...
        ]);

        // 2. Cập nhật vào bảng `users` (BỔ SUNG THÊM GENDER Ở ĐÂY)
        DB::table('users')->where('id', $user->id)->update([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'gender'     => $request->gender, // <--- Bổ sung dòng này
            'updated_at' => now(),
        ]);

        // 3. Cập nhật bảng `user_addresses`
        DB::table('user_addresses')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'receiver_name'    => $request->name,
                'receiver_phone'   => $request->phone,
                'province_id'      => $request->province_id,
                'district_id'      => $request->district_id,
                'ward_code'        => $request->ward_code,
                'specific_address' => $request->specific_address,
                'is_default'       => 1,
                'updated_at'       => now(),
            ]
        );

        return Redirect::route('profile.edit')->with('success', 'Cập nhật thông tin hồ sơ và địa chỉ thành công!');
    }

    /**
     * Xóa tài khoản người dùng.
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
