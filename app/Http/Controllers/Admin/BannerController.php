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
            ->paginate(10);

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
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|max:255',
            'description' => 'nullable',
            'position' => 'required',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
        }

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'link' => $request->link,
            'position' => $request->position,
            'sort_order' => $request->sort_order ?? 0,
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

        // validate ...

        $imageName = $banner->image;

        if ($request->hasFile('image')) {

            if ($banner->image && file_exists(public_path('uploads/banners/' . $banner->image))) {
                unlink(public_path('uploads/banners/' . $banner->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'link' => $request->link,
            'position' => $request->position,
            'sort_order' => $request->sort_order ?? 0,
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
