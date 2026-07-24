<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;
use App\Models\productVariants;

class productsController extends Controller
{
    public function index(Request $request)
    {
        $categories = categories::all();

        $query = products::with('publishers', 'author', 'category');

        if ($request->category_id) {
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.edition' => 'required|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        // ======================
        // 2. CREATE VARIANTS
        // ======================
        $standardPrice = 0;

        $standardPrice = 0;

        foreach ($request->variants as $data) {

            $variant = new ProductVariants();
            $variant->product_id = $product->id;
            $variant->edition = $data['edition'];
            $variant->price = $data['price'];
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
        $product = Products::findOrFail($id);

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
            'image' => 'nullable|image',
            'variants' => 'required|array|min:1',
            'variants.*.edition' => 'required|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'variants.*.edition.required' => 'Vui lòng nhập tên phiên bản.',
            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.price.numeric' => 'Giá phải là số.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
            'variants.*.stock.integer' => 'Số lượng phải là số nguyên.',
        ]);

        $product = Products::findOrFail($id);

        // Cập nhật thông tin sản phẩm
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->description = $request->description;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        // Xóa toàn bộ biến thể cũ
        ProductVariants::where('product_id', $id)->delete();

        $standardPrice = 0;

        // Thêm lại các biến thể từ form
        foreach ($request->variants as $index => $data) {

            ProductVariants::create([
                'product_id' => $id,
                'edition'   => $data['edition'],
                'price'     => $data['price'],
                'stock'     => $data['stock'],
            ]);

            // Lấy giá của phiên bản đầu tiên
            if ($index == 0) {
                $standardPrice = $data['price'];
            }
        }

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
}
