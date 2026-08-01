@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-user-cog mr-2"></i> Hồ sơ cá nhân Admin</h1>
    </div>

    <!-- Thông báo thành công -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow mb-4 border-0 rounded-lg">
                
                <!-- Card Header kèm nút Đăng xuất nhanh -->
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas id-card mr-2"></i> Thông tin tài khoản
                    </h6>
                    
                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger shadow-sm font-weight-bold" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?')">
                            <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                        </button>
                    </form>
                </div>

                <div class="card-body p-4">
                    
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Tên hiển thị -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Tên hiển thị Admin <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" placeholder="Nhập họ và tên..." required>
                            @error('name') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Email đăng nhập -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Email đăng nhập <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" placeholder="Nhập địa chỉ email..." required>
                            @error('email') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e3e6f0;">
                        
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="fas fa-lock mr-1"></i> Đổi mật khẩu <small class="text-muted font-italic">(Để trống nếu giữ nguyên mật khẩu cũ)</small>
                        </h6>

                        <!-- Mật khẩu mới -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu mới từ 6 ký tự...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password" title="Ẩn/Hiện mật khẩu">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password') 
                                <div class="text-danger small mt-1 font-weight-bold">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Xác nhận mật khẩu mới -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation" title="Ẩn/Hiện mật khẩu">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Nút lưu -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Lưu thay đổi hồ sơ
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Script ẩn/hiện mật khẩu -->
@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const targetInput = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endpush

@endsection