<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\TacGia;
use App\Models\NhaXuatBan;

class trangChuController extends Controller
{
    public function index()
    {
        $products = products::where('status', 1)->get();
        $categories = categories::all();
        return view('User.index', compact('products', 'categories'));
    }
}
