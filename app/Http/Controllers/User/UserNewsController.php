<?php

namespace App\Http\Controllers\User;

use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserNewsController extends Controller
{
    public function index()
    {
        // Danh sách tất cả tin
        $posts = News::where('status', 1)
                    ->orderBy('id', 'desc')
                    ->paginate(5);

        // Danh sách tin nổi bật
        $featuredPosts = News::where('status', 1)
                            ->where('is_featured', 1)
                            ->orderBy('id', 'desc')
                            ->take(5)
                            ->get();

        return view('user.news', compact('posts', 'featuredPosts'));
    }

    public function show($id)
    {
        $post = News::findOrFail($id);
        return view('user.newsDetail', compact('post'));
    }
}