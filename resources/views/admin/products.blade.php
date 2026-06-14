@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang quản lý sản phẩm</h1>
                  
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu sản phẩm</h6>
                        </div>
                         <div class="card-body">
                            <a href="{{ route('admin.productAdd') }}" class="btn btn-success mb-3">Thêm sản phẩm</a>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên sách</th>
                                            <th>Danh mục</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Ảnh</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                <img src="{{ asset('uploads/products/' . $product->image) }}" alt="Ảnh sản phẩm" width="100">
                                            </td>
                                            <td> 
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-primary">Chi tiết</a>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-success">Sửa</a>
                                               <form action="{{ route('admin.products.toggleStatus', $product->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $product->status ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $product->status ? 'Ẩn' : 'Hiện' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.products.destroy', $product->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
                                               
                                            </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

@endsection