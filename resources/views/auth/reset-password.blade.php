<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="p-8 bg-white rounded-lg shadow-md max-w-xl mx-auto mt-10">
        <h2 class="text-2xl font-bold text-gray-800" style="font-family: serif;">Thiết Lập Mật Khẩu</h2>
        <p class="text-gray-500 text-sm mb-6">Đổi mật khẩu định kỳ để bảo vệ tài khoản tốt hơn.</p>

        <form action="{{ route('password.reset.update') }}" method="POST">
            @csrf

            <div class="mb-4">
                <x-input-label for="password" value="Mật khẩu mới" />
                <div class="relative flex items-center mt-1">
                    <x-text-input id="password" class="block w-full pr-14" type="password" name="password" placeholder="Nhập mật khẩu mới..." required />
                    <button type="button" class="btn-toggle-view absolute right-3 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600 rounded border border-gray-300 transition">HIỆN</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="password_confirmation" value="Xác nhận mật khẩu mới" />
                <div class="relative flex items-center mt-1">
                    <x-text-input id="password_confirmation" class="block w-full pr-14" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu..." required />
                    <button type="button" class="btn-toggle-view absolute right-3 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-600 rounded border border-gray-300 transition">HIỆN</button>
                </div>
            </div>

            <button type="submit" class="w-full md:w-auto float-right flex items-center justify-center gap-2 font-bold py-3 px-6 rounded-lg transition duration-150 shadow-sm" style="background-color: #ffbc00; color: #2d3748;">
                <i class="fas fa-key"></i> CẬP NHẬT MẬT KHẨU
            </button>
            <div class="clear-both"></div>
        </form>
    </div>

    <script>
        // Xử lý nút Ẩn / Hiện mật khẩu giống hệt thiết kế chữ "HIỆN" của bạn
        document.querySelectorAll('.btn-toggle-view').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if(input.type === 'password') {
                    input.type = 'text';
                    this.innerText = 'ẨN';
                } else {
                    input.type = 'password';
                    this.innerText = 'HIỆN';
                }
            });
        });
    </script>
</x-guest-layout>