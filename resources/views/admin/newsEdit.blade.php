@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chỉnh sửa tin tức: <span class="text-primary">{{ $news->title }}</span></h1>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Hiển thị lỗi Validate nếu có -->
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Vui lòng kiểm tra lại:</h6>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- CỘT TRÁI: Nội dung văn bản (Tiêu đề, Slug, Mô tả, Nội dung) -->
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin chi tiết</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tiêu đề -->
                        <div class="form-group mb-3">
                            <label for="title" class="font-weight-bold text-dark">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control form-control-lg @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $news->title) }}"
                                   placeholder="Nhập tiêu đề tin tức..."
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mô tả ngắn -->
                        <div class="form-group mb-3">
                            <label for="summary" class="font-weight-bold text-dark">Mô tả ngắn <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('summary') is-invalid @enderror"
                                      id="summary"
                                      name="summary"
                                      rows="3"
                                      placeholder="Nhập mô tả ngắn gọn..."
                                      required>{{ old('summary', $news->summary) }}</textarea>
                            @error('summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nội dung chi tiết -->
                        <div class="form-group mb-2">
                            <label for="content" class="font-weight-bold text-dark">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content"
                                      name="content"
                                      rows="8"
                                      placeholder="Nhập nội dung bài viết..."
                                      required>{{ old('content', $news->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: Hình ảnh, Cài đặt & Nút cập nhật -->
            <div class="col-lg-4">
                
                <!-- Thẻ Hình ảnh -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image mr-2"></i> Ảnh bài viết</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($news->image)
                                <img id="preview"
                                     src="{{ asset('uploads/news/' . $news->image) }}"
                                     class="img-fluid rounded shadow-sm border"
                                     style="max-height: 180px; width: 100%; object-fit: cover;"
                                     alt="News Preview">
                            @else
                                <img id="preview"
                                     src="https://placehold.co/350x200?text=No+Image"
                                     class="img-fluid rounded shadow-sm border"
                                     style="max-height: 180px; width: 100%; object-fit: cover;"
                                     alt="News Preview">
                            @endif
                        </div>

                        <div class="form-group text-left mb-0">
                            <input type="file"
                                   class="form-control-file @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <small class="text-muted d-block mt-2">Chọn ảnh mới nếu muốn thay đổi</small>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Thẻ Cài đặt hiển thị & Nút lưu -->
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog mr-2"></i> Cài đặt & Lưu</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Trạng thái -->
                        <div class="form-group mb-3">
                            <label for="status" class="font-weight-bold text-dark small text-uppercase">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1" {{ old('status', $news->status) == 1 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ old('status', $news->status) == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <!-- Tin tức nổi bật -->
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_featured"
                                       name="is_featured"
                                       value="1"
                                       {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="is_featured">
                                    Tin tức nổi bật
                                </label>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Nút Cập nhật & Hủy -->
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-save mr-1"></i> Cập nhật tin tức
                            </button>
                            <a href="{{ route('admin.news.index') }}" class="btn btn-light btn-block text-muted py-2 border">
                                Hủy bỏ
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

</div>

<!-- Script xem trước ảnh khi chọn file -->
@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e){
    if(e.target.files.length){
        document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
    }
});
</script>
@endpush

@endsection