@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Chi tiết tin tức</h1>

    <div class="card-body">

        <a href="{{ route('admin.news.index') }}" class="btn btn-success mb-3">
            Quay lại
        </a>

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Thông tin tin tức
                </h6>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label><strong>Tiêu đề</strong></label>
                    <p>{{ $news->title }}</p>
                </div>

                <div class="form-group">
                    <label><strong>Slug</strong></label>
                    <p>{{ $news->slug }}</p>
                </div>

                <div class="form-group">
                    <label><strong>Ảnh</strong></label><br>

                    @if($news->image)
                        <img src="{{ asset('uploads/news/'.$news->image) }}" width="250">
                    @else
                        Không có ảnh
                    @endif

                </div>

                <div class="form-group">
                    <label><strong>Mô tả ngắn</strong></label>
                    <p>{{ $news->summary }}</p>
                </div>

                <div class="form-group">
                    <label><strong>Nội dung</strong></label>
                    <p>{{ $news->content }}</p>
                </div>

                <div class="form-group">
                    <label><strong>Trạng thái</strong></label>

                    @if($news->status == 1)
                        <span class="badge badge-success">Hiển thị</span>
                    @else
                        <span class="badge badge-danger">Ẩn</span>
                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection