<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher; // Đảm bảo bạn đã có model Voucher

class VoucherController extends Controller
{
    // Hiển thị danh sách Voucher
    public function index()
    {
        // Lấy danh sách mới nhất, phân trang 10 dòng
        $vouchers = Voucher::orderBy('id', 'desc')->paginate(10);
        return view('admin.voucher', compact('vouchers'));
    }

    // Hiển thị form thêm mới
    public function create()
    {
        return view('admin.voucherAdd');
    }

    // Lưu dữ liệu thêm mới vào DB
    public function store(Request $request)
    {
        $request->validate([
            // ... các trường khác
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không đúng định dạng.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        Voucher::create($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Thêm mã giảm giá thành công!');
    }

    // Xóa Voucher
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}