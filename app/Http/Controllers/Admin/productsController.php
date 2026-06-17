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
        $products = products::all();
        $categories = categories::all();
        $query = products::query();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();
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
            'variants' => 'required|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
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
        $product->stock = 0;
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

        foreach ($request->variants as $edition => $data) {

            $variant = new ProductVariants();
            $variant->product_id = $product->id;
            $variant->edition = $edition;
            $variant->price = $data['price'];
            $variant->stock = $data['stock'];

            $variant->save();

            if ($edition === 'Standard') {
                $standardPrice = $data['price'];
            }
        }

        // ======================
        // 3. UPDATE PRODUCT SUMMARY
        // ======================


        $product->price = $standardPrice;
        $product->stock = ProductVariants::where('product_id', $product->id)->sum('stock');
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

        // Luôn tạo đủ 3 biến thể
        $editions = [
            'Standard',
            'Special',
            'Special Signed'
        ];

        foreach ($editions as $edition) {

            ProductVariants::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'edition' => $edition
                ],
                [
                    'price' => 0,
                    'stock' => 0,

                ]
            );
        }

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
            'variants' => 'required|array',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        $product = Products::findOrFail($id);

        // ======================
        // 1. UPDATE PRODUCT INFO
        // ======================
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

        // ======================
        // 2. UPDATE VARIANTS
        // ======================
        $standardPrice = 0;

        foreach ($request->variants as $edition => $data) {

            $variant = ProductVariants::where('product_id', $id)
                ->where('edition', $edition)
                ->first();

            if (!$variant) {
                $variant = new ProductVariants();
                $variant->product_id = $id;
                $variant->edition = $edition;
            }

            $variant->stock = $data['stock'] ?? 0;
            $variant->price = $data['price'] ?? 0;


            if ($edition === 'Standard') {
                $standardPrice = $variant->price;
            }

            $variant->save();
        }

        // ======================
        // 3. UPDATE PRODUCT SUMMARY
        // ======================
        $product->price = $standardPrice;

        $product->stock = ProductVariants::where('product_id', $id)->sum('stock');

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
        $product->stock = productVariants::where(
            'product_id',
            $id
        )->sum('stock');
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
