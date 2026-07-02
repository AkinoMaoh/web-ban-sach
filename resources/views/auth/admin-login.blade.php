<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        html, body, .min-h-screen {
            background-image: url("{{ asset('img/bg-admin.jpg') }}") !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            position: relative;
        }

        .min-h-screen::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 0;
        }

        .min-h-screen > div {
            position: relative;
            z-index: 10;
            backdrop-filter: blur(4px);
            background-color: rgba(255, 255, 255, 0.96) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>

    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wider">Đăng Nhập Quản Trị Viên</h2>
        <p class="text-xs text-gray-500 mt-1">Hệ thống quản lý Thư Viện Sách</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email Quản Trị" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />
            <div class="relative flex items-center mt-1">
                <x-text-input id="password" class="block w-full pr-12" type="password" name="password" required autocomplete="current-password" />
                <button type="button" class="btn-toggle-password absolute right-3 text-gray-500 hover:text-gray-700 transition focus:outline-none" data-target="#password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('admin.register') }}">
                Chưa có tài khoản Admin?
            </a>

            <x-primary-button class="ms-3 bg-red-600 hover:bg-red-700">
                Đăng Nhập Admin
            </x-primary-button>
        </div>
    </form>

    <script>
        document.querySelectorAll('.btn-toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = document.querySelector(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash'); // Đổi thành mắt gạch chéo
                    this.classList.remove('text-gray-500');
                    this.classList.add('text-red-600'); // Đổi màu icon cho nổi bật khi đang hiện
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.classList.remove('text-red-600');
                    this.classList.add('text-gray-500');
                }
            });
        });
    </script>
</x-guest-layout>