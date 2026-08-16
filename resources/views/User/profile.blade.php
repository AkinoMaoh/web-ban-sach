@extends('layout.user')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<div class="container py-5">
    <div class="row justify-content-center">
        
        <!-- CỘT TRÁI: SIDEBAR -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 16px; background: #fff;">
                <div class="mb-3 position-relative d-inline-block mx-auto">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=7fad39&color=fff&size=120" 
                         class="rounded-circle shadow-sm border border-white" style="width: 90px; height: 90px; object-fit: cover;">
                </div>
                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 1.1rem;">{{ Auth::user()->name }}</h6>
                <p class="text-muted small bg-light px-3 py-1 d-inline-block mx-auto rounded-pill mb-4"><i class="fas fa-shield-alt mr-1 text-[#2f4c39]"></i>Tài khoản thành viên</p>
                
                <div class="nav flex-column nav-pills text-left border-top pt-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active font-weight-bold mb-2 py-2.5 px-3 d-flex align-items-center" id="info-tab" data-toggle="pill" href="#pane-info" role="tab" style="border-radius: 10px;">
                        <i class="fas fa-user-edit mr-3" style="font-size: 1.1rem; width: 20px;"></i> Thông tin cá nhân
                    </a>
                    <a class="nav-link font-weight-bold mb-3 py-2.5 px-3 text-secondary d-flex align-items-center" id="pass-tab" data-toggle="pill" href="#pane-pass" role="tab" style="border-radius: 10px;">
                        <i class="fas fa-lock mr-3" style="font-size: 1.1rem; width: 20px;"></i> Đổi mật khẩu
                    </a>
                    <a class="nav-link font-weight-bold mb-3 py-2.5 px-3 text-secondary d-flex align-items-center" id="order-tab" href="{{ route('user.orderHistory') }}" style="border-radius: 10px; transition: 0.3s;">
                        <i class="fas fa-clipboard-list mr-3" style="font-size: 1.1rem; width: 20px;"></i> Lịch sử mua hàng
                    </a>
                    
                    <a class="nav-link font-weight-bold py-2.5 px-3 text-danger border border-danger text-center bg-white transition-all" 
                       href="#" style="border-radius: 10px; transition: 0.2s;"
                       onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn đăng xuất không?')) document.getElementById('user-logout-form').submit();"
                       onmouseover="this.style.backgroundColor='#fff5f5'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                    </a>
                </div>

                <form id="user-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- CỘT PHẢI: NỘI DUNG -->
        <div class="col-md-8 col-lg-8">
            <div id="alert-container"></div>
            
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #d4edda; color: #155724; padding: 15px;">
                    <i class="fas fa-check-circle mr-2" style="font-size: 1.2rem;"></i> <strong>{{ session('success') }}</strong>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <strong>Vui lòng kiểm tra lại:</strong>
                    <ul class="mb-0 mt-1 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="tab-content card border-0 shadow-sm p-4" style="border-radius: 16px; background: #fff;">
                
                <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                <div class="tab-pane fade show active" id="pane-info" role="tabpanel">
                    <div class="border-b pb-2 mb-4">
                        <h5 class="font-weight-bold text-dark mb-1">Hồ Sơ Của Tôi</h5>
                        <p class="text-muted small">Quản lý và cập nhật thông tin tài khoản để nhận sách chính xác.</p>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark small">Họ và Tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control border-left-0 @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required style="border-radius: 0 8px 8px 0; padding: 10px;" placeholder="Nhập họ và tên...">
                                </div>
                                @error('name') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                            </div>
                            
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark small">Email đăng nhập</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" id="user_email" name="email" class="form-control border-left-0 bg-light text-muted" value="{{ Auth::user()->email }}" readonly style="border-radius: 0 8px 8px 0; padding: 10px; cursor: not-allowed;">
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark small">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-phone-alt text-muted"></i></span>
                                    </div>
                                    <input type="tel" name="phone" class="form-control border-left-0 @error('phone') is-invalid @enderror" value="{{ old('phone', Auth::user()->phone ?? ($defaultAddress->receiver_phone ?? '')) }}" style="border-radius: 0 8px 8px 0; padding: 10px;" placeholder="Ví dụ: 0912345678">
                                </div>
                                @error('phone') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark small d-block">Giới tính</label>
                                <div class="d-flex align-items-center" style="height: 45px;">
                                    <div class="custom-control custom-radio custom-control-inline mr-4">
                                        <input type="radio" id="genderMale" name="gender" value="male" class="custom-control-input" {{ old('gender', Auth::user()->gender ?? '') === 'male' ? 'checked' : '' }}>
                                        <label class="custom-control-label text-secondary font-weight-normal" for="genderMale">Nam</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="genderFemale" name="gender" value="female" class="custom-control-input" {{ old('gender', Auth::user()->gender ?? '') === 'female' ? 'checked' : '' }}>
                                        <label class="custom-control-label text-secondary font-weight-normal" for="genderFemale">Nữ</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KHỐI ĐỊA CHỈ VỚI SELECT BOX -->
                        <div class="card bg-light border-0 mb-4 p-3" style="border-radius: 12px;">
                            <label class="font-weight-bold text-dark small mb-3 border-bottom pb-2">
                                <i class="fas fa-map-marked-alt text-success mr-1"></i> Địa chỉ nhận hàng mặc định
                            </label>
                            
                            <div class="row">
                                <!-- Tỉnh/Thành -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-muted small">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select name="province_id" id="province" class="form-control form-control-sm @error('province_id') is-invalid @enderror" style="border-radius: 6px;">
                                        <option value="">Chọn Tỉnh/Thành phố</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('province_id', $defaultAddress->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province_id') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                                </div>

                                <!-- Quận/Huyện -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-muted small">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="district_id" id="district" class="form-control form-control-sm @error('district_id') is-invalid @enderror" style="border-radius: 6px;">
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                    @error('district_id') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                                </div>

                                <!-- Phường/Xã -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-muted small">Phường/Xã <span class="text-danger">*</span></label>
                                    <select name="ward_code" id="ward" class="form-control form-control-sm @error('ward_code') is-invalid @enderror" style="border-radius: 6px;">
                                        <option value="">Chọn Phường/Xã</option>
                                    </select>
                                    @error('ward_code') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Số nhà, ngõ ngách -->
                            <div class="form-group mb-0">
                                <label class="text-muted small">Địa chỉ cụ thể (Số nhà, tên đường, ngõ/ngách...) <span class="text-danger">*</span></label>
                                <textarea name="specific_address" class="form-control @error('specific_address') is-invalid @enderror" rows="2" style="border-radius: 6px; resize: none;" placeholder="Ví dụ: Số 12, ngõ 1, đường Lê Lợi...">{{ old('specific_address', $defaultAddress->specific_address ?? '') }}</textarea>
                                @error('specific_address') <small class="text-danger font-weight-bold">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- CÀI ĐẶT GIAO DIỆN (SÁNG / TỐI) -->
                        <div class="form-group mb-4 p-3 bg-light rounded d-flex align-items-center justify-content-between" style="border: 1px solid #e9ecef;">
                            <div>
                                <label class="font-weight-bold text-dark small mb-0"><i class="fas fa-moon mr-1"></i> Chế độ tối (Dark Mode)</label>
                                <small class="text-muted d-block" style="font-size: 11px;">Bật giao diện tối cho toàn bộ trang web</small>
                            </div>
                            <label class="theme-switch mb-0">
                                <input type="checkbox" id="profileThemeSwitch">
                                <span class="theme-slider"></span>
                            </label>
                        </div>
                        
                        <div class="text-right">
                            <button type="submit" class="btn text-white font-weight-bold px-5 py-2.5 shadow-sm btn-save" style="border-radius: 8px; background-color: #2f4c39;">
                                <i class="fas fa-save mr-2"></i> LƯU THAY ĐỔI HỒ SƠ
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: ĐỔI MẬT KHẨU -->
                <div class="tab-pane fade" id="pane-pass" role="tabpanel">
                    <div class="border-b pb-2 mb-4">
                        <h5 class="font-weight-bold text-dark mb-1">Thiết Lập Mật Khẩu</h5>
                        <p class="text-muted small">Hệ thống sẽ gửi mã xác nhận về Email: <strong>{{ Auth::user()->email }}</strong> trước khi đổi.</p>
                    </div>
                    
                    <form action="{{ route('password.reset.update') }}" method="POST" id="password-change-form">
                        @csrf
                        <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                        <input type="hidden" name="email" value="{{ Auth::user()->email }}">

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small">Mật khẩu mới</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input id="pass_new" type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới..." style="border-radius: 8px; padding: 10px 60px 10px 12px; height: 45px;" required>
                                <button type="button" class="btn-eye" data-target="#pass_new" style="position: absolute; right: 10px; border: none; background: #e9ecef; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 6px; color: #495057;">HIỆN</button>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small">Xác nhận mật khẩu mới</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input id="pass_confirm" type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu..." style="border-radius: 8px; padding: 10px 60px 10px 12px; height: 45px;" required>
                                <button type="button" class="btn-eye" data-target="#pass_confirm" style="position: absolute; right: 10px; border: none; background: #e9ecef; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 6px; color: #495057;">HIỆN</button>
                            </div>
                        </div>

                        <div class="form-group mb-4" id="otp-group" style="display: none;">
                            <label class="font-weight-bold text-danger small">Mã xác nhận (OTP)</label>
                            <div class="input-group">
                                <input type="text" name="otp_code" id="otp_code" class="form-control font-weight-bold text-center text-dark" placeholder="Nhập 6 số mã xác nhận..." style="border-radius: 8px; height: 45px; letter-spacing: 4px; font-size: 1.1rem;">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end align-items-center" style="gap: 15px;">
                            <button type="button" class="btn btn-outline-secondary font-weight-bold px-4 py-2.5" id="btn-send-otp" style="border-radius: 8px;">
                                <i class="fas fa-paper-plane mr-2"></i> GỬI MÃ XÁC NHẬN
                            </button>

                            <button type="submit" class="btn btn-warning font-weight-bold px-5 py-2.5 shadow-sm text-dark" id="btn-submit-password" style="border-radius: 8px; background-color: #ffc107; border:none;" disabled>
                                <i class="fas fa-key mr-2"></i> CẬP NHẬT MẬT KHẨU
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    #v-pills-tab .nav-link.active { background-color: #f1f8e9 !important; color: #2f4c39 !important; }
    #v-pills-tab .nav-link:hover:not(.active):not(.text-danger) { background-color: #f8f9fa; color: #000; }
    .btn-save:hover { background-color: #1f332a !important; }
    .form-control:focus { border-color: #2f4c39 !important; box-shadow: 0 0 0 0.2rem rgba(127, 173, 57, 0.25) !important; }
    .input-group-text { border-color: #ced4da; background-color: #f8f9fa; }

    /* SWITCH PHONG CÁCH IPHONE */
    .theme-switch { position: relative; display: inline-block; width: 46px; height: 26px; margin-bottom: 0; flex-shrink: 0; }
    .theme-switch input { opacity: 0; width: 0; height: 0; }
    .theme-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ffffff; transition: .25s ease; border-radius: 26px; border: 2px solid #000000; }
    .theme-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: #000000; transition: .25s ease; border-radius: 50%; }
    input:checked + .theme-slider { background-color: #000000; border-color: #ffffff; }
    input:checked + .theme-slider:before { transform: translateX(20px); background-color: #ffffff; }
</style>

<script>
    // XỬ LÝ THEME DARK MODE
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.documentElement;
        const themeSwitch = document.getElementById('profileThemeSwitch');
        
        if (localStorage.getItem("theme") === "dark") {
            root.classList.add("dark-mode");
            if(themeSwitch) themeSwitch.checked = true;
        } else {
            root.classList.remove("dark-mode");
            if(themeSwitch) themeSwitch.checked = false;
        }

        if(themeSwitch) {
            themeSwitch.addEventListener('change', function() {
                if (this.checked) {
                    root.classList.add("dark-mode");
                    localStorage.setItem("theme", "dark");
                } else {
                    root.classList.remove("dark-mode");
                    localStorage.setItem("theme", "light");
                }
            });
        }
    });

    // ẨN / HIỆN MẬT KHẨU
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

    $(document).ready(function() {
        // CHUYỂN TAB BOOTSTRAP
        $('#v-pills-tab a[data-toggle="pill"]').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // ==========================================
        // XỬ LÝ AJAX LẤY ĐỊA CHỈ & GIỮ DỮ LIỆU ĐÃ LƯU
        // ==========================================
        let savedDistrict = "{{ old('district_id', $defaultAddress->district_id ?? '') }}";
        let savedWard = "{{ old('ward_code', $defaultAddress->ward_code ?? '') }}";

        function loadDistricts(province_id, selected_district = null) {
            if (province_id) {
                $('#district').html('<option value="">Đang tải...</option>');
                
                $.ajax({
                    url: '/api/locations/districts/' + province_id,
                    type: 'GET',
                    success: function(data) {
                        let html = '<option value="">Chọn Quận/Huyện</option>';
                        $.each(data, function(index, item) {
                            let selected = (item.id == selected_district) ? 'selected' : '';
                            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                        });
                        $('#district').html(html);
                        
                        // Nếu có quận cũ/đang chọn thì tự động tải tiếp phường/xã
                        if (selected_district) {
                            loadWards(selected_district, savedWard);
                        } else {
                            $('#ward').html('<option value="">Chọn Phường/Xã</option>');
                        }
                    },
                    error: function() {
                        $('#district').html('<option value="">Lỗi kết nối</option>');
                    }
                });
            } else {
                $('#district').html('<option value="">Chọn Quận/Huyện</option>');
                $('#ward').html('<option value="">Chọn Phường/Xã</option>');
            }
        }

        function loadWards(district_id, selected_ward = null) {
            if (district_id) {
                $('#ward').html('<option value="">Đang tải...</option>');
                
                $.ajax({
                    url: '/api/locations/wards/' + district_id,
                    type: 'GET',
                    success: function(data) {
                        let html = '<option value="">Chọn Phường/Xã</option>';
                        $.each(data, function(index, item) {
                            let selected = (item.code == selected_ward) ? 'selected' : '';
                            html += `<option value="${item.code}" ${selected}>${item.name}</option>`;
                        });
                        $('#ward').html(html);
                    },
                    error: function() {
                        $('#ward').html('<option value="">Lỗi kết nối</option>');
                    }
                });
            } else {
                $('#ward').html('<option value="">Chọn Phường/Xã</option>');
            }
        }

        // Sự kiện khi người dùng đổi Tỉnh/Thành
        $('#province').change(function() {
            savedDistrict = null; // Xóa cache quận cũ
            savedWard = null;     // Xóa cache xã cũ
            loadDistricts($(this).val());
        });

        // Sự kiện khi người dùng đổi Quận/Huyện
        $('#district').change(function() {
            savedWard = null;     // Xóa cache xã cũ
            loadWards($(this).val());
        });

        // TỰ ĐỘNG CHẠY KHI VÀO TRANG: Nạp dữ liệu cũ đã lưu từ DB
        let initialProvince = $('#province').val();
        if (initialProvince) {
            loadDistricts(initialProvince, savedDistrict);
        }
    });
</script>
@endsection