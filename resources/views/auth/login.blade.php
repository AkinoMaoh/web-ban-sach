<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Nhập email của bạn..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />

            <div class="relative flex items-center mt-1">
                <x-text-input id="password" class="block w-full pr-12"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Nhập mật khẩu..." />
                
                <button type="button" class="btn-toggle-password absolute right-3 text-gray-500 hover:text-gray-700 transition focus:outline-none">
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

        <div class="flex items-center justify-between mt-4" style="display: flex; justify-content: space-between; align-items: center;">
            
            <div>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 15px;">
                @if (Route::has('register'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('register') }}" style="color: #7fad39; font-weight: bold; text-decoration: underline;">
                        Đăng ký tài khoản?
                    </a>
                @endif

                <x-primary-button>
                    Đăng nhập
                </x-primary-button>
            </div>

        </div>
    </form>

    <script>
        document.querySelector('.btn-toggle-password').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash'); // Đổi sang biểu tượng mắt gạch chéo
                this.classList.remove('text-gray-500');
                this.classList.add('text-red-600');    // Chuyển màu đỏ nổi bật khi hiển thị mật khẩu
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.classList.remove('text-red-600');
                this.classList.add('text-gray-500');
            }
        });
    </script>
</x-guest-layout>