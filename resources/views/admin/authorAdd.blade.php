@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang và Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Thêm tác giả mới</h1>
        <a href="{{ route('admin.authors') }}" class="btn btn-sm btn-secondary shadow-sm">
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

    <!-- Form store (có enctype để upload file ảnh) -->
    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- CỘT TRÁI: Thông tin văn bản (Tên, Tiểu sử) -->
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin chi tiết</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tên tác giả -->
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold text-dark">Tên tác giả <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Nhập tên tác giả...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tiểu sử -->
                        <div class="form-group mb-2">
                            <label for="bio" class="font-weight-bold text-dark">Tiểu sử</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="6" placeholder="Nhập tiểu sử tác giả...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: Ảnh đại diện & Nút hành động -->
            <div class="col-lg-4">
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image mr-2"></i> Ảnh đại diện</h6>
                    </div>
                    <div class="card-body text-center">
                        
                        <!-- Khu vực xem trước ảnh (Preview) -->
                        <div class="mb-3">
                            <img src="https://via.placeholder.com/120" 
                                 alt="Avatar preview" 
                                 class="rounded-circle shadow-sm border" 
                                 width="120" height="120" 
                                 style="object-fit: cover;" 
                                 id="avatar-preview">
                        </div>

                        <!-- Chọn file ảnh -->
                        <div class="form-group text-left">
                            <label for="avatar" class="font-weight-bold text-dark small text-uppercase">Tải lên ảnh</label>
                            <input type="file" class="form-control-file @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            <small class="text-muted d-block mt-1">Định dạng: jpeg, png, jpg, gif</small>
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Nút Thêm & Hủy -->
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-plus-circle mr-1"></i> Thêm tác giả
                            </button>
                            <a href="{{ route('admin.authors') }}" class="btn btn-light btn-block text-muted py-2 border">
                                Hủy bỏ
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<!-- Script hỗ trợ xem trước ảnh khi chọn file -->
@push('scripts')
<script>
    document.getElementById('avatar').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('avatar-preview').src = URL.createObjectURL(file);
        }
    });
</script>
@endpush

@endsection