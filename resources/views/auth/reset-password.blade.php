<x-guest-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow p-4 p-md-5 w-100" style="max-width: 550px; border-radius: 12px;">
            <h3 class="fw-bold text-dark mb-2" style="font-family: serif;">Thiết Lập Mật Khẩu</h3>
            <p class="text-muted small mb-4">Đổi mật khẩu định kỳ để bảo vệ tài khoản tốt hơn.</p>

            <form action="{{ route('password.reset.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <div class="input-group">
                        <input id="password" class="form-control" type="password" name="password" placeholder="Nhập mật khẩu mới..." required />
                        <button type="button" class="btn btn-light border fw-bold text-secondary btn-toggle-view" style="font-size: 0.85rem;">HIỆN</button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="text-danger small mt-1" />
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu..." required />
                        <button type="button" class="btn btn-light border fw-bold text-secondary btn-toggle-view" style="font-size: 0.85rem;">HIỆN</button>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn fw-bold px-4 py-2" style="background-color: #ffbc00; color: #2d3748;">
                        <i class="fas fa-key me-2"></i> CẬP NHẬT MẬT KHẨU
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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