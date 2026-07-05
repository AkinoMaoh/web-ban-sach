<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Hàm hiển thị giao diện Liên hệ
    public function index()
    {
        return view('User.contact');
    }

    // Hàm xử lý khi người dùng bấm "Gửi tin nhắn"
    public function send(Request $request)
    {
        // Khúc này bạn có thể code thêm logic gửi mail hoặc lưu Database sau
        // Hiện tại chỉ cần redirect về và báo thành công
        return back()->with('success', 'Cảm ơn bạn! Tin nhắn đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất.');
    }
}