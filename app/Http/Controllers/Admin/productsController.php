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
        $categories = categories::all();
        $publishers = publishers::all();
        $authors = authors::all();
        $productVariants = productVariants::all();
        return view('admin.productAdd', compact('categories', 'publishers', 'authors', 'productVariants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'edition' => 'required|string',
            'volume_number' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // ======================
        // 1. CREATE PRODUCT
        // ======================
        $product = new products();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->price = $request->price; // giá gốc
        $product->description = $request->description;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        // ======================
        // 2. CALCULATE VARIANT PRICE
        // ======================
        $basePrice = $product->price;
        $price = $basePrice;

        // Edition
        if ($request->edition === 'Special') {
            $price += $basePrice * 0.30;
        }

        if ($request->edition === 'Signed') {
            $price += $basePrice * 0.20;
        }

        // Volume
        if ($request->volume_number) {
            $price += $basePrice * (0.01 * $request->volume_number);
        }

        // ======================
        // 3. CREATE VARIANT
        // ======================
        $variant = new productVariants();
        $variant->product_id = $product->id;
        $variant->stock = $request->stock;
        $variant->edition = $request->edition;
        $variant->volume_number = $request->volume_number;
        $variant->price = $price;
        $variant->save();

        return redirect()->route('admin.products')
            ->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = products::findOrFail($id);
        $categories = categories::all();
        $publishers = publishers::all();
        $authors = authors::all();
        $productVariants = productVariants::all();
        return view('admin.productEdit', compact('product', 'categories', 'publishers', 'authors', 'productVariants'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = products::findOrFail($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }
        $product->stock = productVariants::where(
            'product_id',
            $id
        )->sum('stock');

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function show($id)
    {
        $product = products::findOrFail($id);
        $product->stock = productVariants::where(
            'product_id',
            $id
        )->sum('stock');
        $productVariants = productVariants::all();
        return view('admin.productShow', compact('product', 'productVariants'));
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
}
