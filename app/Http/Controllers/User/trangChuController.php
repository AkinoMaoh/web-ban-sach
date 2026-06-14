<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\TheLoai;
use App\Models\TacGia;
use App\Models\NhaXuatBan;

class trangChuController extends Controller
{
    public function index()
    {
        $sach = products::all();
        return view('User/index', compact('sach'));
    }
}
