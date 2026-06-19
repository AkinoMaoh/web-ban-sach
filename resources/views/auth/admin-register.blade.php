<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wider">Đăng Ký Tài Khoản Admin</h2>
        <p class="text-xs text-gray-500 mt-1">Tạo mới quyền quản trị hệ thống</p>
    </div>

    <form method="POST" action="{{ route('admin.register.submit') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Tên hiển thị Admin" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email làm việc" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('admin.login') }}">
                Đã có tài khoản Quản trị?
            </a>

            <x-primary-button class="ms-4 bg-red-600 hover:bg-red-700">
                Đăng Ký Admin
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>