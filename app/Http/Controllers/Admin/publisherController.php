<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\publishers;

class publisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publishers = publishers::withCount('products')->paginate(8);

        return view('admin.publisher', compact('publishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.publisherAdd');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'website' => 'required'
        ]);
        publishers::create([
            'name' => $request->name,
            'address' => $request->address,
            'website' => $request->website,
        ]);
        return redirect()
            ->route('admin.publishers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $publisher = publishers::findOrFail($id);
        return view('admin.publisherEdit', compact('publisher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $publisher = publishers::findOrFail($id);
        $publisher->update([
            'name' => $request->name,
            'address' => $request->address,
            'website' => $request->website,
        ]);
        return redirect()
            ->route('admin.publishers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $publisher = publishers::findOrFail($id);

        // Kiểm tra có sản phẩm thuộc nhà xuất bản này không
        if ($publisher->products()->exists()) {
            return redirect()
                ->route('admin.publishers.index')
                ->with('error', 'Không thể xóa nhà xuất bản vì vẫn còn sản phẩm thuộc nhà xuất bản này.');
        }

        $publisher->delete();

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Nhà xuất bản đã được xóa thành công.');
    }
}
