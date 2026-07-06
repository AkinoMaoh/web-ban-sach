<header class="modern-header">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('user.index') }}" class="text-decoration-none">
            <h2 class="serif-font mb-0" style="color: var(--primary-color);">SachHay.</h2>
        </a>
        
        <!-- Menu -->
       <nav class="main-menu">
    <a href="{{ route('user.index') }}"
       class="menu-item {{ request()->routeIs('user.index') ? 'active' : '' }}">
        Trang chủ
    </a>

    <a href="{{ route('user.shop') }}"
       class="menu-item {{ request()->routeIs('user.shop*') ? 'active' : '' }}">
        Tủ sách
    </a>

    <a href="#" class="menu-item">
        Blog
    </a>

    <a href="{{ route('user.contact') }}"
       class="menu-item {{ request()->routeIs('user.contact') ? 'active' : '' }}">
        Liên hệ
    </a>
</nav>

        <!-- Search & Auth -->
        <div class="d-flex align-items-center">
            <!-- Search form -->
            <form action="{{ route('user.index') }}" method="GET" class="mr-4 position-relative d-none d-lg-block">
                <input type="text" id="search" name="keyword" placeholder="Tìm tên sách..." class="form-control rounded-pill bg-light border-0" style="padding: 5px 15px; font-size: 14px; width: 200px;" autocomplete="off">
                <div id="search-result" style="position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #ddd; z-index: 999; display: none;"></div>
            </form>

            <a href="{{ route('user.wishlist') }}" class="text-dark mr-3 position-relative" title="Sách yêu thích">
                <i class="far fa-heart fa-lg"></i>
            </a>
            <a href="{{ route('cart.index') }}" class="text-dark mr-3 position-relative">
                <i class="fas fa-shopping-bag fa-lg"></i>
            </a>
            
             <!-- Nút chuyển đổi chế độ sáng/tối -->
<button id="theme-toggle"
        type="button"
        class="btn btn-outline-secondary ml-3"
        title="Chuyển giao diện">
    <i class="fas fa-moon"></i>
</button>

            @auth
                <a href="{{ route('profile.edit') }}" class="text-dark font-weight-bold text-decoration-none">
                    <i class="fas fa-user-circle mr-1" style="color: var(--primary-color);"></i> {{ Auth::user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-orange text-decoration-none">Đăng nhập</a>
            @endauth
        </div>
    </div>
</header>
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