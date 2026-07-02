<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Họ và Tên" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nhập họ và tên của bạn..." />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Nhập địa chỉ email..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />
            <div class="relative flex items-center mt-1">
                <x-text-input id="password" class="block w-full pr-12" type="password" name="password" required autocomplete="new-password" placeholder="Tạo mật khẩu (tối thiểu 8 ký tự)..." />
                <button type="button" class="btn-toggle-password absolute right-3 text-gray-500 hover:text-gray-700 transition focus:outline-none" data-target="#password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" />
            <div class="relative flex items-center mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-12" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu phía trên..." />
                <button type="button" class="btn-toggle-password absolute right-3 text-gray-500 hover:text-gray-700 transition focus:outline-none" data-target="#password_confirmation">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4" style="display: flex; justify-content: space-between; align-items: center;">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('login') }}">
                Bạn đã có tài khoản? <span style="color: #7fad39; font-weight: bold; text-decoration: underline;">Đăng nhập</span>
            </a>

            <x-primary-button class="ms-4">
                Đăng ký
            </x-primary-button>
        </div>
    </form>

    <script>
        document.querySelectorAll('.btn-toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
                // Lấy ô input mục tiêu dựa vào thuộc tính data-target
                const targetSelector = this.getAttribute('data-target');
                const input = document.querySelector(targetSelector);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash'); // Đổi thành mắt gạch chéo khi hiện chữ
                    this.classList.remove('text-gray-500');
                    this.classList.add('text-red-600');    // Chuyển sang màu đỏ nổi bật
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