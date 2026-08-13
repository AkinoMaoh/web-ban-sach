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
        return view('admin.vouchers', compact('vouchers'));
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
            'code' => 'required|unique:vouchers,code|max:50',
            'type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
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