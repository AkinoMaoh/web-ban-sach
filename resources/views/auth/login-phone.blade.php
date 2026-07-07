@extends('layout.user')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="container py-5" style="background-color: #f4f6f9; min-height: 75vh;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg p-4" style="border-radius: 20px; background: #fff;">
                <div class="text-center mb-4">
                    <h4 class="font-weight-bold text-dark mb-1">Xin Chào</h4>
                    <p class="text-muted small">Đăng nhập hoặc Tạo tài khoản mới siêu tốc</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 border-0 small" style="border-radius: 8px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div id="phone-section">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Số Điện Thoại</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-phone text-muted"></i></span>
                            </div>
                            <input type="tel" id="phone_input" class="form-control border-left-0" style="border-radius: 0 8px 8px 0; height: 45px;" placeholder="Nhập số điện thoại của bạn...">
                        </div>
                    </div>
                    <button type="button" id="btn-send-otp" class="btn text-white font-weight-bold w-100 py-2.5 mb-3" style="border-radius: 8px; background-color: #7fad39;">
                        TIẾP TỤC
                    </button>
                </div>

                <form id="otp-section" action="{{ route('phone.verifyLogin') }}" method="POST" class="d-none">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Nhập mã xác thực OTP</label>
                        <p class="text-muted text-xs mb-2">Mã gồm 6 chữ số vừa được gửi về SĐT của bạn.</p>
                        <input type="text" name="otp" class="form-control text-center font-weight-bold" maxlength="6" style="border-radius: 8px; height: 45px; font-size: 1.3rem; letter-spacing: 5px;" placeholder="******" required>
                    </div>
                    <button type="submit" class="btn btn-primary font-weight-bold w-100 py-2.5 mb-3" style="border-radius: 8px;">
                        XÁC NHẬN ĐĂNG NHẬP
                    </button>
                </form>

                <div class="position-relative text-center my-4">
                    <hr class="w-100">
                    <span class="position-absolute px-3 bg-white text-muted small" style="top: -12px; left: 50%; transform: translateX(-50%);">Hoặc tiếp tục bằng</span>
                </div>

                <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100 font-weight-bold py-2.5 d-flex align-items-center justify-content-center" style="border-radius: 8px; border-color: #ddd;">
                    <img src="https://lh3.googleusercontent.com/COxit9g9b1wFoQ95asIqCYAmeYgoGQ1thXWwDYCYUqYAnJa61g9bTOVMf50M85GPwS7w" style="width: 20px; class='mr-2';" class="mr-2">
                    Đăng nhập bằng Google
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // AJAX xử lý bấm nút gửi OTP không bị load lại trang giống website xịn
    document.getElementById('btn-send-otp').addEventListener('click', function() {
        const phone = document.getElementById('phone_input').value;
        if(!phone || phone.length < 10) {
            alert('Vui lòng nhập số điện thoại hợp lệ!');
            return;
        }

        this.innerText = 'ĐANG GỬI...';
        this.disabled = true;

        fetch("{{ route('phone.sendOtp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.msg);
                // Ẩn thớt nhập SĐT, hiện thớt nhập OTP lên
                document.getElementById('phone-section').classList.add('d-none');
                document.getElementById('otp-section').classList.remove('d-none');
            }
        })
        .catch(err => {
            alert('Có lỗi xảy ra, vui lòng thử lại!');
            this.innerText = 'TIẾP TỤC';
            this.disabled = false;
        });
    });
</script>
@endsection