@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Trang thêm tin tức</h1>

    <div class="card-body">

        <a href="{{ route('admin.news.index') }}" class="btn btn-success mb-3">
            Quay lại
        </a>

        <div class="card-body">

            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Điền thông tin tin tức muốn thêm
                    </h6>
                </div>

                <div class="card-body">

                    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group">

                            <label for="title">
                                Tiêu đề
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="title"
                                name="title"
                                required>

                        </div>

                        <div class="form-group">

                            <label for="slug">
                                Slug
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="slug"
                                name="slug"
                                required>

                        </div>

                        <div class="form-group">

                            <label for="image">
                                Ảnh
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="image"
                                name="image">

                        </div>

                        <div class="form-group">

                            <label for="summary">
                                Mô tả ngắn
                            </label>

                            <textarea
                                class="form-control"
                                id="summary"
                                name="summary"
                                rows="3"
                                required></textarea>

                        </div>

                        <div class="form-group">

                            <label for="content">
                                Nội dung
                            </label>

                            <textarea
                                class="form-control"
                                id="content"
                                name="content"
                                rows="8"
                                required></textarea>

                        </div>

                        <div class="form-group">

                            <label for="status">
                                Trạng thái
                            </label>

                            <select
                                class="form-control"
                                id="status"
                                name="status">

                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>

                            </select>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            Thêm tin tức
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection