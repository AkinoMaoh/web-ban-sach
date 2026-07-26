<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    private function getShopBanners()
    {
        return Banner::where('status', 1)
            ->where('position', 'category')
            ->orderBy('sort_order')
            ->get();
    }
    
    public function index(Request $request)
    {
        $tatCaDanhMuc = categories::where('status', 1)->get();
        $tacGia       = authors::all();
        $nhaXuatBan   = publishers::all();
        $banners      = $this->getShopBanners();

        // XỬ LÝ TỪ KHÓA TÌM KIẾM TRỌN GÓI TRONG CONTROLLER
        if ($request->filled('keyword')) {
            $keyword = mb_strtolower(trim($request->keyword), 'UTF-8');
            
            $truyVan = products::with(['author', 'firstVariant', 'category'])
                ->where('status', 1)
                ->where(function($query) use ($keyword) {
                    $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . $keyword . '%')
                          ->orWhereHas('category', function($q) use ($keyword) {
                              $q->where(DB::raw('LOWER(name)'), 'LIKE', '%' . $keyword . '%');
                          })
                          ->orWhereHas('author', function($q) use ($keyword) {
                              $q->where(DB::raw('LOWER(name)'), 'LIKE', '%' . $keyword . '%');
                          });
                });

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
                case 'price_asc': $truyVan->orderBy('price', 'asc'); break;
                case 'price_desc': $truyVan->orderBy('price', 'desc'); break;
                case 'newest': $truyVan->latest(); break;
                default: $truyVan->orderBy('id', 'desc'); break;
            }

            $danhSachSanPham = $truyVan->paginate(12)->appends($request->query());

            return view('User.shop', compact(
                'tatCaDanhMuc',
                'tacGia',
                'nhaXuatBan',
                'banners',
                'danhSachSanPham',
                'keyword'
            ));
        }

        // MẶC ĐỊNH: Hiển thị sản phẩm theo từng danh mục
        $sanPhamTheoDanhMuc = $tatCaDanhMuc->map(function ($dm) {
            $dm->sanPham = products::where('status', 1)
                ->where('category_id', $dm->id)
                ->orderBy('id', 'asc')
                ->take(6)
                ->get();
            return $dm;
        })->filter(fn($dm) => $dm->sanPham->isNotEmpty());
        
        return view('User.shop', compact(
            'tatCaDanhMuc',
            'sanPhamTheoDanhMuc',
            'tacGia',
            'nhaXuatBan',
            'banners'
        ));
    }

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
            case 'price_asc': $truyVan->orderBy('price', 'asc'); break;
            case 'price_desc': $truyVan->orderBy('price', 'desc'); break;
            case 'newest': $truyVan->latest(); break;
            default: $truyVan->orderBy('id', 'asc'); break;
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