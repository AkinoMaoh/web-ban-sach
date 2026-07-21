<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Danh sách banner
     */
    public function index(Request $request)
    {
        $query = Banner::query();

        // Tìm kiếm theo tiêu đề
        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $banners = $query
            ->orderBy('sort_order')
            ->latest()
            ->paginate(8);

        return view('admin.banners', compact('banners'));
    }

    /**
     * Form thêm banner
     */
    public function create()
    {
        return view('admin.bannersAdd');
    }

    /**
     * Lưu banner
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|max:255',
            'description' => 'nullable',
            'position' => 'required',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ được phép có định dạng jpg, jpeg, png hoặc webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'position.required' => 'Vui lòng chọn vị trí hiển thị.',
            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị phải lớn hơn 0.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        // Xử lý thứ tự
        if ($request->filled('sort_order')) {

            if (Banner::where('sort_order', $request->sort_order)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'sort_order' => 'Thứ tự này đã tồn tại.'
                    ]);
            }

            $sortOrder = $request->sort_order;
        } else {

            $sortOrder = (Banner::max('sort_order') ?? 0) + 1;
        }

        // Upload ảnh
        $imageName = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $file = $request->file('image');

            $imageName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/banners'), $imageName);
        }

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'link' => $request->link,
            'position' => $request->position,
            'sort_order' => $sortOrder,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Thêm banner thành công.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);

        return view('admin.bannersEdit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|max:255',
            'description' => 'nullable',
            'position' => 'required',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ được phép có định dạng jpg, jpeg, png hoặc webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',

            'position.required' => 'Vui lòng chọn vị trí hiển thị.',


            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị phải lớn hơn 0.',

            'status.required' => 'Vui lòng chọn trạng thái.',

            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $sortOrder = $request->filled('sort_order')
            ? $request->sort_order
            : $banner->sort_order;

        if ($request->filled('sort_order')) {

            $exists = Banner::where('sort_order', $sortOrder)
                ->where('id', '!=', $banner->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'sort_order' => 'Thứ tự này đã tồn tại.'
                    ]);
            }
        }

        $imageName = $banner->image;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            if ($banner->image && file_exists(public_path('uploads/banners/' . $banner->image))) {
                unlink(public_path('uploads/banners/' . $banner->image));
            }

            $file = $request->file('image');

            $imageName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/banners'), $imageName);
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'link' => $request->link,
            'position' => $request->position,
            'sort_order' => $sortOrder,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Cập nhật banner thành công.');
    }

    /**
     * Xóa banner
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && file_exists(public_path('uploads/banners/' . $banner->image))) {
            unlink(public_path('uploads/banners/' . $banner->image));
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Xóa banner thành công.');
    }

    /**
     * Bật/Tắt trạng thái
     */
    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);

        $banner->status = !$banner->status;
        $banner->save();

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }
    // Xem chi tiết banner
    public function show($id)
    {
        $banner = Banner::findOrFail($id);

        return view('admin.bannersShow', compact('banner'));
    }
}
