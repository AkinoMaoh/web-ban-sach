<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\ProductImage;
use App\Models\publishers;
use App\Models\productVariants;

class productsController extends Controller
{
    public function index(Request $request)
    {
        $categories = categories::all();

        $query = products::with(
            'publishers',
            'author',
            'category',
            'firstVariant',
            'variants'
        );

        // Tìm theo tên sản phẩm
        if ($request->filled('keyword')) {
            $query->where('name', 'like', $request->keyword . '%');
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(8);

        return view('admin.products', compact('products', 'categories'));
    }
    public function create()
    {
        $categories = Categories::all();
        $publishers = Publishers::all();
        $authors = Authors::all();

        return view('admin.productAdd', compact(
            'categories',
            'publishers',
            'authors'
        ));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'description' => 'required|string',
            'images' => 'nullable|array|max:7',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.edition' => 'required|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|numeric|min:0',

        ]);

        // ======================
        // 1. CREATE PRODUCT
        // ======================
        $product = new Products();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->description = $request->description;
        $product->price = 0;
        $product->save();
        if ($request->hasFile('images')) {

            $thumbnail = null;

            foreach ($request->file('images') as $index => $image) {

                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/products'), $imageName);

                // Lưu ảnh đầu tiên làm ảnh đại diện
                if ($index === 0) {
                    $thumbnail = $imageName;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            // Cập nhật ảnh đại diện cho sản phẩm
            $product->image = $thumbnail;
        }
        // ======================
        // 2. CREATE VARIANTS
        // ======================

        $standardPrice = 0;

        foreach ($request->variants as $data) {

            $price = $data['price'];
            $salePrice = $data['sale_price'] ?? 0;

            // Không nhập hoặc nhập 0 => không giảm giá
            if (empty($salePrice) || $salePrice <= 0 || $salePrice >= $price) {
                $salePrice = null;
                $discount = 0;
            } else {
                $discount = round((($price - $salePrice) / $price) * 100);
            }

            $variant = new ProductVariants();
            $variant->product_id = $product->id;
            $variant->edition = $data['edition'];
            $variant->price = $price;
            $variant->sale_price = $salePrice;
            $variant->discount_percent = $discount;
            $variant->stock = $data['stock'];

            $variant->save();

            // Lấy giá của phiên bản đầu tiên làm giá hiển thị
            if ($standardPrice == 0) {
                $standardPrice = $data['price'];
            }
        }

        // ======================
        // 3. UPDATE PRODUCT SUMMARY
        // ======================


        $product->price = $standardPrice;
        $totalStock = ProductVariants::where('product_id', $product->id)->sum('stock');
        $product->save();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Tạo sản phẩm thành công');
    }

    public function edit($id)
    {
        $product = Products::with('images')->findOrFail($id);

        $categories = Categories::all();
        $authors = Authors::all();
        $publishers = Publishers::all();
        $productVariants = ProductVariants::where('product_id', $product->id)->get();

        return view('admin.productEdit', compact(
            'product',
            'categories',
            'authors',
            'publishers',
            'productVariants'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.edition' => 'required|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'variants.*.edition.required' => 'Vui lòng nhập tên phiên bản.',
            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.price.numeric' => 'Giá phải là số.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
            'variants.*.stock.integer' => 'Số lượng phải là số nguyên.',
        ]);

        $product = Products::findOrFail($id);


        // Kiểm tra tổng số ảnh hiện tại + ảnh mới
        $currentImages = $product->images()->count();

        $newImages = $request->hasFile('images')
            ? count($request->file('images'))
            : 0;


        if ($currentImages + $newImages > 7) {

            return back()
                ->withErrors([
                    'images' => "Sản phẩm chỉ được tối đa 7 ảnh. Hiện tại đang có {$currentImages} ảnh."
                ])
                ->withInput();
        }
        // Cập nhật thông tin sản phẩm
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->description = $request->description;

        if ($request->hasFile('images')) {

            // Lấy sort_order lớn nhất hiện có
            $sortOrder = ProductImage::where('product_id', $product->id)->max('sort_order');
            $sortOrder = is_null($sortOrder) ? 0 : $sortOrder + 1;

            foreach ($request->file('images') as $image) {

                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/products'), $imageName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                    'is_primary' => false,
                    'sort_order' => $sortOrder++,
                ]);

                // Nếu sản phẩm chưa có ảnh đại diện thì lấy ảnh đầu tiên
                if (empty($product->image)) {
                    $product->image = $imageName;
                }
            }
        }

        $product->save();

        $standardPrice = 0;
        $submittedVariantIds = [];

        foreach ($request->variants as $data) {

            $price = $data['price'];

            // Nếu không nhập sale_price thì lấy bằng giá gốc
            $salePrice = !empty($data['sale_price'])
                ? $data['sale_price']
                : $price;

            // Không cho sale_price lớn hơn hoặc bằng giá gốc
            if ($salePrice >= $price) {
                $salePrice = $price;
                $discount = 0;
            } else {
                $discount = round((($price - $salePrice) / $price) * 100);
            }

            $variantData = [
                'product_id'        => $id,
                'edition'           => $data['edition'],
                'price'             => $price,
                'sale_price'        => $salePrice,
                'discount_percent'  => $discount,
                'stock'             => $data['stock'],
            ];

            if (!empty($data['id'])) {
                // Variant đã tồn tại -> cập nhật, giữ nguyên ID
                $variant = ProductVariants::where('id', $data['id'])
                    ->where('product_id', $id)
                    ->first();

                if ($variant) {
                    $variant->update($variantData);
                    $submittedVariantIds[] = $variant->id;
                } else {
                    // ID gửi lên không khớp sản phẩm này -> tạo mới cho an toàn
                    $newVariant = ProductVariants::create($variantData);
                    $submittedVariantIds[] = $newVariant->id;
                }
            } else {
                // Variant mới -> tạo mới bình thường
                $newVariant = ProductVariants::create($variantData);
                $submittedVariantIds[] = $newVariant->id;
            }

            // Lấy đúng giá phiên bản Standard
            if ($data['edition'] == 'Standard') {
                $standardPrice = $price;
            }
        }

        // Xóa những variant cũ KHÔNG còn xuất hiện trong form nữa
        // (tức admin chủ động xóa phiên bản đó khỏi sản phẩm)
        ProductVariants::where('product_id', $id)
            ->whereNotIn('id', $submittedVariantIds)
            ->delete();

        $product->price = $standardPrice;
        $product->save();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy($id)
    {
        $product = products::findOrFail($id);
        $product->stock = productVariants::where(
            'product_id',
            $id
        )->sum('stock');
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $product = products::findOrFail($id);
        $product->status = $product->status == 1 ? 0 : 1;
        $totalStock = ProductVariants::where('product_id', $id)->sum('stock');
        $product->save();
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
    }
    public function show($id)
    {
        $product = Products::findOrFail($id);

        $productVariants = ProductVariants::where('product_id', $id)->get();

        $totalStock = $productVariants->sum('stock');

        return view('admin.productShow', compact(
            'product',
            'productVariants',
            'totalStock'

        ));
    }
    public function setPrimary($id)
    {
        $image = ProductImage::findOrFail($id);

        ProductImage::where('product_id', $image->product_id)
            ->update([
                'is_primary' => 0
            ]);

        $image->update([
            'is_primary' => 1
        ]);

        Products::where('id', $image->product_id)
            ->update([
                'image' => $image->image
            ]);

        return back()->with('success', 'Đã đổi ảnh đại diện.');
    }
    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);

        $path = public_path('uploads/products/' . $image->image);

        if (file_exists($path)) {
            unlink($path);
        }

        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        $image->delete();

        if ($wasPrimary) {

            $newPrimary = ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();

            if ($newPrimary) {

                $newPrimary->update([
                    'is_primary' => 1
                ]);

                Products::where('id', $productId)
                    ->update([
                        'image' => $newPrimary->image
                    ]);
            } else {

                Products::where('id', $productId)
                    ->update([
                        'image' => null
                    ]);
            }
        }

        return back();
    }
    public function sortImages(Request $request)
    {
        foreach ($request->images as $index => $id) {

            ProductImage::where('id', $id)
                ->update([
                    'sort_order' => $index
                ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
    // Tìm kiếm sản phẩm ajax
    public function search(Request $request)
    {
        $products = products::where('name', 'like', '%' . $request->keyword . '%')
            ->select('name')
            ->distinct()
            ->limit(5)
            ->get();

        return response()->json($products);
    }
}
