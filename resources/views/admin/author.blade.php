@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang quản lý  tác giả</h1>
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
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu tác giả</h6>
                        </div>
                         <div class="card-body">
                            <a href="{{ route('admin.authorAdd') }}" class="btn btn-success mb-3">Thêm tác giả</a>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên tác giả</th>
                                            <th>Ảnh</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach ($authors as $author)
                                        <tr>
                                            <td>{{ $author->id }}</td>
                                            <td>{{ $author->name }}</td>
                                            <td>
                                                <img src="{{ asset('uploads/authors/' . $author->avatar) }}" alt="Ảnh nhà xuất bản" width="100">
                                            </td>
                                            <td> 
                                                <a href="{{ route('admin.authors.show', $author->id) }}" class="btn btn-sm btn-primary">Chi tiết</a>
                                                <a href="{{ route('admin.authors.edit', $author->id) }}" class="btn btn-sm btn-success">Sửa</a>
                                               <form action="{{ route('admin.authors.toggleStatus', $author->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $author->status ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $author->status ? 'Ẩn' : 'Hiện' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.authors.destroy', $author->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhà xuất bản này?')">Xóa</a>
                                               
                                            </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                {{ $authors->appends(request()->query())->links() }}
            </div>
                </div>

@endsection