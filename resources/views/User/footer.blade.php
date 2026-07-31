<footer class="modern-footer pt-5 pb-3 transition-base">
    <div class="container">
        <div class="row pb-4">
            <!-- Cột 1: Logo & Giới thiệu -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h2 class="serif-font mb-3 font-weight-bold" style="color: var(--primary-color, #D35400); letter-spacing: -0.5px; font-size: 26px;">SachHay.</h2>
                <p class="text-muted text-justify pr-md-3" style="font-size: 14px; line-height: 1.6;">
                    Khám phá thế giới qua từng trang sách. Chúng tôi mang đến cho bạn những đầu sách tuyệt vời nhất, nuôi dưỡng tri thức và tâm hồn mỗi ngày.
                </p>
                <div class="social-icons d-flex align-items-center mt-3" style="gap: 12px;">
                    <a href="#" class="footer-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-btn" title="Youtube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Cột 2: Liên kết hữu ích -->
            <div class="col-lg-4 col-md-6 mb-4 pl-lg-5">
                <h5 class="serif-font font-weight-bold mb-3 footer-heading" style="font-size: 16px;">Liên kết hữu ích</h5>
                <ul class="list-unstyled footer-links" style="font-size: 14px;">
                    <li class="mb-2.5"><a href="#" class="text-decoration-none">Về chúng tôi</a></li>
                    <li class="mb-2.5"><a href="#" class="text-decoration-none">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2.5"><a href="#" class="text-decoration-none">Chính sách bảo mật</a></li>
                    <li class="mb-2.5"><a href="#" class="text-decoration-none">Điều khoản dịch vụ</a></li>
                </ul>
            </div>
            
            <!-- Cột 3: Thông tin liên hệ -->
            <div class="col-lg-4 col-md-12 mb-4">
                <h5 class="serif-font font-weight-bold mb-3 footer-heading" style="font-size: 16px;">Liên hệ với chúng tôi</h5>
                <ul class="list-unstyled text-muted" style="font-size: 14px; line-height: 1.8;">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3 footer-icon-color"></i> 
                        <span>123 Đường Sách, Ngô Quyền, Hải Phòng</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="fas fa-phone mr-3 footer-icon-color"></i> 
                        <span>0988 888 888</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="fas fa-envelope mr-3 footer-icon-color"></i> 
                        <span>hotro@sachhay.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bản quyền -->
        <div class="text-center py-3 border-top copyright-section" style="font-size: 13px;">
            &copy; {{ date('Y') }} <strong>SachHay.</strong> All rights reserved.
        </div>
    </div>
</footer>

<style>
    .modern-footer {
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        color: #6c757d;
    }

    .footer-heading {
        color: #2c3e50;
    }

    .footer-links a {
        color: #6c757d;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: var(--primary-color, #D35400);
        transform: translateX(4px);
    }

    .footer-icon-color {
        color: var(--primary-color, #D35400);
        width: 16px;
        text-align: center;
    }

    /* Nút mạng xã hội tròn trịa, hiện đại */
    .footer-social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .footer-social-btn:hover {
        background-color: var(--primary-color, #D35400);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(211, 84, 0, 0.25);
    }

    .copyright-section {
        border-color: rgba(0, 0, 0, 0.05) !important;
        color: #adb5bd;
    }

    /* --- DARK MODE CHO FOOTER --- */
    html.dark-mode .modern-footer,
    body.dark-mode .modern-footer,
    .dark-mode .modern-footer {
        background-color: #161a1d !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #a0aec0 !important;
    }

    html.dark-mode .footer-heading,
    body.dark-mode .footer-heading,
    .dark-mode .footer-heading {
        color: #f7fafc !important;
    }

    html.dark-mode .footer-links a,
    body.dark-mode .footer-links a,
    .dark-mode .footer-links a {
        color: #a0aec0 !important;
    }

    html.dark-mode .footer-links a:hover,
    body.dark-mode .footer-links a:hover,
    .dark-mode .footer-links a:hover {
        color: #ff9900 !important;
    }

    html.dark-mode .footer-social-btn,
    body.dark-mode .footer-social-btn,
    .dark-mode .footer-social-btn {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #e2e8f0 !important;
    }

    html.dark-mode .footer-social-btn:hover,
    body.dark-mode .footer-social-btn:hover,
    .dark-mode .footer-social-btn:hover {
        background-color: #D35400 !important;
        color: #fff !important;
    }

    html.dark-mode .copyright-section,
    body.dark-mode .copyright-section,
    .dark-mode .copyright-section {
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #718096 !important;
    }
</style>