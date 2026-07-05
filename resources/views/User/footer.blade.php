<footer class="modern-footer">
    <div class="container">
        <div class="row pb-4">
            <div class="col-md-4 mb-4">
                <h3 class="serif-font" style="color: var(--primary-color);">SachHay.</h3>
                <p class="text-muted mt-3 text-justify">Khám phá thế giới qua từng trang sách. Chúng tôi mang đến cho bạn những đầu sách tuyệt vời nhất.</p>
                <div>
                    <a href="#" class="text-muted mr-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5 class="serif-font font-weight-bold mb-3">Liên kết hữu ích</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Về chúng tôi</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Bảo mật thông tin</a></li>
                </ul>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5 class="serif-font font-weight-bold mb-3">Liên hệ</h5>
                <p class="text-muted mb-2"><i class="fas fa-map-marker-alt mr-2 text-dark"></i> 123 Đường Sách, Hải Phòng</p>
                <p class="text-muted mb-2"><i class="fas fa-phone mr-2 text-dark"></i> 0988 888 888</p>
                <p class="text-muted"><i class="fas fa-envelope mr-2 text-dark"></i> hotro@sachhay.com</p>
            </div>
        </div>
        <div class="text-center py-3 border-top text-muted" style="font-size: 14px;">
            &copy; {{ date('Y') }} SachHay. All rights reserved.
        </div>
    </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("theme-toggle");
    const root = document.documentElement;

    if (!btn) return;

    const saved = localStorage.getItem("theme");

    if (saved === "dark") {
        root.classList.add("dark-mode");
        btn.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        btn.innerHTML = '<i class="bi bi-moon-fill"></i>';
    }

    btn.addEventListener("click", function () {
        const isDark = root.classList.toggle("dark-mode");

        localStorage.setItem("theme", isDark ? "dark" : "light");

        btn.innerHTML = isDark
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-fill"></i>';
    });

});
</script>