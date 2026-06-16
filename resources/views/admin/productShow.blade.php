@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang chi tiết sản phẩm</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  
                                    <div class="form-group">
                                        <label for="name">Tên sách</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Danh mục</label>
                                        <input type="text" class="form-control" id="category_id" name="category_id" value="{{ $product->category->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="author_id">Tác giả</label>
                                        <input type="text" class="form-control" id="author_id" name="author_id" value="{{ $product->author->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="publisher_id">NXB</label>
                                        <input type="text" class="form-control" id="publisher_id" name="publisher_id" value="{{ $product->publishers->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Đánh giá</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $product->description }}" readonly>
                                    </div>
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
                                                @forelse($productVariants as $variant)
                                                    <tr>
                                                        <td>
                                                            {{ $variant->edition == 'Standard' ? 'Bản thường' : 'Bản đặc biệt' }}
                                                        </td>

                                                        <td>
                                                            {{ number_format($variant->price, 0, ',', '.') }} đ
                                                        </td>

                                                        <td>
                                                            {{ $variant->stock }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">
                                                            Chưa có biến thể nào
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <label>Tổng giá gốc</label>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ number_format($product->price, 0, ',', '.') }} đ"
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Tổng số lượng</label>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ $productVariants->sum('stock') }}"
                                            readonly>
                                    </div>  
                                   <div class="form-group">
                                        <label>Hình ảnh</label>
                                        <br>
                                        <img src="{{ asset('uploads/products/' . $product->image) }}"
                                            alt="Ảnh sản phẩm"
                                            width="150">
                                </div>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>

@endsection
