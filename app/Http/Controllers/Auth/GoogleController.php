<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client; // BẮT BUỘC PHẢI THÊM DÒNG NÀY ĐỂ TẠO CUSTOM CLIENT
use Exception;

class GoogleController extends Controller
{
    // Điều hướng người dùng sang trang chọn tài khoản Google
    public function redirectToGoogle()
    {
        // Tạo một HTTP Client tạm thời bỏ qua kiểm tra SSL trên local
        $client = new Client(['verify' => false]);

        return Socialite::driver('google')
                        ->setHttpClient($client)
                        ->redirect();
    }

    // Nhận data Google trả về sau khi đăng nhập thành công
    public function handleGoogleCallback()
    {
        try {
            // Tạo HTTP Client bypass SSL để sử dụng lúc lấy thông tin User quay về
            $client = new Client([
                'verify' => false,
                'timeout' => 20
            ]);

            // Ép Socialite dùng Client này để gọi API của Google mà không bị chặn ở dòng dưới
            $googleUser = Socialite::driver('google')
                                    ->setHttpClient($client)
                                    ->stateless()
                                    ->user();
            
            // Tìm xem tài khoản này đã từng đăng nhập bằng Google chưa, hoặc có trùng email không
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Nếu chưa có google_id thì cập nhật vào (trường hợp tài khoản đăng ký thường trước đó)
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Auth::login($user, true); // Thêm true để hệ thống ghi nhớ phiên đăng nhập ổn định
            } else {
                // Nếu là khách hàng hoàn toàn mới -> Tự động đăng ký luôn thành viên
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => '0', // Đồng bộ đúng kiểu dữ liệu '0' đại diện cho người dùng thường
                    'is_active' => 1,
                ]);

                Auth::login($newUser, true);
            }

            // Ép buộc chuyển hướng thẳng về trang chủ, không sử dụng intended tránh lưu cache trang cũ
            return redirect('/'); 

        } catch (Exception $e) {
            // Trường hợp lỗi sẽ hiển thị thông báo lỗi cụ thể để dễ phân tích
            dd([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine()
            ]);
        }
    }
}