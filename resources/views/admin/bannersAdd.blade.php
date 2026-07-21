
@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 text-gray-800">Thêm Banner</h1>

        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="card shadow">

            <div class="card-header">
                <strong>Thông tin Banner</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- Cột trái -->
                    <div class="col-lg-8">

                        <div class="form-group">
                            <label>Tiêu đề</label>
                            <input type="text"
                                   class="form-control"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <input type="text"
                                   class="form-control"
                                   name="link"
                                   value="{{ old('link') }}">
                        </div>

                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea class="form-control"
                                      rows="4"
                                      name="description">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Vị trí</label>
                                <select class="form-control" name="position">
                                    <option value="home">Home</option>
                                    <option value="category">Category</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Thứ tự</label>
                                <input type="number"
                                class="form-control"
                                name="sort_order"
                                value="{{ old('sort_order') }}"
                                placeholder="Để trống để tự tăng">
                            </div>

                            <div class="col-md-4">
                                <label>Trạng thái</label>
                                <select class="form-control" name="status">
                                    <option value="1">Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col-md-6">
                                <label>Ngày bắt đầu</label>
                                <input type="date"
                                       class="form-control"
                                       name="start_date"
                                       value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label>Ngày kết thúc</label>
                                <input type="date"
                                       class="form-control"
                                       name="end_date"
                                       value="{{ old('end_date') }}">
                            </div>

                        </div>

                    </div>

                    <!-- Cột phải -->
                    <div class="col-lg-4 text-center">

                        <img id="preview"
                             src="https://placehold.co/350x200?text=Banner"
                             class="img-fluid rounded shadow mb-3"
                             style="max-height:220px;object-fit:cover;">

                        <input type="file"
                               class="form-control"
                               id="image"
                               name="image"
                               required>

                    </div>

                </div>

            </div>

            <div class="text-left">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Thêm Banner
                </button>
            </div>

        </div>

    </form>

</div>

<script>
document.getElementById('image').addEventListener('change', function(e){
    if(e.target.files.length){
        document.getElementById('preview').src =
            URL.createObjectURL(e.target.files[0]);
    }
});
</script>

@endsection

