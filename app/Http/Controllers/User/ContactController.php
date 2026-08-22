<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact; // Nhớ import Model Contact

class ContactController extends Controller
{
    // Hàm hiển thị giao diện Liên hệ
    public function index()
    {
        return view('User.contact'); // Lưu ý: đường dẫn view tuỳ thuộc vào cấu trúc thư mục của bạn
    }

    // Hàm xử lý khi người dùng bấm "Gửi tin nhắn"
    public function send(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Định dạng email không hợp lệ',
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn',
        ]);

        // 2. Lưu vào Database
        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // 3. Chuyển hướng và báo thành công
        return back()->with('success', 'Cảm ơn bạn! Tin nhắn đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất.');
    }
}