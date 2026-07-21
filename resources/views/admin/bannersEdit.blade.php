
@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 text-gray-800">Chỉnh sửa Banner</h1>

        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card shadow">
            <div class="card-body">

                <div class="row">

                    <!-- Thông tin -->
                    <div class="col-lg-8">

                        <div class="form-group">
                            <label>Tiêu đề</label>
                            <input type="text"
                                   class="form-control"
                                   name="title"
                                   value="{{ old('title',$banner->title) }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <input type="text"
                                   class="form-control"
                                   name="link"
                                   value="{{ old('link',$banner->link) }}">
                        </div>

                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea class="form-control"
                                      rows="4"
                                      name="description">{{ old('description',$banner->description) }}</textarea>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vị trí</label>
                                    <select class="form-control" name="position">
                                        <option value="home" {{ $banner->position=='home'?'selected':'' }}>Home</option>
                                        <option value="category" {{ $banner->position=='category'?'selected':'' }}>Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Thứ tự</label>
                                    <input type="number"
                                           class="form-control"
                                           name="sort_order"
                                           value="{{ old('sort_order',$banner->sort_order) }}">
                                           @error('sort_order')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="status">
                                        <option value="1" {{ $banner->status ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ !$banner->status ? 'selected' : '' }}>Ẩn</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <label>Ngày bắt đầu</label>
                                <input type="date"
                                       class="form-control"
                                       name="start_date"
                                       value="{{ optional($banner->start_date)->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-6">
                                <label>Ngày kết thúc</label>
                                <input type="date"
                                       class="form-control"
                                       name="end_date"
                                       value="{{ optional($banner->end_date)->format('Y-m-d') }}">
                            </div>

                        </div>

                    </div>

                    <!-- Ảnh -->
                    <div class="col-lg-4 text-center">

                        <img id="preview"
                             src="{{ asset('uploads/banners/'.$banner->image) }}"
                             class="img-fluid rounded shadow mb-3"
                             style="max-height:260px;object-fit:cover;">

                        <input type="file"
                               class="form-control"
                               name="image"
                               id="image">

                    </div>

                </div>

            </div>

            <div class="text-left p-3 ">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </div>

        </div>

    </form>

</div>

<script>
document.getElementById('image').addEventListener('change', function(e){
    if(e.target.files.length){
        document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
    }
});
</script>

@endsection

