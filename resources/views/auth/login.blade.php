<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4 w-100" style="max-width: 450px; border-radius: 12px;">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <ul class="nav nav-tabs nav-justified mb-4" id="loginTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-primary fw-bold" id="tab-email" type="button" role="tab" style="border-bottom: 2px solid #0d6efd;">
                        <i class="fas fa-envelope me-2"></i>Email / Mật khẩu
                    </button>
                </li>
            </ul>

            <form id="form-email-login" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username" placeholder="Nhập email của bạn...">
                    <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                    <div class="input-group">
                        <input id="password" class="form-control border-end-0" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu...">
                        <!-- Đã sửa: Đảm bảo có type="button" và class btn-toggle-password -->
                        <button class="btn btn-outline-secondary border-start-0 btn-toggle-password" type="button" id="togglePasswordBtn">
                            <i class="fas fa-eye text-muted"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="text-danger small mt-1" />
                </div>

                <div class="mb-3 form-check">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label for="remember_me" class="form-check-label text-muted small">Ghi nhớ đăng nhập</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none text-muted small" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        @if (Route::has('register'))
                            <a class="text-decoration-none small fw-bold" href="{{ route('register') }}" style="color: #7fad39;">Đăng ký?</a>
                        @endif
                        <button class="btn btn-primary px-4" type="submit">Đăng nhập</button>
                    </div>
                </div>
            </form>

            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="mx-3 text-muted small">Hoặc tiếp tục bằng</span>
                <hr class="flex-grow-1">
            </div>

            <form action="{{ route('google.login') }}" method="POST" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100 fw-semibold d-flex justify-content-center align-items-center gap-2 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.5 24c0-1.61-.15-3.16-.42-4.69H24v8.89h12.62c-.54 2.85-2.15 5.27-4.57 6.9l7.1 5.51C43.32 36.36 46.5 30.73 46.5 24z"/>
                        <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.1-5.51c-1.97 1.32-4.51 2.11-8.79 2.11-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    Tiếp tục với Google
                </button>
            </form>
        </div>
    </div>

    <script>
        // Xử lý ẩn / hiện mật khẩu (Đã tối ưu hóa kiểm tra element tồn tại)
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.btn-toggle-password');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault(); // Tránh submit form
                    const input = document.getElementById('password');
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = "fas fa-eye-slash text-danger";
                    } else {
                        input.type = 'password';
                        icon.className = "fas fa-eye text-muted";
                    }
                });
            }
        });

        // Xử lý chuyển Tab (kiểm tra tồn tại trước khi addEventListener tránh lỗi Console)
        const tabEmail = document.getElementById('tab-email');
        if (tabEmail) {
            tabEmail.addEventListener('click', function() {
                this.classList.add('active', 'text-primary');
                this.classList.remove('text-muted');
                this.style.borderBottomColor = '#0d6efd';
                
                const phoneTab = document.getElementById('tab-phone');
                if (phoneTab) {
                    phoneTab.classList.remove('active', 'text-primary');
                    phoneTab.classList.add('text-muted');
                    phoneTab.style.borderBottomColor = 'transparent';
                }
                
                const emailForm = document.getElementById('form-email-login');
                const phoneForm = document.getElementById('form-phone-login');
                if (emailForm) emailForm.classList.remove('d-none');
                if (phoneForm) phoneForm.classList.add('d-none');
            });
        }

        const tabPhone = document.getElementById('tab-phone');
        if (tabPhone) {
            tabPhone.addEventListener('click', function() {
                this.classList.add('active', 'text-primary');
                this.classList.remove('text-muted');
                this.style.borderBottomColor = '#0d6efd';
                
                const emailTab = document.getElementById('tab-email');
                if (emailTab) {
                    emailTab.classList.remove('active', 'text-primary');
                    emailTab.classList.add('text-muted');
                    emailTab.style.borderBottomColor = 'transparent';
                }
                
                const phoneForm = document.getElementById('form-phone-login');
                const emailForm = document.getElementById('form-email-login');
                if (phoneForm) phoneForm.classList.remove('d-none');
                if (emailForm) emailForm.classList.add('d-none');
            });
        }
    </script>
</x-guest-layout>