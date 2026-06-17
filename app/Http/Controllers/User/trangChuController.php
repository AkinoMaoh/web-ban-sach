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
        $categories = categories::where('status', 1)->get();

        $products = products::where('status', 1)
            ->whereHas('author', function ($q) {
                $q->where('status', 1);
            })
            ->get();

        return view('User.index', compact(
            'products',
            'categories'
        ));
    }

    // Tìm kiếm sản phẩm
   public function search(Request $request)
    {
        $keyword = $request->keyword;

        $categories = categories::where('status', 1)->get();

        $products = products::where('name','like','%'.$keyword.'%')
            ->get();

        return view('User.index', compact(
            'products',
            'categories',
            'keyword'
        ));
    }
}
