<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class variantController extends Controller
{
    /**
     * =========================
     * DANH SÁCH BIẾN THỂ
     * =========================
     */
    public function index(Request $request)
    {
        $query = DB::table('variants')
            ->orderBy('id', 'desc');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', $request->keyword . '%');
        }

        $variants = $query->get()
            ->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                ];
            });

        return view('admin.variants', compact('variants'));
    }


    /**
     * =========================
     * THÊM BIẾN THỂ
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên biến thể.',
            'name.string'   => 'Tên biến thể không hợp lệ.',
            'name.max'      => 'Tên biến thể không được vượt quá 255 ký tự.',
        ]);


        // Kiểm tra trùng tên
        $exists = DB::table('variants')
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.variants')
                ->with('error', 'Tên biến thể này đã tồn tại.');
        }


        DB::table('variants')->insert([
            'name' => $request->name,
        ]);


        return redirect()
            ->route('admin.variants')
            ->with('success', 'Thêm biến thể thành công.');
    }


    /**
     * =========================
     * SỬA BIẾN THỂ
     * =========================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên biến thể.',
            'name.string'   => 'Tên biến thể không hợp lệ.',
            'name.max'      => 'Tên biến thể không được vượt quá 255 ký tự.',
        ]);


        // Kiểm tra biến thể có tồn tại không
        $variant = DB::table('variants')
            ->where('id', $id)
            ->first();

        if (!$variant) {
            return redirect()
                ->route('admin.variants')
                ->with('error', 'Không tìm thấy biến thể.');
        }


        // Kiểm tra trùng tên với biến thể khác
        $exists = DB::table('variants')
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.variants')
                ->with('error', 'Tên biến thể này đã tồn tại.');
        }


        DB::table('variants')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
            ]);


        return redirect()
            ->route('admin.variants')
            ->with('success', 'Cập nhật biến thể thành công.');
    }


    /**
     * =========================
     * XÓA BIẾN THỂ
     * =========================
     */
    public function destroy($id)
    {
        // Kiểm tra biến thể có tồn tại không
        $variant = DB::table('variants')
            ->where('id', $id)
            ->first();

        if (!$variant) {
            return redirect()
                ->route('admin.variants')
                ->with('error', 'Không tìm thấy biến thể.');
        }


        // Kiểm tra biến thể đang được sử dụng
        $used = DB::table('product_variants')
            ->where('variant_id', $id)
            ->exists();


        if ($used) {
            return redirect()
                ->route('admin.variants')
                ->with(
                    'error',
                    'Không thể xóa biến thể "' .
                        $variant->name .
                        '" vì biến thể này đang được sử dụng cho sản phẩm.'
                );
        }


        // Xóa
        DB::table('variants')
            ->where('id', $id)
            ->delete();


        return redirect()
            ->route('admin.variants')
            ->with('success', 'Xóa biến thể thành công.');
    }
    // Tìm kiếm ajax cho biến thể
    public function search(Request $request)
    {
        $variants = DB::table('variants')
            ->where('name', 'like', '%' . $request->keyword . '%')
            ->limit(5)
            ->get();

        return response()->json($variants);
    }
}
