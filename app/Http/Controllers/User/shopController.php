<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;

class ShopController extends Controller
{
    private function getShopBanners()
    {
        return Banner::where('status', 1)
            ->where('position', 'category')
            ->orderBy('sort_order')
            ->get();
    }
    /**
     * Trang shop tổng – hiển thị sản phẩm theo từng danh mục
     */
    public function index(Request $request)
    {
        $tatCaDanhMuc = categories::where('status', 1)->get();
        $tacGia       = authors::all();
        $nhaXuatBan   = publishers::all();



        // Lấy sản phẩm nhóm theo danh mục (mỗi danh mục lấy tối đa 6)
        $sanPhamTheoDanhMuc = $tatCaDanhMuc->map(function ($dm) {
            $dm->sanPham = products::where('status', 1)
                ->where('category_id', $dm->id)
                ->orderBy('id', 'asc')
                ->take(6)
                ->get();
            return $dm;
        })->filter(fn($dm) => $dm->sanPham->isNotEmpty());
        $banners = $this->getShopBanners();
        return view('User.shop', compact(
            'tatCaDanhMuc',
            'sanPhamTheoDanhMuc',
            'tacGia',
            'nhaXuatBan',
            'banners'
        ));
    }

    /**
     * Trang shop theo danh mục – lọc, sắp xếp, phân trang
     */
    public function category(Request $request, $id)
    {
        $danhMuc      = categories::where('id', $id)->where('status', 1)->firstOrFail();
        $tatCaDanhMuc = categories::where('status', 1)->get();
        $tacGia       = authors::all();
        $nhaXuatBan   = publishers::all();

        $truyVan = products::where('status', 1)->where('category_id', $id);

        if ($request->filled('author')) {
            $truyVan->where('author_id', $request->author);
        }
        if ($request->filled('publisher')) {
            $truyVan->where('publisher_id', $request->publisher);
        }
        if ($request->filled('price_min')) {
            $truyVan->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $truyVan->where('price', '<=', $request->price_max);
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $truyVan->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $truyVan->orderBy('price', 'desc');
                break;
            case 'newest':
                $truyVan->latest();
                break;
            default:
                $truyVan->orderBy('id', 'asc');
                break;
        }

        $danhSachSanPham = $truyVan->paginate(12)->appends($request->query());
        $banners = $this->getShopBanners();
        return view('User.shop', compact(
            'tatCaDanhMuc',
            'tacGia',
            'nhaXuatBan',
            'danhMuc',
            'danhSachSanPham',
            'banners'
        ));
    }
    public function author($id)
    {
        $author = authors::findOrFail($id);

        $tatCaDanhMuc = categories::where('status', 1)->get();
        $tacGia = authors::all();
        $nhaXuatBan = publishers::all();

        $danhSachSanPham = products::where('author_id', $id)
            ->where('status', 1)
            ->paginate(12);
        $banners = $this->getShopBanners();
        return view('User.shop', compact(
            'tatCaDanhMuc',
            'tacGia',
            'nhaXuatBan',
            'author',
            'danhSachSanPham',
            'banners'
        ));
    }
}
