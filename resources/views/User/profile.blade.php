@extends('layout.user')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="container py-5" style="background-color: #f8f9fa; min-height: 75vh;">
    <div class="row justify-content-center">
        
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px; background: #fff;">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=7fad39&color=fff&size=100" 
                         class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                </div>
                <h6 class="font-weight-bold text-dark mb-1">{{ Auth::user()->name }}</h6>
                <p class="text-muted small mb-3">Tài khoản thành viên</p>
                
                <div class="nav flex-column nav-pills text-left border-top pt-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active font-weight-bold mb-2 py-2.5 px-3" id="info-tab" data-toggle="pill" href="#pane-info" role="tab" style="border-radius: 8px;">
                        <i class="fas fa-user-edit mr-2"></i> Thông tin cá nhân
                    </a>
                    <a class="nav-link font-weight-bold mb-3 py-2.5 px-3 text-secondary" id="pass-tab" data-toggle="pill" href="#pane-pass" role="tab" style="border-radius: 8px;">
                        <i class="fas fa-lock mr-2"></i> Đổi mật khẩu
                    </a>
                    
                    <a class="nav-link font-weight-bold py-2.5 px-3 text-white text-center bg-danger shadow-sm" 
                       href="#" style="border-radius: 8px; background-color: #dc3545 !important;"
                       onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn đăng xuất không?')) document.getElementById('user-logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                    </a>
                </div>

                <form id="user-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <div class="col-md-8 col-lg-7">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 8px; background-color: #d4edda; color: #155724;">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="tab-content card border-0 shadow-sm p-4" style="border-radius: 15px; background: #fff;">
                
                <div class="tab-pane fade show active" id="pane-info" role="tabpanel">
                    <h5 class="font-weight-bold text-dark mb-1">Hồ Sơ Của Tôi</h5>
                    <p class="text-muted small mb-4">Quản lý và cập nhật thông tin tài khoản mua hàng</p>
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-secondary small">Họ và Tên</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required style="border-radius: 8px; padding: 10px;">
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-secondary small">Email đăng nhập</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required style="border-radius: 8px; padding: 10px;">
                        </div>
                        <button type="submit" class="btn text-white font-weight-bold w-100 py-2.5" style="border-radius: 8px; background-color: #7fad39;">LƯU THAY ĐỔI HỒ SƠ</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="pane-pass" role="tabpanel">
                    <h5 class="font-weight-bold text-dark mb-1">Thiết Lập Mật Khẩu</h5>
                    <p class="text-muted small mb-4">Để trống nếu bạn không muốn thay đổi mật khẩu cũ</p>
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                        <input type="hidden" name="email" value="{{ Auth::user()->email }}">

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-secondary small">Mật khẩu mới</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input id="pass_new" type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới..." style="border-radius: 8px; padding: 10px 60px 10px 12px;">
                                <button type="button" class="btn-eye" data-target="#pass_new" style="position: absolute; right: 10px; border: none; background: #e9ecef; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 4px; color: #495057;">HIỆN</button>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-secondary small">Xác nhận mật khẩu mới</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input id="pass_confirm" type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu..." style="border-radius: 8px; padding: 10px 60px 10px 12px;">
                                <button type="button" class="btn-eye" data-target="#pass_confirm" style="position: absolute; right: 10px; border: none; background: #e9ecef; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 4px; color: #495057;">HIỆN</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning font-weight-bold w-100 py-2.5 text-dark" style="border-radius: 8px;">CẬP NHẬT MẬT KHẨU</button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    #v-pills-tab .nav-link.active { background-color: #f1f8e9 !important; color: #7fad39 !important; }
    #v-pills-tab .nav-link:hover:not(.active):not(.bg-danger) { background-color: #f8f9fa; color: #000; }
</style>

<script>
    document.querySelectorAll('.btn-eye').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.querySelector(this.getAttribute('data-target'));
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'ẨN';
                this.style.backgroundColor = '#f8d7da';
                this.style.color = '#721c24';
            } else {
                input.type = 'password';
                this.textContent = 'HIỆN';
                this.style.backgroundColor = '#e9ecef';
                this.style.color = '#495057';
            }
        });
    });
</script>
@endsection