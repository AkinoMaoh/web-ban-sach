<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhoneLoginController extends Controller
{
    public function showForm() {
        return view('auth.login');
    }

    // 1. Nhận SĐT từ AJAX và phát sinh OTP ghi vào log
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
        ]);
        
        $phone = $request->phone;
        $otp = rand(100000, 999999); // Tạo mã 6 số ngẫu nhiên

        // Lưu tạm SĐT và OTP vào Session
        session(['login_phone' => $phone, 'login_otp' => $otp]);

        // Ghi mã OTP vào file log (storage/logs/laravel.log) để lập trình viên lấy test
        Log::info("Mã OTP đăng nhập của số $phone là: $otp");
        
        return response()->json([
            'success' => true, 
            'msg' => 'Mã OTP đã được gửi! Hãy mở file storage/logs/laravel.log để lấy mã kích hoạt.'
        ]);
    }

    // 2. Nhận OTP từ AJAX khách nhập để xác thực đăng nhập
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'phone' => 'required'
        ]);

        $sessionOtp = session('login_otp');
        $sessionPhone = session('login_phone');

        // Kiểm tra mã OTP khớp với session không
        if ($request->otp == $sessionOtp && $request->phone == $sessionPhone) {
            
            // Xóa session OTP ngay sau khi dùng xong để bảo mật
            session()->forget(['login_otp']);

            // Tìm xem SĐT này đã có tài khoản chưa
            $user = User::where('phone', $sessionPhone)->first();

            if (!$user) {
                // Chưa có -> Tự động tạo luôn tài khoản theo SĐT mới
                $user = User::create([
                    'name' => 'Khách hàng ' . substr($sessionPhone, -4),
                    'phone' => $sessionPhone,
                    'email' => $sessionPhone . '@localhost.com', // Tránh lỗi trường UNIQUE email trong DB nếu có
                    'password' => bcrypt(Str::random(16)),      // Mật khẩu ngẫu nhiên
                    'role' => 0,                                 // Quy ước user thường (phù hợp với các route khác của bạn)
                    'is_active' => 1
                ]);
            }

            // Tiến hành đăng nhập vào hệ thống
            Auth::login($user, true);

            return response()->json([
                'success' => true,
                'redirect' => route('user.index') // Trả về link trang chủ cho JS điều hướng
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mã OTP bạn nhập không chính xác hoặc đã hết hạn!'
        ], 422);
    }
}