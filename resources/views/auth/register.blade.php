<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4 p-md-5 w-100" style="max-width: 500px; border-radius: 12px;">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark mb-1">Đăng Ký Tài Khoản</h4>
                <p class="text-muted small">Tạo tài khoản mới để trải nghiệm dịch vụ</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Họ và Tên</label>
                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nhập họ và tên của bạn...">
                    <x-input-error :messages="$errors->get('name')" class="text-danger small mt-1" />
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Nhập địa chỉ email...">
                    <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                    <div class="input-group">
                        <input id="password" class="form-control border-end-0" type="password" name="password" required autocomplete="new-password" placeholder="Tạo mật khẩu (tối thiểu 8 ký tự)...">
                        <button class="btn btn-outline-secondary border-start-0 btn-toggle-password" type="button" data-target="#password">
                            <i class="fas fa-eye text-muted"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="text-danger small mt-1" />
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <input id="password_confirmation" class="form-control border-end-0" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu phía trên...">
                        <button class="btn btn-outline-secondary border-start-0 btn-toggle-password" type="button" data-target="#password_confirmation">
                            <i class="fas fa-eye text-muted"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-danger small mt-1" />
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <a class="text-decoration-none text-muted small" href="{{ route('login') }}">
                        Bạn đã có tài khoản? <span class="fw-bold text-decoration-underline" style="color: #7fad39;">Đăng nhập</span>
                    </a>
                    <button type="submit" class="btn btn-primary px-4 w-100 w-sm-auto">
                        Đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetSelector = this.getAttribute('data-target');
                const input = document.querySelector(targetSelector);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye', 'text-muted');
                    icon.classList.add('fa-eye-slash', 'text-danger');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash', 'text-danger');
                    icon.classList.add('fa-eye', 'text-muted');
                }
            });
        });
    </script>
</x-guest-layout>