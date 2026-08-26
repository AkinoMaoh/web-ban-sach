<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = products::with([
            'publishers',
            'author',
            'category',
            'firstVariant',
            'variants',
        ]);

        if ($request->filled('keyword')) {
            $query->where('name', 'like', $request->keyword . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(8);
        return view('admin.products', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = categories::all();
        $publishers = publishers::all();
        $authors = authors::all();
        $variants = Variant::where('status', 1)->get();

        return view('admin.productAdd', compact('categories', 'publishers', 'authors', 'variants'));
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
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'publisher_id.required' => 'Vui lòng chọn nhà xuất bản.',
            'author_id.required' => 'Vui lòng chọn tác giả.',
            'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
            'images.max' => 'Sản phẩm chỉ được tối đa 7 ảnh.',
            'variants.required' => 'Vui lòng thêm ít nhất một biến thể.',
            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Tạo sản phẩm
            $product = new products();
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->publisher_id = $request->publisher_id;
            $product->author_id = $request->author_id;
            $product->description = $request->description;
            $product->price = 0;
            $product->save();

            // 2. Tải ảnh lên
            if ($request->hasFile('images')) {
                $thumbnail = null;
                foreach ($request->file('images') as $index => $image) {
                    $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/products'), $imageName);

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

            // 3. Tạo biến thể
            $standardPrice = 0;
            foreach ($request->variants as $data) {
                $price = $data['price'];
                $salePrice = $data['sale_price'] ?? 0;

                if (empty($salePrice) || $salePrice <= 0 || $salePrice >= $price) {
                    $salePrice = null;
                    $discount = 0;
                } else {
                    $discount = round((($price - $salePrice) / $price) * 100);
                }

                productVariants::create([
                    'product_id' => $product->id,
                    'variant_id' => $data['variant_id'],
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'discount_percent' => $discount,
                    'stock' => $data['stock'],
                ]);

                $variantInfo = Variant::find($data['variant_id']);
                if ($variantInfo && $variantInfo->name === 'Standard') {
                    $standardPrice = $price;
                }
            }

            // 4. Cập nhật giá Standard cho sản phẩm
            $product->price = $standardPrice;
            $product->save();
        });

        return redirect()->route('admin.products')->with('success', 'Tạo sản phẩm thành công.');
    }

    public function edit($id)
    {
        $product = products::with('images')->findOrFail($id);
        $categories = categories::all();
        $authors = authors::all();
        $publishers = publishers::all();
        $productVariants = productVariants::with('variant')->where('product_id', $product->id)->get();
        $variants = Variant::where('status', 1)->get();

        return view('admin.productEdit', compact('product', 'categories', 'authors', 'publishers', 'productVariants', 'variants'));
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
            'variants.*.variant_id' => 'required|integer|exists:variants,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $product = products::findOrFail($id);

        // Kiểm tra tổng số lượng ảnh
        $currentImages = $product->images()->count();
        $newImages = $request->hasFile('images') ? count($request->file('images')) : 0;
        if ($currentImages + $newImages > 7) {
            return back()->withErrors(['images' => "Sản phẩm chỉ được tối đa 7 ảnh. Hiện tại đang có {$currentImages} ảnh."])->withInput();
        }

        // Kiểm tra biến thể trùng lặp
        $variantIds = collect($request->variants)->pluck('variant_id')->filter();
        if ($variantIds->count() !== $variantIds->unique()->count()) {
            return back()->withErrors(['variants' => 'Không được chọn trùng phiên bản cho cùng một sản phẩm.'])->withInput();
        }

        DB::transaction(function () use ($request, $product, $id, $variantIds) {
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->publisher_id = $request->publisher_id;
            $product->author_id = $request->author_id;
            $product->description = $request->description;

            // Thêm ảnh mới nếu có
            if ($request->hasFile('images')) {
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

                    if (empty($product->image)) {
                        $product->image = $imageName;
                    }
                }
            }

            // Cập nhật biến thể
            $standardPrice = 0;
            $submittedVariantIds = [];
            $allVariants = Variant::whereIn('id', $variantIds->unique())->get()->keyBy('id');

            foreach ($request->variants as $data) {
                $price = $data['price'];
                $salePrice = !empty($data['sale_price']) ? $data['sale_price'] : null;

                if (is_null($salePrice) || $salePrice <= 0 || $salePrice >= $price) {
                    $salePrice = null;
                    $discount = 0;
                } else {
                    $discount = round((($price - $salePrice) / $price) * 100);
                }

                $variantData = [
                    'product_id' => $id,
                    'variant_id' => $data['variant_id'],
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'discount_percent' => $discount,
                    'stock' => $data['stock'],
                ];

                if (!empty($data['id'])) {
                    $variant = productVariants::where('id', $data['id'])->where('product_id', $id)->first();
                    if ($variant) {
                        $variant->update($variantData);
                        $submittedVariantIds[] = $variant->id;
                    } else {
                        $newVariant = productVariants::create($variantData);
                        $submittedVariantIds[] = $newVariant->id;
                    }
                } else {
                    $newVariant = productVariants::create($variantData);
                    $submittedVariantIds[] = $newVariant->id;
                }

                $variantInfo = $allVariants->get($data['variant_id']);
                if ($variantInfo && $variantInfo->name === 'Standard') {
                    $standardPrice = $price;
                }
            }

            // Xóa biến thể không còn trong form
            productVariants::where('product_id', $id)->whereNotIn('id', $submittedVariantIds)->delete();

            $product->price = $standardPrice;
            $product->save();
        });

        return redirect()->route('admin.products')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy($id)
    {
        $product = products::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Xóa sản phẩm thành công.');
    }

    public function toggleStatus($id)
    {
        $product = products::findOrFail($id);
        $product->status = $product->status == 1 ? 0 : 1;
        $product->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function show($id)
    {
        $product = products::findOrFail($id);
        $productVariants = productVariants::with('variant')->where('product_id', $id)->get();
        $totalStock = $productVariants->sum('stock');

        return view('admin.productShow', compact('product', 'productVariants', 'totalStock'));
    }

    public function setPrimary($id)
    {
        DB::transaction(function () use ($id) {
            $image = ProductImage::findOrFail($id);

            ProductImage::where('product_id', $image->product_id)->update(['is_primary' => 0]);
            $image->update(['is_primary' => 1]);

            products::where('id', $image->product_id)->update(['image' => $image->image]);
        });

        return back()->with('success', 'Đã đổi ảnh đại diện.');
    }

    public function deleteImage($id)
    {
        DB::transaction(function () use ($id) {
            $image = ProductImage::findOrFail($id);
            $path = public_path('uploads/products/' . $image->image);

            if (file_exists($path)) {
                @unlink($path);
            }

            $productId = $image->product_id;
            $wasPrimary = $image->is_primary;

            $image->delete();

            if ($wasPrimary) {
                $newPrimary = ProductImage::where('product_id', $productId)->orderBy('sort_order')->first();

                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => 1]);
                    products::where('id', $productId)->update(['image' => $newPrimary->image]);
                } else {
                    products::where('id', $productId)->update(['image' => null]);
                }
            }
        });

        return back()->with('success', 'Đã xóa ảnh.');
    }

    public function sortImages(Request $request)
    {
        foreach ($request->images as $index => $id) {
            ProductImage::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $products = products::where(
            'name',
            'like',
            '%' . $request->keyword . '%'
        )
        ->select('id', 'name', 'image')
        ->distinct()
        ->limit(5)
        ->get()
        ->map(function ($product) {

            return [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image
                    ? asset('uploads/products/' . $product->image)
                    : null,
            ];

        });

        return response()->json($products);
    }
}
