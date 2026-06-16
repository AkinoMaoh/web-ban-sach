@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang cập nhật sản phẩm</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Điền thông tin sản phẩm muốn cập nhật</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Tên sách</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Danh mục</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">Chọn danh mục</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="author_id">Tác giả</label>
                                        <select class="form-control" id="author_id" name="author_id" required>
                                            <option value="">Chọn tác giả</option>
                                            @foreach ($authors as $author)
                                                <option value="{{ $author->id }}" {{ $product->author_id == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="publisher_id">NXB</label>
                                        <select class="form-control" id="publisher_id" name="publisher_id" required>
                                            <option value="">Chọn NXB</option>
                                            @foreach ($publishers as $publisher)
                                                <option value="{{ $publisher->id }}" {{ $product->publisher_id == $publisher->id ? 'selected' : '' }}>
                                                    {{ $publisher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                       <div class="form-group">
                                        <label for="description">Đánh giá</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $product->description }}" required>
                                    </div>
                                   @php
                                        $variant = $productVariants->first();
                                    @endphp

                                    <div class="form-group">
                                        <label for="edition">Phiên bản</label>
                                        <input type="text" class="form-control"
                                            name="edition"
                                            value="{{ $variant->edition ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Giá</label>
                                        <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Số lượng</label>
                                        <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Hình ảnh</label>
                                        <input type="file" class="form-control" id="image" name="image" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>

@endsection