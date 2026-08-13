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
use App\Models\Variant;

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

        // Lấy các biến thể đang hoạt động
        $variants = Variant::where('status', 1)->get();

        return view('admin.productAdd', compact(
            'categories',
            'publishers',
            'authors',
            'variants'
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

            'variants.*.variant_id' => 'required|integer|exists:variants,id',

            'variants.*.price' => 'required|numeric|min:0',

            'variants.*.sale_price' => 'nullable|numeric|min:0',

            'variants.*.stock' => 'required|integer|min:0',

        ], [

            // ==========================
            // SẢN PHẨM
            // ==========================

            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là dạng văn bản.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không tồn tại.',

            'publisher_id.required' => 'Vui lòng chọn nhà xuất bản.',
            'publisher_id.exists' => 'Nhà xuất bản không tồn tại.',

            'author_id.required' => 'Vui lòng chọn tác giả.',
            'author_id.exists' => 'Tác giả không tồn tại.',

            'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
            'description.string' => 'Mô tả sản phẩm không hợp lệ.',

            // ==========================
            // HÌNH ẢNH
            // ==========================

            'images.array' => 'Danh sách hình ảnh không hợp lệ.',
            'images.max' => 'Sản phẩm chỉ được tối đa 7 ảnh.',

            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng JPEG, PNG, JPG, GIF hoặc WEBP.',
            'images.*.max' => 'Mỗi hình ảnh không được vượt quá 2MB.',

            // ==========================
            // BIẾN THỂ
            // ==========================

            'variants.required' => 'Vui lòng thêm ít nhất một biến thể.',
            'variants.array' => 'Danh sách biến thể không hợp lệ.',
            'variants.min' => 'Sản phẩm phải có ít nhất một biến thể.',

            'variants.*.variant_id.required' => 'Vui lòng chọn phiên bản.',
            'variants.*.variant_id.integer' => 'Phiên bản không hợp lệ.',
            'variants.*.variant_id.exists' => 'Phiên bản được chọn không tồn tại.',

            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.price.numeric' => 'Giá phải là số.',
            'variants.*.price.min' => 'Giá không được nhỏ hơn 0.',

            'variants.*.sale_price.numeric' => 'Giá giảm phải là số.',
            'variants.*.sale_price.min' => 'Giá giảm không được nhỏ hơn 0.',

            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
            'variants.*.stock.integer' => 'Số lượng phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng không được nhỏ hơn 0.',
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

        // Tạm thời để 0
        $product->price = 0;

        $product->save();


        // ======================
        // 2. CREATE IMAGES
        // ======================

        if ($request->hasFile('images')) {

            $thumbnail = null;

            foreach ($request->file('images') as $index => $image) {

                $imageName = uniqid()
                    . '_'
                    . time()
                    . '.'
                    . $image->getClientOriginalExtension();

                $image->move(
                    public_path('uploads/products'),
                    $imageName
                );

                // Ảnh đầu tiên là ảnh đại diện
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

            $product->image = $thumbnail;
        }


        // ======================
        // 3. CREATE VARIANTS
        // ======================

        $standardPrice = 0;

        foreach ($request->variants as $data) {

            $price = $data['price'];

            $salePrice = $data['sale_price'] ?? 0;


            // Không nhập hoặc giá giảm không hợp lệ
            if (
                empty($salePrice) ||
                $salePrice <= 0 ||
                $salePrice >= $price
            ) {

                $salePrice = null;

                $discount = 0;
            } else {

                $discount = round(
                    (($price - $salePrice) / $price) * 100
                );
            }


            $variant = new ProductVariants();

            $variant->product_id = $product->id;

            // Lưu ID từ bảng variants
            $variant->variant_id = $data['variant_id'];

            $variant->price = $price;

            $variant->sale_price = $salePrice;

            $variant->discount_percent = $discount;

            $variant->stock = $data['stock'];

            $variant->save();


            // Lấy giá Standard làm giá chính của sản phẩm
            $variantInfo = Variant::find($data['variant_id']);

            if (
                $variantInfo &&
                $variantInfo->name === 'Standard'
            ) {
                $standardPrice = $price;
            }
        }


        // ======================
        // 4. UPDATE PRODUCT SUMMARY
        // ======================

        $product->price = $standardPrice;

        $totalStock = ProductVariants::where(
            'product_id',
            $product->id
        )->sum('stock');

        $product->save();


        // ======================
        // 5. THÔNG BÁO
        // ======================

        return redirect()
            ->route('admin.products')
            ->with('success', 'Tạo sản phẩm thành công.');
    }

    public function edit($id)
    {
        $product = Products::with('images')->findOrFail($id);

        $categories = Categories::all();
        $authors = Authors::all();
        $publishers = Publishers::all();

        // Lấy các biến thể đang có của sản phẩm
        $productVariants = ProductVariants::with('variant')
            ->where('product_id', $product->id)
            ->get();

        // Lấy danh sách biến thể đang hoạt động
        $variants = Variant::where('status', 1)->get();

        return view('admin.productEdit', compact(
            'product',
            'categories',
            'authors',
            'publishers',
            'productVariants',
            'variants'
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

            'variants.*.id' => [
                'nullable',
                'integer',
                'exists:product_variants,id'
            ],

            'variants.*.variant_id' => [
                'required',
                'integer',
                'exists:variants,id'
            ],

            'variants.*.price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'variants.*.sale_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'variants.*.stock' => [
                'required',
                'integer',
                'min:0'
            ],

        ], [

            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là dạng văn bản.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không tồn tại.',

            'publisher_id.required' => 'Vui lòng chọn nhà xuất bản.',
            'publisher_id.exists' => 'Nhà xuất bản không tồn tại.',

            'author_id.required' => 'Vui lòng chọn tác giả.',
            'author_id.exists' => 'Tác giả không tồn tại.',

            'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
            'description.string' => 'Mô tả sản phẩm không hợp lệ.',

            'images.array' => 'Danh sách hình ảnh không hợp lệ.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng JPEG, PNG, JPG, GIF hoặc WEBP.',
            'images.*.max' => 'Mỗi hình ảnh không được vượt quá 2MB.',

            'variants.required' => 'Vui lòng thêm ít nhất một biến thể.',
            'variants.array' => 'Danh sách biến thể không hợp lệ.',
            'variants.min' => 'Sản phẩm phải có ít nhất một biến thể.',

            'variants.*.id.exists' => 'Biến thể sản phẩm không tồn tại.',

            'variants.*.variant_id.required' => 'Vui lòng chọn biến thể.',
            'variants.*.variant_id.exists' => 'Biến thể được chọn không tồn tại.',

            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.price.numeric' => 'Giá phải là số.',
            'variants.*.price.min' => 'Giá không được nhỏ hơn 0.',

            'variants.*.sale_price.numeric' => 'Giá giảm phải là số.',
            'variants.*.sale_price.min' => 'Giá giảm không được nhỏ hơn 0.',

            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
            'variants.*.stock.integer' => 'Số lượng phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng không được nhỏ hơn 0.',
        ]);


        $product = Products::findOrFail($id);


        // =====================================================
        // KIỂM TRA SỐ LƯỢNG ẢNH
        // =====================================================

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


        // =====================================================
        // CẬP NHẬT THÔNG TIN SẢN PHẨM
        // =====================================================

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->description = $request->description;


        // =====================================================
        // THÊM ẢNH MỚI
        // =====================================================

        if ($request->hasFile('images')) {

            $sortOrder = ProductImage::where('product_id', $product->id)
                ->max('sort_order');

            $sortOrder = is_null($sortOrder)
                ? 0
                : $sortOrder + 1;

            foreach ($request->file('images') as $image) {

                $imageName = uniqid() . '_'
                    . time() . '.'
                    . $image->getClientOriginalExtension();

                $image->move(
                    public_path('uploads/products'),
                    $imageName
                );

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                    'is_primary' => false,
                    'sort_order' => $sortOrder++,
                ]);

                // Nếu sản phẩm chưa có ảnh đại diện
                if (empty($product->image)) {
                    $product->image = $imageName;
                }
            }
        }


        $product->save();


        // =====================================================
        // CẬP NHẬT BIẾN THỂ
        // =====================================================

        $standardPrice = 0;

        $submittedVariantIds = [];

        // Lấy toàn bộ biến thể một lần
        $allVariants = Variant::whereIn(
            'id',
            collect($request->variants)->pluck('variant_id')->unique()
        )->get()->keyBy('id');


        // Kiểm tra biến thể bị chọn trùng
        $variantIds = collect($request->variants)
            ->pluck('variant_id')
            ->filter();

        if ($variantIds->count() !== $variantIds->unique()->count()) {

            return back()
                ->withErrors([
                    'variants' => 'Không được chọn trùng phiên bản cho cùng một sản phẩm.'
                ])
                ->withInput();
        }


        foreach ($request->variants as $data) {

            $price = $data['price'];

            // ================================================
            // TÍNH GIÁ GIẢM
            // ================================================

            $salePrice = !empty($data['sale_price'])
                ? $data['sale_price']
                : null;


            if (
                is_null($salePrice) ||
                $salePrice <= 0 ||
                $salePrice >= $price
            ) {

                $salePrice = null;

                $discount = 0;
            } else {

                $discount = round(
                    (($price - $salePrice) / $price) * 100
                );
            }


            // ================================================
            // DỮ LIỆU LƯU
            // ================================================

            $variantData = [

                'product_id' => $id,

                'variant_id' => $data['variant_id'],

                'price' => $price,

                'sale_price' => $salePrice,

                'discount_percent' => $discount,

                'stock' => $data['stock'],
            ];


            // ================================================
            // CẬP NHẬT BIẾN THỂ CŨ
            // ================================================

            if (!empty($data['id'])) {

                $variant = ProductVariants::where('id', $data['id'])
                    ->where('product_id', $id)
                    ->first();

                if ($variant) {

                    $variant->update($variantData);

                    $submittedVariantIds[] = $variant->id;
                } else {

                    $newVariant = ProductVariants::create($variantData);

                    $submittedVariantIds[] = $newVariant->id;
                }
            }

            // ================================================
            // THÊM BIẾN THỂ MỚI
            // ================================================

            else {

                $newVariant = ProductVariants::create($variantData);

                $submittedVariantIds[] = $newVariant->id;
            }


            // ================================================
            // LẤY GIÁ STANDARD
            // ================================================

            $variantInfo = $allVariants->get($data['variant_id']);

            if ($variantInfo && $variantInfo->name === 'Standard') {

                $standardPrice = $price;
            }
        }


        // =====================================================
        // XÓA BIẾN THỂ ĐÃ BỊ XÓA KHỎI FORM
        // =====================================================

        ProductVariants::where('product_id', $id)
            ->whereNotIn('id', $submittedVariantIds)
            ->delete();


        // =====================================================
        // CẬP NHẬT GIÁ CHÍNH CỦA SẢN PHẨM
        // =====================================================

        $product->price = $standardPrice;


        // =====================================================
        // CẬP NHẬT GIÁ STANDARD CHO PRODUCTS
        // =====================================================

        $product->price = $standardPrice;

        $product->save();


        // =====================================================
        // THÔNG BÁO THÀNH CÔNG
        // =====================================================

        return redirect()
            ->route('admin.products')
            ->with('success', 'Cập nhật sản phẩm thành công.');
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

        $productVariants = ProductVariants::with('variant')
            ->where('product_id', $id)
            ->get();

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
