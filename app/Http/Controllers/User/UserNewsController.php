<?php

namespace App\Http\Controllers\User;

use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserNewsController extends Controller
{
    public function index()
    {
        $posts = News::where('status', 1)
                    ->orderBy('id', 'desc')
                    ->paginate(5);
        return view('user.news', compact('posts'));
    }

    public function show($id)
    {
        $post = News::findOrFail($id);
        return view('user.newsDetail', compact('post'));
    }
}