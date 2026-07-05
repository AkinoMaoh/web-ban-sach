@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang quản lý danh mục</h1>
                  
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu danh mục</h6>
                        </div>
                         <div class="card-body">
                            <a href="{{ route('admin.categoryAdd') }}" class="btn btn-success mb-3">Thêm danh mục</a>
                              @if(session('error'))
                                                    <div class="alert alert-danger">
                                                        {{ session('error') }}
                                                    </div>
                                                @endif

                                                @if(session('success'))
                                                    <div class="alert alert-success">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên danh mục</th>
                                            <th>Ảnh</th>
                                            <th>Số lượng sản phẩm thuộc danh mục</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <img src="{{ asset('uploads/categories/' . $category->image) }}" alt="Ảnh danh mục" width="100">
                                            </td>
                                            <td> 
                                                {{ $category->products_count }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-success" title="Sửa"> <i class="fas fa-edit"></i> </a>
                                               <form action="{{ route('admin.categories.toggleStatus', $category->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $category->status ? 'btn-warning' : 'btn-success' }}"
                                                        title="{{ $category->status ? 'Ẩn' : 'Hiện' }}">
                                                        <i class="fas {{ $category->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                    </button>
                                                </form>
                                              
         <a href="{{ route('admin.categories.destroy', $category->id) }}"
   class="btn btn-sm btn-danger"
   title="Xóa"
   onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
    <i class="fas fa-trash"></i>
</a>                          
                                      </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <div class="d-flex justify-content-center mt-3">
                {{ $categories->appends(request()->query())->links() }}
            </div>

                </div>

@endsection