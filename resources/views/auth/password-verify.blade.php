<x-guest-layout>
    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4 w-100" style="max-width: 450px; border-radius: 12px;">
            <h4 class="fw-bold text-center mb-3 text-secondary">XÁC THỰC ĐỔI MẬT KHẨU</h4>
            <p class="text-muted small text-center mb-4">Hệ thống sẽ gửi mã xác thực gồm 6 chữ số tới hòm thư cá nhân đã đăng ký của bạn.</p>

            <div id="block-send">
                <div class="mb-4">
                    <label class="form-label">Tài khoản Email xác nhận</label>
                    <input class="form-control bg-light" type="text" value="{{ Auth::user()->email }}" disabled />
                </div>
                <button type="button" id="btn-send-mail-otp" class="btn btn-success w-100 fw-bold py-2">
                    GỬI MÃ XÁC THỰC VỀ EMAIL
                </button>
            </div>

            <div id="block-verify" class="d-none mt-4">
                <div class="mb-4">
                    <label for="input-otp" class="form-label">Nhập 6 số mã xác thực nhận được</label>
                    <input id="input-otp" class="form-control text-center fw-bold fs-4" type="text" maxlength="6" placeholder="******" style="letter-spacing: 5px;" />
                </div>
                <button type="button" id="btn-confirm-otp" class="btn btn-primary w-100 fw-bold py-2">
                    XÁC NHẬN MÃ ĐỂ TIẾP TỤC
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-send-mail-otp').addEventListener('click', function() {
            this.innerText = 'Đang gửi mã...';
            this.disabled = true;

            fetch("{{ route('password.verify.send') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    alert(data.msg);
                    document.getElementById('block-send').classList.add('d-none');
                    document.getElementById('block-verify').classList.remove('d-none');
                } else {
                    alert(data.msg);
                    this.innerText = 'GỬI MÃ XÁC THỰC VỀ EMAIL';
                    this.disabled = false;
                }
            });
        });

        document.getElementById('btn-confirm-otp').addEventListener('click', function() {
            const code = document.getElementById('input-otp').value.trim();
            if(code.length !== 6) { alert('Mã OTP phải có đúng 6 chữ số!'); return; }
            this.innerText = 'Đang kiểm tra...';
            this.disabled = true;

            fetch("{{ route('password.verify.match') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ otp: code })
            }).then(res => res.json()).then(data => {
                if(data.success) { window.location.href = data.redirect; }
                else {
                    alert(data.message);
                    this.innerText = 'XÁC NHẬN MÃ ĐỂ TIẾP TỤC';
                    this.disabled = false;
                }
            });
        });
    </script>
</x-guest-layout>