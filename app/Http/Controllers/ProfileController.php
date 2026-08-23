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

        // 1. Validate toàn bộ dữ liệu đầu vào
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['required', 'regex:/^(0)[0-9]{9}$/'], // Bắt buộc đúng 10 số VN
            'gender'           => ['nullable', 'in:male,female'],
            'province_id'      => ['required'],
            'district_id'      => ['required'],
            'ward_code'        => ['required'],
            'specific_address' => ['required', 'string', 'max:500'],
        ], [
            'name.required'             => 'Vui lòng nhập họ và tên.',
            'phone.required'            => 'Vui lòng nhập số điện thoại.',
            'phone.regex'               => 'Số điện thoại không hợp lệ (gồm 10 số bắt đầu bằng số 0).',
            'province_id.required'      => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required'      => 'Vui lòng chọn Quận/Huyện.',
            'ward_code.required'        => 'Vui lòng chọn Phường/Xã.',
            'specific_address.required' => 'Vui lòng nhập số nhà, tên đường cụ thể.',
        ]);

        // 2. Cập nhật thông tin vào bảng `users`
        DB::table('users')->where('id', $user->id)->update([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'updated_at' => now(),
        ]);

        // 3. Cập nhật hoặc Thêm mới vào bảng `user_addresses`
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
