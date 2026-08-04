<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification;

class AdminNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'like', $request->keyword . '%');
        }

        $news = $query->paginate(5);

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
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_featured' => 'nullable|boolean'
        ]);

        $news = new News();

        $news->title = $request->title;
        $news->summary = $request->summary;
        $news->content = $request->content;
        $news->status = $request->status;
        $news->is_featured = $request->has('is_featured');

        // Upload ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/news'), $imageName);
            $news->image = $imageName;
        }
        
        $news->save();

        $userIds = User::where('role', '!=', 1)->pluck('id');
        
        $notifications = [];
        $now = now(); 
        
        // 2. Gom dữ liệu vào 1 mảng
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id'    => $userId,
                'message'    => "Đã có bài viết mới: " . $news->title,
                'is_read'    => false,
                'target_url' => route('user.news.show', $news->id),
                'created_at' => $now, 
                'updated_at' => $now, 
            ];
        }

        foreach (array_chunk($notifications, 1000) as $chunk) {
            Notification::insert($chunk);
        }

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
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_featured' => 'nullable|boolean'
        ]);

        $news = News::findOrFail($id);

        $news->title = $request->title;
        $news->summary = $request->summary;
        $news->content = $request->content;
        $news->status = $request->status;
        $news->is_featured = $request->has('is_featured');

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

        // 1. Xóa ảnh nếu có
        if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
            unlink(public_path('uploads/news/' . $news->image));
        }

        $targetUrl = route('user.news.show', $news->id);
        Notification::where('target_url', $targetUrl)->delete();

        // 3. Xóa bài viết
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Xóa tin tức thành công');
    }
    // Tìm kiếm tin tức ajax
    public function search(Request $request)
    {
        $news = News::where('title', 'like', $request->keyword . '%')
            ->limit(5)
            ->get();

        return response()->json($news);
    }
}