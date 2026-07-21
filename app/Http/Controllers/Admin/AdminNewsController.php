<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::paginate(5);
        return view('admin.news', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.newsAdd');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $news = new News();

        $news->title = $request->title;
        $news->slug = $request->slug;
        $news->summary = $request->summary;
        $news->content = $request->content;
        $news->status = $request->status;

        // Upload ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/news'), $imageName);
            $news->image = $imageName;
        }
        $news->save();
        return redirect()
        ->route('admin.news.index')
        ->with('success', 'Thêm tin tức thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('admin.newsShow', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.newsEdit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $news = News::findOrFail($id);

        $news->title = $request->title;
        $news->slug = $request->slug;
        $news->summary = $request->summary;
        $news->content = $request->content;
        $news->status = $request->status;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/news'), $imageName);
            $news->image = $imageName;
        }

        $news->save();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Cập nhật tin tức thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Xóa ảnh nếu có
        if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
            unlink(public_path('uploads/news/' . $news->image));
        }

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Xóa tin tức thành công');
    }
}
