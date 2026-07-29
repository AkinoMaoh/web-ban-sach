@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang và Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chỉnh sửa Banner <span class="text-primary">#{{ $banner->id }}</span></h1>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-secondary shadow-sm">
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

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4 border-0 rounded-lg">
            
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin chi tiết Banner</h6>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- CỘT TRÁI: Nhập liệu thông tin banner -->
                    <div class="col-lg-8">

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   name="title"
                                   value="{{ old('title', $banner->title) }}"
                                   placeholder="Nhập tiêu đề banner..."
                                   required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Link liên kết</label>
                            <input type="text"
                                   class="form-control"
                                   name="link"
                                   value="{{ old('link', $banner->link) }}"
                                   placeholder="Ví dụ: https://... hoặc /products">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Mô tả</label>
                            <textarea class="form-control"
                                      rows="3"
                                      name="description"
                                      placeholder="Nhập mô tả ngắn cho banner...">{{ old('description', $banner->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">Vị trí</label>
                                <select class="form-control" name="position">
                                    <option value="home" {{ $banner->position == 'home' ? 'selected' : '' }}>Home</option>
                                    <option value="category" {{ $banner->position == 'category' ? 'selected' : '' }}>Category</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label class="font-weight-bold text-dark">Thứ tự</label>
                                <input type="number"
                                       class="form-control @error('sort_order') is-invalid @enderror"
                                       name="sort_order"
                                       value="{{ old('sort_order', $banner->sort_order) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label class="font-weight-bold text-dark">Trạng thái</label>
                                <select class="form-control" name="status">
                                    <option value="1" {{ $banner->status ? 'selected' : '' }}>Hiển thị</option>
                                    <option value="0" {{ !$banner->status ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">Ngày bắt đầu</label>
                                <input type="date"
                                       class="form-control"
                                       name="start_date"
                                       value="{{ old('start_date', optional($banner->start_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">Ngày kết thúc</label>
                                <input type="date"
                                       class="form-control"
                                       name="end_date"
                                       value="{{ old('end_date', optional($banner->end_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                    </div>

                    <!-- CỘT PHẢI: Xem trước ảnh và Tải file -->
                    <div class="col-lg-4 text-center border-left">
                        <label class="font-weight-bold text-dark d-block text-left mb-2">Hình ảnh Banner</label>
                        
                        <div class="mb-3">
                            <img id="preview"
                                 src="{{ asset('uploads/banners/' . $banner->image) }}"
                                 class="img-fluid rounded shadow-sm border"
                                 style="max-height: 200px; width: 100%; object-fit: cover;"
                                 alt="Banner Preview">
                        </div>

                        <div class="form-group text-left">
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
            </div>

            <!-- Footer chứa nút lưu -->
            <div class="card-footer bg-white py-3 d-flex justify-content-end">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary mr-2 px-4">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-success px-4 font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Cập nhật Banner
                </button>
            </div>

        </div>

    </form>

</div>

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