@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Trang cập nhật sản phẩm</h1>

    <div class="card-body">
        <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
            </div>

            <div class="card-body">

                <form action="{{ route('admin.products.update', $product->id) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- NAME --}}
                    <div class="form-group">
                        <label>Tên sách</label>
                        <input type="text" class="form-control" name="name"
                               value="{{ $product->name }}" required>
                    </div>

               <div class="row">
                    {{-- CATEGORY --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select name="category_id" class="form-control">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- AUTHOR --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tác giả</label>
                            <select name="author_id" class="form-control">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}"
                                        {{ $product->author_id == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- PUBLISHER --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NXB</label>
                            <select name="publisher_id" class="form-control">
                                @foreach($publishers as $publisher)
                                    <option value="{{ $publisher->id }}"
                                        {{ $product->publisher_id == $publisher->id ? 'selected' : '' }}>
                                        {{ $publisher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                    {{-- DESCRIPTION --}}
                    <div class="form-group">
                        <label>Mô tả</label>
                        <input type="text" class="form-control" name="description"
                               value="{{ $product->description }}" required>
                    </div>

                    {{-- VARIANTS --}}
                    @php
                        $editions = ['Standard', 'Special', 'Special Signed'];
                    @endphp

                    <div class="form-group">
                        <label>Danh sách biến thể</label>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Phiên bản</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($editions as $edition)

                                    @php
                                        $variant = $productVariants->firstWhere('edition', $edition);
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $edition == 'Standard' ? 'Bản thường' : ($edition == 'Special' ? 'Bản đặc biệt' : 'Bản đặc biệt có chữ ký') }}

                                            <input type="hidden"
                                                   name="variants[{{ $edition }}][edition]"
                                                   value="{{ $edition }}">
                                        </td>

                                        {{-- PRICE --}}
                                        <td>
                                            <input type="number"
                                                class="form-control"
                                                name="variants[{{ $edition }}][price]"
                                                value="{{ (int) ($variant->price ?? 0) }}">
                                        </td>

                                        {{-- STOCK --}}
                                        <td>
                                            <input type="number"
                                                   class="form-control"
                                                   name="variants[{{ $edition }}][stock]"
                                                   value="{{ $variant->stock ?? 0 }}">
                                        </td>

                                       
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- TOTAL STOCK --}}
                    <div class="form-group">
                        <label>Tổng số lượng</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $productVariants->sum('stock') }}"
                               readonly>
                    </div>

                    {{-- IMAGE --}}
                    <div class="form-group">
                        <label>Ảnh sản phẩm</label>
                        <input type="file" class="form-control-file" name="image">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Cập nhật sản phẩm
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection