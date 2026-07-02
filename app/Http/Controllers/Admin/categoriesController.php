<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\categories;
use Illuminate\Support\Str;

class categoriesController extends Controller
{
    public function index()
    {
        $categories = categories::withCount('products')->paginate(8);
        $categories->loadCount('products');
        return view('admin.categories', compact('categories'));
    }
    public function create()
    {
        return view('admin.categoryAdd');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $category = new categories();
        $category->name = $request->name;

        if ($request->hasFile('image')) {

            // Tạo tên ảnh duy nhất
            $imageName = Str::uuid() . '.' . $request->file('image')->extension();

            // Lưu ảnh vào public/uploads/categories
            $request->file('image')->move(
                public_path('uploads/categories'),
                $imageName
            );

            // Lưu tên ảnh vào database
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories')
            ->with('success', 'Danh mục đã được thêm thành công!');
    }
    public function edit($id)
    {
        $category = categories::findOrFail($id);
        return view('admin.categoryEdit', compact('category'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $category = categories::findOrFail($id);

        $category->name = $request->name;

        if ($request->hasFile('image')) {

            // Xóa ảnh cũ nếu có
            if ($category->image && file_exists(public_path('uploads/categories/' . $category->image))) {
                unlink(public_path('uploads/categories/' . $category->image));
            }

            // Tạo tên ảnh mới
            $imageName = Str::uuid() . '.' . $request->file('image')->extension();

            // Lưu ảnh mới
            $request->file('image')->move(
                public_path('uploads/categories'),
                $imageName
            );

            // Cập nhật tên ảnh
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories')
            ->with('success', 'Danh mục đã được cập nhật thành công!');
    }
    public function destroy($id)
    {
        $category = categories::findOrFail($id);

        // Kiểm tra còn sản phẩm hay không
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm thuộc danh mục này!');
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Danh mục đã được xóa thành công!');
    }
    public function toggleStatus($id)
    {
        $category = categories::findOrFail($id);
        $category->status = !$category->status;
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Trạng thái danh mục đã được cập nhật thành công!');
    }
}
