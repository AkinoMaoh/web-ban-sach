<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng Nhập Quản Trị - Hệ Thống</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            /* Ảnh nền cũ của admin */
            background-image: url("{{ asset('img/bg-admin.jpg') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            position: relative;
        }

        /* Lớp phủ mờ màu tối đậm chất Admin */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(15, 23, 42, 0.7); /* Phủ đen 70% */
            backdrop-filter: blur(8px); /* Làm nhòe ảnh nền 8px */
            -webkit-backdrop-filter: blur(8px);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1; /* Nổi lên trên lớp mờ */
        }

        .admin-login-card {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-top: 6px solid #dc3545; 
            position: relative;
        }
        
        .shield-icon {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 80px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: 0 10px 15px -3px rgba(220, 53, 69, 0.4);
            border: 4px solid #0f172a; /* Viền trùng với màu lớp mờ */
        }
        
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        .btn-admin {
            background-color: #dc3545;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-admin:hover {
            background-color: #bb2d3b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(220, 53, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center px-3">
        <div class="admin-login-card p-4 p-md-5 mt-5">
            
            <div class="shield-icon">
                <i class="fas fa-user-shield"></i>
            </div>

            <div class="text-center mt-4 mb-4">
                <h4 class="fw-bold text-dark">HỆ THỐNG QUẢN TRỊ</h4>
                <p class="text-muted small">Yêu cầu quyền truy cập cấp cao</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success small py-2">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary small">TÀI KHOẢN EMAIL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input id="email" class="form-control border-start-0 ps-0" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@example.com">
                    </div>
                    @error('email') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold text-secondary small">MẬT KHẨU</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input id="password" class="form-control border-start-0 border-end-0 px-0" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        <button class="btn btn-light border border-start-0 btn-toggle-password" type="button" data-target="#password">
                            <i class="fas fa-eye text-muted"></i>
                        </button>
                    </div>
                    @error('password') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 form-check">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label for="remember_me" class="form-check-label text-muted small">Ghi nhớ phiên đăng nhập</label>
                </div>

                <button class="btn btn-admin w-100 py-2 mb-3" type="submit">
                    <i class="fas fa-sign-in-alt me-2"></i> ĐĂNG NHẬP ADMIN
                </button>

                <div class="text-center mt-3">
                    <a class="text-decoration-none text-muted small" href="{{ route('admin.register') }}">
                        Tạo tài khoản quản trị mới?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = document.querySelector(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                    icon.classList.replace('text-muted', 'text-danger');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                    icon.classList.replace('text-danger', 'text-muted');
                }
            });
        });
    </script>
</body>
</html>