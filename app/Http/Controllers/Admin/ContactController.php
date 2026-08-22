<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    // Hiển thị danh sách liên hệ
    public function index()
    {
        // Lấy danh sách liên hệ, sắp xếp mới nhất lên đầu, phân trang 10 dòng/trang
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.contact', compact('contacts'));
    }

        public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contactshow', compact('contact'));
    }

    // Đánh dấu đã xử lý / chưa xử lý
    public function updateStatus($id)
    {
        $contact = Contact::findOrFail($id);
        // Đảo ngược trạng thái: 0 thành 1, 1 thành 0
        $contact->status = $contact->status == 0 ? 1 : 0; 
        $contact->save();

        return back()->with('success', 'Đã cập nhật trạng thái liên hệ!');
    }

    // Xóa liên hệ
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Đã xóa liên hệ thành công!');
    }
}