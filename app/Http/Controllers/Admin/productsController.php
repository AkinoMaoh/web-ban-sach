<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\products;
use App\Models\categories;
use App\Models\authors;
use App\Models\publishers;

class productsController extends Controller
{
    public function index()
    {
        $products = products::all();
        $categories = categories::all();
        return view('admin.products', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = categories::all();
        $publishers = publishers::all();
        $authors = authors::all();
        return view('admin.productAdd', compact('categories', 'publishers', 'authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new products();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->price = $request->price;
        $product->stock = $request->stock;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = products::findOrFail($id);
        $categories = categories::all();
        $publishers = publishers::all();
        $authors = authors::all();
        return view('admin.productEdit', compact('product', 'categories', 'publishers', 'authors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = products::findOrFail($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->publisher_id = $request->publisher_id;
        $product->author_id = $request->author_id;
        $product->price = $request->price;
        $product->stock = $request->stock;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5_file($image->getRealPath()) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function show($id)
    {
        $product = products::findOrFail($id);
        return view('admin.productShow', compact('product'));
    }

    public function destroy($id)
    {
        $product = products::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $product = products::findOrFail($id);
        $product->status = $product->status == 1 ? 0 : 1;
        $product->save();
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
