@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết tin tức: <span class="text-primary">{{ $news->title }}</span></h1>
        <div>
            <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-sm btn-success shadow-sm mr-1">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI: Nội dung chi tiết bài viết -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Nội dung bài viết</h6>
                </div>
                <div class="card-body">
                    
                    <!-- Tiêu đề -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Tiêu đề</label>
                        <h4 class="text-dark font-weight-bold">{{ $news->title }}</h4>
                    </div>

                    <!-- Mô tả ngắn -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Mô tả ngắn</label>
                        <p class="text-dark font-italic bg-light p-3 rounded border mb-0">{{ $news->summary }}</p>
                    </div>

                    <!-- Nội dung -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Nội dung chi tiết</label>
                        <div class="text-dark p-3 bg-light rounded border" style="line-height: 1.6; white-space: pre-line;">{{ $news->content }}</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: Hình ảnh & Thông tin chung -->
        <div class="col-lg-4">
            
            <!-- Thẻ Ảnh -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image mr-2"></i> Ảnh bài viết</h6>
                </div>
                <div class="card-body text-center">
                    @if($news->image)
                        <img src="{{ asset('uploads/news/' . $news->image) }}" 
                             alt="{{ $news->title }}" 
                             class="img-fluid rounded shadow-sm border" 
                             style="max-height: 220px; width: 100%; object-fit: cover;">
                    @else
                        <span class="text-muted small font-italic py-4 d-block">Không có ảnh</span>
                    @endif
                </div>
            </div>

            <!-- Thẻ Trạng thái & Thao tác -->
            <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog mr-2"></i> Thông tin chung</h6>
                </div>
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="font-weight-bold text-muted">Trạng thái:</span>
                        @if($news->status == 1)
                            <span class="badge badge-success px-3 py-2 font-weight-bold">Hiển thị</span>
                        @else
                            <span class="badge badge-danger px-3 py-2 font-weight-bold">Ẩn</span>
                        @endif
                    </div>

                    @if(isset($news->is_featured))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="font-weight-bold text-muted">Nổi bật:</span>
                        @if($news->is_featured)
                            <span class="badge badge-primary px-3 py-2 font-weight-bold">Có</span>
                        @else
                            <span class="text-muted font-weight-bold">Không</span>
                        @endif
                    </div>
                    @endif

                    <hr class="my-3">

                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa tin tức
                        </a>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-light btn-block text-muted py-2 border">
                            Quay lại danh sách
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection