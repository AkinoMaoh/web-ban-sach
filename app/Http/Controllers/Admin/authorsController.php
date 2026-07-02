<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\authors;
use App\Models\products;
use Illuminate\Http\Request;

class authorsController extends Controller
{
    public function index()
    {
        $authors = authors::withCount('products')->paginate(8);
        return view('admin.author', compact('authors'));
    }
    public function authorCreate()
    {
        return view('admin.authorAdd');
    }
    public function authorStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $author = new authors();
        $author->name = $request->name;
        $author->bio = $request->bio;
        $author->status = true; // Mặc định nhà xuất bản mới sẽ có trạng thái kích hoạt

        if ($request->hasFile('avatar')) {
            $avatarName = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $request->file('avatar')->move(public_path('uploads/authors'), $avatarName);
            $author->avatar = $avatarName;
        }

        $author->save();

        return redirect()->route('admin.authors')->with('success', 'Nhà xuất bản đã được thêm thành công.');
    }
    public function authorEdit($id)
    {
        $author = authors::findOrFail($id);
        return view('admin.authorEdit', compact('author'));
    }
    public function authorUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $author = authors::findOrFail($id);
        $author->name = $request->name;
        $author->bio = $request->bio;
        $author->status = $request->status ?? false;

        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($author->avatar && file_exists(public_path('uploads/authors/' . $author->avatar))) {
                unlink(public_path('uploads/authors/' . $author->avatar));
            }

            $avatarName = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $request->file('avatar')->move(public_path('uploads/authors'), $avatarName);
            $author->avatar = $avatarName;
        }

        $author->save();

        return redirect()->route('admin.authors')->with('success', 'Nhà xuất bản đã được cập nhật thành công.');
    }
    public function authorDestroy($id)
    {
        $author = authors::findOrFail($id);

        // Kiểm tra tác giả có sản phẩm không
        if ($author->products()->exists()) {
            return redirect()->route('admin.authors')
                ->with('error', 'Không thể xóa tác giả vì vẫn còn sản phẩm thuộc tác giả này.');
        }

        // Xóa ảnh nếu có
        if ($author->avatar && file_exists(public_path('uploads/authors/' . $author->avatar))) {
            unlink(public_path('uploads/authors/' . $author->avatar));
        }

        $author->delete();

        return redirect()->route('admin.authors')
            ->with('success', 'Tác giả đã được xóa thành công.');
    }
    public function authorToggleStatus($id)
    {
        $author = authors::findOrFail($id);
        $author->status = !$author->status;
        $author->save();
        products::where('author_id', $author->id)
            ->update(['status' => $author->status]);
        return redirect()->route('admin.authors')->with('success', 'Trạng thái nhà xuất bản đã được cập nhật thành công.');
    }
    public function authorShow($id)
    {
        $author = authors::findOrFail($id);
        return view('admin.authorShow', compact('author'));
    }
}
