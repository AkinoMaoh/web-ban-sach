<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Hàm hiển thị giao diện Tin tức
    public function index()
    {
        return view('User.news');
    }
    public function show()
    {
        return view('User.news-detail');
    }


}