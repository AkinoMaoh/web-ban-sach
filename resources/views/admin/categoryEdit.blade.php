@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang và Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chỉnh sửa danh mục</h1>
        <a href="{{ route('admin.categories') }}" class="btn btn-sm btn-secondary shadow-sm">
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

    <!-- Form update danh mục -->
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- CỘT TRÁI: Thông tin văn bản (Tên danh mục) -->
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit mr-2"></i> Thông tin danh mục
                        </h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tên danh mục -->
                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   required 
                                   placeholder="Nhập tên danh mục...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: Ảnh danh mục & Nút hành động -->
            <div class="col-lg-4">
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-image mr-2"></i> Ảnh danh mục
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        
                        <!-- Khu vực xem trước ảnh hiện tại / ảnh mới -->
                        <div class="mb-3">
                            @if ($category->image && file_exists(public_path('uploads/categories/' . $category->image)))
                                <img src="{{ asset('uploads/categories/' . $category->image) }}" 
                                     alt="Category Preview" 
                                     class="rounded shadow-sm border" 
                                     width="150" height="150" 
                                     style="object-fit: cover;" 
                                     id="image-preview">
                            @else
                                <img src="https://placehold.co/200x200?text=No+Image" 
                                     alt="Category Preview" 
                                     class="rounded shadow-sm border" 
                                     width="150" height="150" 
                                     style="object-fit: cover;" 
                                     id="image-preview">
                            @endif
                        </div>

                        <!-- Chọn file ảnh mới -->
                        <div class="form-group text-left">
                            <label for="image" class="font-weight-bold text-dark small text-uppercase">Thay đổi ảnh</label>
                            <input type="file" class="form-control-file @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <small class="text-muted d-block mt-1">Định dạng: jpeg, png, jpg, gif (Để trống nếu giữ nguyên)</small>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Nút Cập nhật & Hủy -->
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-save mr-1"></i> Cập nhật danh mục
                            </button>
                            <a href="{{ route('admin.categories') }}" class="btn btn-light btn-block text-muted py-2 border">
                                Hủy bỏ
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<!-- Script hỗ trợ xem trước ảnh mới chọn ngay lập tức -->
@push('scripts')
<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('image-preview').src = URL.createObjectURL(file);
        }
    });
</script>
@endpush

@endsection