@extends('Admin.layout') @section('admin_content') <div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white font-weight-bold">
                    <i class="fas fa-user-cog"></i> THÔNG TIN TÀI KHOẢN ADMIN
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-gray-800">Tên hiển thị Admin</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" placeholder="Nhập họ và tên">
                            @error('name') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-gray-800">Email đăng nhập</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" placeholder="Nhập địa chỉ email">
                            @error('email') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #ced4da;">
                        <h6 class="text-primary font-weight-bold mb-3">
                            <i class="fas fa-lock"></i> ĐỔI MẬT KHẨU (Để trống nếu giữ nguyên mật khẩu cũ)
                        </h6>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-gray-800">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu mới từ 6 ký tự">
                            @error('password') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-800">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 font-weight-bold py-2 shadow-sm">
                            <i class="fas fa-save"></i> LƯU THAY ĐỔI HỒ SƠ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection