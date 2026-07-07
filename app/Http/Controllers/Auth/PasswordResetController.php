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
    // Hiển thị trang nhập Email / Gửi mã xác nhận ban đầu (Giữ nguyên cho router cũ nếu có dùng)
    public function showVerifyForm()
    {
        return view('auth.password-verify');
    }

    // 1. Xử lý tạo và gửi mã OTP ngẫu nhiên về email (Dùng cho Ajax trên Profile)
    public function sendOtp(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'msg' => 'Phiên đăng nhập đã hết hạn.']);
            }
            
            // Tạo mã OTP ngẫu nhiên gồm 6 chữ số
            $otp = rand(100000, 999999);
            
            // Đồng bộ đồng thời cả 2 cấu trúc Session cũ và mới để không làm lỗi tính năng khác
            session([
                'password_reset_otp' => $otp,
                'password_reset_expires_at' => now()->addMinutes(5),
                'otp_verified' => false
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

    // Kiểm tra mã OTP khớp hay sai (Giữ nguyên cho các router cũ)
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);
        $sessionOtp = session('password_reset_otp');
        $expiresAt = session('password_reset_expires_at');

        if (!$sessionOtp || !$expiresAt || now()->isAfter($expiresAt)) {
            return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn, vui lòng nhận mã mới!']);
        }

        if ((int)$request->otp === (int)$sessionOtp) {
            session(['otp_verified' => true]);
            session()->forget(['password_reset_otp', 'password_reset_expires_at']);
            return response()->json(['success' => true, 'redirect' => route('password.reset.fields')]);
        }
        return response()->json(['success' => false, 'message' => 'Mã xác thực nhập vào không chính xác!']);
    }

    // Hiển thị trang thiết lập mật khẩu mới (Giữ nguyên cho các router cũ)
    public function showResetFieldsForm()
    {
        if (!session('otp_verified')) {
            return redirect()->route('password.verify.form')->with('error', 'Bạn cần xác thực mã OTP trước!');
        }
        return view('auth.password-reset-fields');
    }

    // 2. Xử lý cập nhật chính thức mật khẩu từ Form Profile gửi lên
    public function updatePassword(Request $request)
    {
        // Xác thực dữ liệu form gửi lên
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'otp_code' => 'required|numeric',
        ], [
            'password.required' => 'Vui lòng điền mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có độ dài ít nhất từ 8 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận lại không trùng khớp.',
            'otp_code.required' => 'Vui lòng nhập mã OTP xác nhận.',
            'otp_code.numeric' => 'Mã OTP phải là một dãy số.',
        ]);

        $sessionOtp = session('password_reset_otp');
        $expiresAt = session('password_reset_expires_at');

        // Kiểm tra tính hiệu lực OTP
        if (!$sessionOtp || !$expiresAt || now()->isAfter($expiresAt)) {
            return back()->withErrors(['otp_code' => 'Mã OTP đã hết hạn hoặc không tồn tại, vui lòng lấy mã mới!']);
        }

        // So khớp mã OTP người dùng điền với Session mã đã gửi
        if ((int)$request->otp_code !== (int)$sessionOtp) {
            return back()->withErrors(['otp_code' => 'Mã xác thực OTP không chính xác!']);
        }

        // Hợp lệ tiến hành đổi mật khẩu cho User đang đăng nhập
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa sạch session OTP để bảo mật tránh dùng lại
        session()->forget(['password_reset_otp', 'password_reset_expires_at', 'otp_verified']);

        return back()->with('success', 'Đổi mật khẩu tài khoản thành công!');
    }
}