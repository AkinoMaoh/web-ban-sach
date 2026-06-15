<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\NhaXuatBan;

class trangChuController extends Controller
{
    public function index()
    {
        $products = products::where('status', 1)->get();
        $categories = categories::all();
        $authors = authors::where('status', 1)->get();
        return view('User.index', compact('products', 'categories', 'authors'));
    }
}
