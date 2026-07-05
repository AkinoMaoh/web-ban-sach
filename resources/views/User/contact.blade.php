@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Liên hệ</li>
            </ol>
        </nav>
    </div>
</div>

<section class="contact-section py-5 mb-5">
    <div class="container">
        <!-- Tiêu đề trang -->
        <div class="text-center mb-5">
            <h2 class="serif-font font-weight-bold" style="color: var(--text-main);">Liên Hệ Với SachHay</h2>
            <p class="text-muted" style="font-size: 16px;">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn mọi lúc, mọi nơi.</p>
        </div>

        <div class="row">
            <!-- CỘT TRÁI: Thông tin liên hệ & Bản đồ -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border h-100">
                    <h4 class="serif-font font-weight-bold mb-4 border-bottom pb-3">Thông tin liên hệ</h4>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-box d-flex align-items-center justify-content-center bg-light rounded-circle text-orange mr-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark">Địa chỉ cửa hàng</h6>
                            <p class="text-muted mb-0">123 Đường Sách, Lê Lợi, Hải Phòng</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="icon-box d-flex align-items-center justify-content-center bg-light rounded-circle text-orange mr-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fas fa-phone-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark">Điện thoại hỗ trợ</h6>
                            <p class="text-muted mb-0">0988.888.888 (8:00 - 22:00)</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="icon-box d-flex align-items-center justify-content-center bg-light rounded-circle text-orange mr-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark">Email</h6>
                            <p class="text-muted mb-0">hotro@sachhay.com</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <!-- Bản đồ nhúng Google Maps -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3728.3842601007923!2d106.68007201533214!3d20.85655748609139!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a7a8d56b0b5cd%3A0xb1514781440d9d20!2sL%C3%AA%20L%E1%BB%A3i%2C%20Ng%C3%B4%20Quy%E1%BB%81n%2C%20H%E1%BA%A3i%20Ph%C3%B2ng%2C%20Vietnam!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="100%" height="250" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: Form Gửi tin nhắn -->
            <div class="col-lg-7">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border h-100">
                    <h4 class="serif-font font-weight-bold mb-4 border-bottom pb-3">Gửi tin nhắn cho chúng tôi</h4>

                    <!-- Hiển thị thông báo thành công nếu có -->
                    @if(session('success'))
                        <div class="alert alert-success shadow-sm border-0 mb-4" style="border-left: 5px solid #28a745; border-radius: 6px;">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="#" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg custom-input" placeholder="Tên của bạn" required>
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg custom-input" placeholder="Email liên hệ" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control form-control-lg custom-input" placeholder="Bạn cần hỗ trợ vấn đề gì?" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Nội dung <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control custom-input" placeholder="Nhập chi tiết nội dung tin nhắn..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-orange rounded-pill px-5 py-3 font-weight-bold shadow-sm mt-2">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<style>
    /* CSS Làm đẹp Input dùng chung (Có thể tận dụng từ trang checkout) */
    .custom-input { border: 1px solid #E0E0E0; border-radius: 8px; font-size: 15px; transition: all 0.3s; }
    .custom-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(211,84,0,0.15); }
    .text-orange { color: var(--primary-color); }
</style>
@endpush