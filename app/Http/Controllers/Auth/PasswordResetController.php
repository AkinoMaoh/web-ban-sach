<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class PasswordResetController extends Controller
{
    // 1. Xử lý tạo và gửi mã OTP ngẫu nhiên về email (Dùng cho Ajax gửi mã)
    public function sendOtp(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'msg' => 'Phiên đăng nhập đã hết hạn.']);
            }
            
            // Tạo mã OTP ngẫu nhiên gồm 6 chữ số
            $otp = rand(100000, 999999);
            
            // Lưu mã OTP và thời gian hết hạn (5 phút) vào Session
            session([
                'password_reset_otp' => $otp,
                'password_reset_expires_at' => now()->addMinutes(5),
            ]);

            // Gửi mail cho người dùng
            Mail::raw("Mã xác thực đổi mật khẩu của bạn là: $otp (Mã này có hiệu lực trong 5 phút).", function ($message) use ($user) {
                $message->to($user->email)->subject('[BookStore] Mã xác thực đổi mật khẩu');
            });

            return response()->json(['success' => true, 'msg' => 'Mã xác thực đã được gửi tới email của bạn!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Không thể gửi mail, vui lòng thử lại!']);
        }
    }

    // 2. Xử lý cập nhật chính thức mật khẩu trực tiếp trên cùng form Profile
    public function updatePassword(Request $request)
    {
        // Validate dữ liệu mật khẩu và trường OTP nhập lên
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'otp_code' => 'required|numeric',
        ], [
            'password.required' => 'Vui lòng điền mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có độ dài ít nhất từ 8 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận lại không trùng khớp.',
            'otp_code.required' => 'Vui lòng nhập mã OTP xác nhận.',
            'otp_code.numeric' => 'Mã OTP phải là chuỗi số.',
        ]);

        $sessionOtp = session('password_reset_otp');
        $expiresAt = session('password_reset_expires_at');

        // Kiểm tra tính hợp lệ của OTP trong Session
        if (!$sessionOtp || !$expiresAt || now()->isAfter($expiresAt)) {
            return back()->withErrors(['otp_code' => 'Mã OTP đã hết hạn, vui lòng nhận lại mã mới!']);
        }

        // Kiểm tra mã người dùng nhập so với mã hệ thống gửi
        if ((int)$request->otp_code !== (int)$sessionOtp) {
            return back()->withErrors(['otp_code' => 'Mã xác thực OTP nhập vào không chính xác!']);
        }

        // Đúng mã OTP -> Tiến hành cập nhật mật khẩu mới vào Database
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa mã OTP khỏi session sau khi dùng xong để bảo mật
        session()->forget(['password_reset_otp', 'password_reset_expires_at']);

        return back()->with('success', 'Đổi mật khẩu tài khoản thành công!');
    }
}