@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết tác giả: <span class="text-primary">{{ $author->name }}</span></h1>
        <div>
            <a href="{{ route('admin.authors.edit', $author->id) }}" class="btn btn-sm btn-success shadow-sm mr-1">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.authors') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI: Thông tin tên và tiểu sử tác giả -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas id-card mr-2"></i> Thông tin cá nhân
                    </h6>
                </div>
                <div class="card-body">
                    
                    <!-- Tên tác giả -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Tên tác giả</label>
                        <h4 class="text-dark font-weight-bold">{{ $author->name }}</h4>
                    </div>

                    <!-- Tiểu sử -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Tiểu sử</label>
                        <div class="text-dark bg-light p-3 rounded border" style="line-height: 1.6; white-space: pre-line;">{{ $author->bio ?? 'Chưa có thông tin tiểu sử' }}</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: Ảnh đại diện & Thao tác nhanh -->
        <div class="col-lg-4">
            
            <!-- Thẻ Ảnh đại diện -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-image mr-2"></i> Ảnh đại diện
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if ($author->avatar && file_exists(public_path('uploads/authors/' . $author->avatar)))
                        <img src="{{ asset('uploads/authors/' . $author->avatar) }}" 
                             alt="{{ $author->name }}" 
                             class="rounded-circle shadow-sm border" 
                             width="160" height="160" 
                             style="object-fit: cover;">
                    @else
                        <img src="https://placehold.co/160x160?text=No+Avatar" 
                             alt="Default Avatar" 
                             class="rounded-circle shadow-sm border" 
                             width="160" height="160" 
                             style="object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Thẻ Thao tác -->
            <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog mr-2"></i> Thao tác
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.authors.edit', $author->id) }}" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa tác giả
                        </a>
                        <a href="{{ route('admin.authors') }}" class="btn btn-light btn-block text-muted py-2 border">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection