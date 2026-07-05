<header class="modern-header">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('user.index') }}" class="text-decoration-none">
            <h2 class="serif-font mb-0" style="color: var(--primary-color);">SachHay.</h2>
        </a>
        
        <!-- Menu -->
        <nav class="d-none d-md-flex">
            <a href="{{ route('user.index') }}" class="nav-link mx-3">Trang chủ</a>
            <a href="{{ route('user.shop') }}" class="nav-link mx-3">Tủ sách</a>
            <a href="#" class="nav-link mx-3">Blog</a>
            <a href="{{ route('user.contact') }}" class="nav-link mx-3">Liên hệ</a>
        </nav>

        <!-- Search & Auth -->
        <div class="d-flex align-items-center">
            <!-- Search form -->
            <form action="{{ route('user.index') }}" method="GET" class="mr-4 position-relative d-none d-lg-block">
                <input type="text" id="search" name="keyword" placeholder="Tìm tên sách..." class="form-control rounded-pill bg-light border-0" style="padding: 5px 15px; font-size: 14px; width: 200px;" autocomplete="off">
                <div id="search-result" style="position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #ddd; z-index: 999; display: none;"></div>
            </form>

            <a href="/cart" class="text-dark mr-4 position-relative">
                <i class="fas fa-shopping-bag fa-lg"></i>
            </a>

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