@extends('admin.layout')

@section('admin_content')
  <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang quản lý Banner</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu banner</h6>
                        </div>
                         <div class="card-body">
                            <a href="{{ route('admin.banners.create') }}" class="btn btn-success mb-3">Thêm Banner</a>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered"  width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ảnh</th>
                                            <th>Tiêu đề</th>
                                            <th>Vị trí</th>
                                            <th>Thứ tự</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach ($banners as $banner)
                                        <tr>
                                            <td>{{ $banner->id }}</td>
                                            
                                            <td>
                                                <img src="{{ asset('uploads/banners/' . $banner->image) }}" alt="Ảnh banner" width="100">
                                            </td>
                                            <td>{{ $banner->title }}</td>
                                            <td>{{ $banner->position }}</td>
                                            <td>{{ $banner->sort_order }}</td>
                                            <td>
                                            @if($banner->status)
                                                <span class="badge badge-success px-3 py-2">Hiển thị</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-2">Ẩn</span>
                                            @endif
                                            @php
                                                $now = now();
                                            @endphp

                                            @if($banner->start_date && $now->lt($banner->start_date))
                                                <span class="badge badge-primary px-3 py-2">
                                                    <i class="fas fa-clock"></i> Chưa tới
                                                </span>

                                            @elseif($banner->end_date && $now->gt($banner->end_date))
                                                <span class="badge badge-danger px-3 py-2">
                                                    <i class="fas fa-times-circle"></i> Hết hạn
                                                </span>

                                            @else
                                                <span class="badge badge-success px-3 py-2">
                                                    <i class="fas fa-check-circle"></i> Đang diễn ra
                                                </span>
                                            @endif
                                            </td>
                                            <td> 
                                                <a href="{{ route('admin.banners.show',$banner->id) }}"
                                                class="btn btn-info btn-sm">
                                                    <i class="fas fa-search"></i>
                                                </a>

                                                <form action="{{ route('admin.banners.toggleStatus', $banner->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $banner->status ? 'btn-warning' : 'btn-success' }}"
                                                    title="{{ $banner->status ? 'Ẩn' : 'Hiện' }}">
                                                    <i class="fas {{ $banner->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                             </form>

                                                <a href="{{ route('admin.banners.edit',$banner->id) }}"
                                                class="btn btn-success btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.banners.destroy',$banner->id) }}"
                                                    method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                               
                                            </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                {{ $banners->appends(request()->query())->links() }}
            </div>
                </div>
@endsection