<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductVariants;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $danhSachGioHang = [];
        if (Auth::check()) {
            $danhSachGioHang = Cart::with('productVariant.product.variants')->where('user_id', Auth::id())->get();
        } else {
            $gioHangSession = session()->get('cart', []);
            foreach ($gioHangSession as $maBienThe => $sanPham) {
                $bienThe = ProductVariants::with('product.variants')->find($maBienThe);
                if ($bienThe) {
                    $obj = new Cart();
                    $obj->id = $maBienThe;
                    $obj->product_variant_id = $maBienThe;
                    $obj->quantity = $sanPham['quantity'];
                    $obj->setRelation('productVariant', $bienThe);
                    $danhSachGioHang[] = $obj;
                }
            }
        }
        return view('User.cart', compact('danhSachGioHang'));
    }

    public function addToCart(Request $request)
    {
        $maBienThe = $request->input('product_variant_id');
        $soLuong = (int)$request->input('quantity', 1);

        $bienThe = ProductVariants::find($maBienThe);
        if (!$bienThe) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại!']);
            }
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        if (Auth::check()) {
            $sanPhamTrongGio = Cart::where('user_id', Auth::id())->where('product_variant_id', $maBienThe)->first();
            $soLuongHienTai = $sanPhamTrongGio ? $sanPhamTrongGio->quantity : 0;
        } else {
            $gioHang = session()->get('cart', []);
            $soLuongHienTai = $gioHang[$maBienThe]['quantity'] ?? 0;
        }

        if ($soLuongHienTai + $soLuong > $bienThe->stock) {
            $soLuongConLai = $bienThe->stock - $soLuongHienTai;
            $thongBao = $soLuongConLai > 0
                ? "Chỉ còn có thể thêm {$soLuongConLai} sản phẩm nữa (tồn kho: {$bienThe->stock})!"
                : "Sản phẩm đã đạt giới hạn tồn kho ({$bienThe->stock})!";

            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $thongBao]);
            }
            return back()->with('error', $thongBao);
        }

        if (Auth::check()) {
            $sanPhamTrongGio
                ? $sanPhamTrongGio->increment('quantity', $soLuong)
                : Cart::create([
                    'user_id' => Auth::id(),
                    'product_variant_id' => $maBienThe,
                    'quantity' => $soLuong
                ]);
        } else {
            $gioHang = session()->get('cart', []);
            $gioHang[$maBienThe]['quantity'] = $soLuongHienTai + $soLuong;
            session()->put('cart', $gioHang);
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!']);
        }
        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function updateCart(Request $request)
    {
        $danhSachSoLuong = $request->input('quantities', []);
        $danhSachBienThe = $request->input('variants', []);
        $danhSachLoi = [];

        if (Auth::check()) {
            // Track các cart ID đã bị merge vào (không được update lại bằng giá trị cũ từ form)
            $daBiMergeVao = [];
            // Track số lượng mới nhất theo cart_id trong request này (tránh đọc DB cũ)
            // key: cart_id, value: số lượng mới nhất
            $soLuongMoiNhat = [];

            foreach ($danhSachSoLuong as $id => $soLuong) {
                // Bỏ qua nếu cart này đã được merge từ cart khác vào
                if (in_array((int)$id, $daBiMergeVao)) continue;

                $soLuong = (int)$soLuong;
                $sanPhamTrongGio = Cart::where('user_id', Auth::id())->where('id', $id)->first();
                if (!$sanPhamTrongGio) continue;

                $maBienTheMoi = (int)($danhSachBienThe[$id] ?? $sanPhamTrongGio->product_variant_id);
                $maBienTheCu = (int)$sanPhamTrongGio->product_variant_id;
                $bienThe = ProductVariants::find($maBienTheMoi);

                // Kiểm tra số lượng không vượt stock
                if ($bienThe && $soLuong > $bienThe->stock) {
                    $danhSachLoi[] = "'{$bienThe->edition}' chỉ còn {$bienThe->stock} sản phẩm trong kho!";
                    $soLuong = $bienThe->stock;
                }

                if ($maBienTheMoi !== $maBienTheCu) {
                    $sanPhamTrungBienThe = Cart::where('user_id', Auth::id())
                                    ->where('product_variant_id', $maBienTheMoi)
                                    ->first();

                    if ($sanPhamTrungBienThe) {
                        // Dùng số lượng mới nhất trong request (nếu đã được update trước đó), không dùng DB cũ
                        $soLuongDich = $soLuongMoiNhat[$sanPhamTrungBienThe->id] ?? $sanPhamTrungBienThe->quantity;

                        // Không cho đổi nếu tổng số lượng sau merge vượt quá stock
                        if ($soLuongDich + $soLuong > $bienThe->stock) {
                            $conLai = $bienThe->stock - $soLuongDich;
                            $danhSachLoi[] = $conLai > 0
                                ? "Không thể đổi sang '{$bienThe->edition}' vì chỉ còn có thể thêm {$conLai} sản phẩm nữa (tồn kho: {$bienThe->stock})!"
                                : "Không thể đổi sang '{$bienThe->edition}' vì đã đạt giới hạn tồn kho ({$bienThe->stock})!";
                            continue;
                        }

                        $soLuongSauMerge = $soLuongDich + $soLuong;
                        $sanPhamTrungBienThe->update(['quantity' => $soLuongSauMerge]);
                        $sanPhamTrongGio->delete();

                        // Lưu lại số lượng mới nhất để các vòng lặp sau dùng
                        $soLuongMoiNhat[$sanPhamTrungBienThe->id] = $soLuongSauMerge;
                        // Đánh dấu cart ID đích đã được merge, không cho vòng lặp sau ghi đè
                        $daBiMergeVao[] = $sanPhamTrungBienThe->id;
                    } else {
                        $sanPhamTrongGio->update(['product_variant_id' => $maBienTheMoi, 'quantity' => $soLuong]);
                    }
                } else {
                    $sanPhamTrongGio->update(['quantity' => $soLuong]);
                    // Lưu lại số lượng mới nhất
                    $soLuongMoiNhat[(int)$id] = $soLuong;
                }
            }
        } else {
            $gioHang = session()->get('cart', []);
            // Track các key session đã bị merge vào
            $daBiMergeVao = [];

            foreach ($danhSachSoLuong as $id => $soLuong) {
                // Bỏ qua nếu key này đã được merge từ key khác vào
                if (in_array((int)$id, $daBiMergeVao)) continue;

                $soLuong = (int)$soLuong;
                $maBienTheMoi = (int)($danhSachBienThe[$id] ?? $id);
                $bienThe = ProductVariants::find($maBienTheMoi);

                if ($bienThe && $soLuong > $bienThe->stock) {
                    $danhSachLoi[] = "'{$bienThe->edition}' chỉ còn {$bienThe->stock} sản phẩm trong kho!";
                    $soLuong = $bienThe->stock;
                }

                if ($maBienTheMoi !== (int)$id) {
                    $soLuongHienTaiDich = $gioHang[$maBienTheMoi]['quantity'] ?? 0;

                    // Không cho đổi nếu tổng số lượng sau merge vượt quá stock
                    if ($bienThe && $soLuongHienTaiDich + $soLuong > $bienThe->stock) {
                        $conLai = $bienThe->stock - $soLuongHienTaiDich;
                        $danhSachLoi[] = $conLai > 0
                            ? "Không thể đổi sang '{$bienThe->edition}' vì chỉ còn có thể thêm {$conLai} sản phẩm nữa (tồn kho: {$bienThe->stock})!"
                            : "Không thể đổi sang '{$bienThe->edition}' vì đã đạt giới hạn tồn kho ({$bienThe->stock})!";
                        continue;
                    }

                    unset($gioHang[$id]);
                    $gioHang[$maBienTheMoi]['quantity'] = $soLuongHienTaiDich + $soLuong;

                    // Đánh dấu key đích đã được merge, không cho vòng lặp sau ghi đè
                    $daBiMergeVao[] = $maBienTheMoi;
                } else {
                    if (isset($gioHang[$id])) $gioHang[$id]['quantity'] = $soLuong;
                }
            }
            session()->put('cart', $gioHang);
        }

        if (!empty($danhSachLoi)) {
            return back()->with('warning', implode('<br>', $danhSachLoi));
        }
        return back()->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    public function remove($id)
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->where('id', $id)->delete();
        } else {
            $gioHang = session()->get('cart', []);
            unset($gioHang[$id]);
            session()->put('cart', $gioHang);
        }
        return back();
    }
}