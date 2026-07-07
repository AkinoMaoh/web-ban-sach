<x-guest-layout>
    <div class="p-6 bg-white rounded-lg shadow-md max-w-md mx-auto mt-10">
        <h2 class="text-xl font-bold text-center mb-4" style="color: #4a5568;">XÁC THỰC ĐỔI MẬT KHẨU</h2>
        <p class="text-gray-500 text-sm text-center mb-6">Hệ thống sẽ gửi mã xác thực gồm 6 chữ số tới hòm thư cá nhân đã đăng ký của bạn.</p>

        <div id="block-send">
            <div class="mb-4">
                <x-input-label value="Tài khoản Email xác nhận" />
                <x-text-input class="block mt-1 w-full bg-gray-100" type="text" value="{{ Auth::user()->email }}" disabled />
            </div>
            <button type="button" id="btn-send-mail-otp" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg transition">
                GỬI MÃ XÁC THỰC VỀ EMAIL
            </button>
        </div>

        <div id="block-verify" class="hidden mt-4">
            <div class="mb-4">
                <x-input-label for="input-otp" value="Nhập 6 số mã xác thực nhận được" />
                <x-text-input id="input-otp" class="block mt-1 w-full text-center font-bold text-xl tracking-widest" type="text" maxlength="6" placeholder="******" />
            </div>
            <button type="button" id="btn-confirm-otp" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg transition">
                XÁC NHẬN MÃ ĐỂ TIẾP TỤC
            </button>
        </div>
    </div>

    <script>
        // Xử lý gửi mã xác thực về Email
        document.getElementById('btn-send-mail-otp').addEventListener('click', function() {
            this.innerText = 'Đang gửi mã...';
            this.disabled = true;

            fetch("{{ route('password.verify.send') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.msg);
                    document.getElementById('block-send').classList.add('hidden');
                    document.getElementById('block-verify').classList.remove('hidden');
                } else {
                    alert(data.msg);
                    this.innerText = 'GỬI MÃ XÁC THỰC VỀ EMAIL';
                    this.disabled = false;
                }
            });
        });

        // Xử lý kiểm tra mã xác thực để điều hướng
        document.getElementById('btn-confirm-otp').addEventListener('click', function() {
            const code = document.getElementById('input-otp').value.trim();
            if(code.length !== 6) { alert('Mã OTP phải có đúng 6 chữ số!'); return; }

            this.innerText = 'Đang kiểm tra...';
            this.disabled = true;

            fetch("{{ route('password.verify.match') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ otp: code })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.href = data.redirect; // Đưa về trang điền mật khẩu mới nếu đúng mã
                } else {
                    alert(data.message);
                    this.innerText = 'XÁC NHẬN MÃ ĐỂ TIẾP TỤC';
                    this.disabled = false;
                }
            });
        });
    </script>
</x-guest-layout>