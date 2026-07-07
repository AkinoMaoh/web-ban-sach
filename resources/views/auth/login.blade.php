<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex border-b border-gray-200 mb-6">
        <button id="tab-email" type="button" class="w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-indigo-600 text-indigo-600 focus:outline-none">
            <i class="fas fa-envelope mr-2"></i>Email / Mật khẩu
        </button>
        <button id="tab-phone" type="button" class="w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none">
            <i class="fas fa-phone mr-2"></i>Số điện thoại OTP
        </button>
    </div>

    <form id="form-email-login" method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus autocomplete="username" placeholder="Nhập email của bạn..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />
            <div class="relative flex items-center mt-1">
                <x-text-input id="password" class="block w-full pr-12" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu..." />
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

        <div class="flex items-center justify-between mt-4">
            <div>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('register'))
                    <a class="underline text-sm hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('register') }}" style="color: #7fad39; font-weight: bold;">
                        Đăng ký?
                    </a>
                @endif
                <x-primary-button>Đăng nhập</x-primary-button>
            </div>
        </div>
    </form>

    <div id="form-phone-login" class="hidden">
        <div id="phone-input-block">
            <x-input-label for="txt-phone" value="Số điện thoại nhận OTP" />
            <x-text-input id="txt-phone" class="block mt-1 w-full" type="tel" placeholder="Ví dụ: 0987654321" />

            <button type="button" id="btn-send-sms" class="w-full mt-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-150">
                GỬI MÃ OTP
            </button>
        </div>

        <div id="otp-input-block" class="hidden">
            <x-input-label for="txt-otp" value="Nhập 6 số OTP từ tệp tin laravel.log" />
            <x-text-input id="txt-otp" class="block mt-1 w-full text-center font-bold tracking-widest text-xl" type="text" maxlength="6" placeholder="******" />
            
            <button type="button" id="btn-verify-otp" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-150">
                XÁC NHẬN ĐĂNG NHẬP
            </button>
        </div>
    </div>

    <div class="relative flex py-5 items-center">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="flex-shrink mx-4 text-gray-400 text-sm">Hoặc tiếp tục bằng</span>
        <div class="flex-grow border-t border-gray-300"></div>
    </div>

    <div class="mb-3">
        <form action="{{ route('google.login') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.5 24c0-1.61-.15-3.16-.42-4.69H24v8.89h12.62c-.54 2.85-2.15 5.27-4.57 6.9l7.1 5.51C43.32 36.36 46.5 30.73 46.5 24z"/>
                    <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.1-5.51c-1.97 1.32-4.51 2.11-8.79 2.11-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                <span>Tiếp tục với Google</span>
            </button>
        </form>
    </div>

    <script>
        // 1. CHUYỂN TAB MƯỢT MÀ
        const tabEmail = document.getElementById('tab-email');
        const tabPhone = document.getElementById('tab-phone');
        const formEmail = document.getElementById('form-email-login');
        const formPhone = document.getElementById('form-phone-login');

        tabEmail.addEventListener('click', () => {
            tabEmail.className = "w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-indigo-600 text-indigo-600 focus:outline-none";
            tabPhone.className = "w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none";
            formEmail.classList.remove('hidden');
            formPhone.classList.add('hidden');
        });

        tabPhone.addEventListener('click', () => {
            tabPhone.className = "w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-indigo-600 text-indigo-600 focus:outline-none";
            tabEmail.className = "w-1/2 pb-3 text-center font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none";
            formPhone.classList.remove('hidden');
            formEmail.classList.add('hidden');
        });

        // 2. GỬI SĐT LÊN CONTROLLER ĐỂ TỰ SINH OTP VÀO LOG
        document.getElementById('btn-send-sms').addEventListener('click', function() {
            let phoneInput = document.getElementById('txt-phone').value.trim();
            if(!phoneInput || phoneInput.length < 10) { alert('Số điện thoại không hợp lệ!'); return; }

            this.innerText = 'Đang xử lý...';
            this.disabled = true;

            fetch("{{ route('phone.sendOtp') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ phone: phoneInput })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.msg);
                    document.getElementById('phone-input-block').classList.add('hidden');
                    document.getElementById('otp-input-block').classList.remove('hidden');
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                    location.reload();
                }
            })
            .catch(err => {
                alert('Yêu cầu gửi OTP thất bại!');
                location.reload();
            });
        });

        // 3. GỬI MÃ OTP ĐỂ XÁC THỰC VÀ ĐĂNG NHẬP CỤC BỘ
        document.getElementById('btn-verify-otp').addEventListener('click', function() {
            const code = document.getElementById('txt-otp').value.trim();
            const phoneInput = document.getElementById('txt-phone').value.trim();
            if(code.length !== 6) { alert('Mã OTP gồm 6 chữ số!'); return; }

            this.innerText = 'Đang xác thực...';
            this.disabled = true;

            fetch("{{ route('login.phone.verify') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ otp: code, phone: phoneInput })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Mã OTP sai hoặc đã hết hạn!');
                    this.innerText = 'XÁC NHẬN ĐĂNG NHẬP';
                    this.disabled = false;
                }
            })
            .catch(err => {
                alert('Mã OTP không đúng hoặc hệ thống gặp sự cố!');
                location.reload();
            });
        });

        // 4. ẨN HIỆN MẬT KHẨU
        document.querySelector('.btn-toggle-password').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = "fas fa-eye-slash text-red-600";
            } else {
                input.type = 'password';
                icon.className = "fas fa-eye text-gray-500";
            }
        });
    </script>
</x-guest-layout>