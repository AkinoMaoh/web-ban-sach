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
       class="menu-item {{ request()->routeIs('user.shop','user.category') ? 'active' : '' }}">
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
  @php
if(auth()->check()){
    $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count();
}else{
    $cartCount = count(session('cart', []));
}
@endphp

<a href="{{ route('cart.index') }}" class="text-dark mr-3 position-relative">
    <i class="fas fa-shopping-bag fa-lg"></i>

    @if($cartCount > 0)
        <span class="cart-badge">
            {{ $cartCount }}
        </span>
    @endif
</a>
          
             <!-- Nút chuyển đổi chế độ sáng/tối -->
           <button id="theme-toggle"
                    type="button"
                    class="btn mr-3"
                    title="Chuyển giao diện">
                <i class="fas fa-moon"></i>
            </button>
               @auth
                    @php
                        $notifs = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
                        $count = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                    @endphp
                    <div class="dropdown mr-3">
                        <a class="text-dark position-relative" href="#" data-toggle="dropdown">
                            <i class="fas fa-bell fa-lg"></i>
                            @if($count > 0) <span class="badge badge-danger" style="position: absolute; top: -10px; right: -10px; font-size: 10px;">{{ $count }}</span> @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow border-0" style="width: 300px;">
                            <h6 class="dropdown-header">Thông báo</h6>
                            @forelse($notifs as $n)
                                <div class="dropdown-item d-flex justify-content-between align-items-start py-2 {{ !$n->is_read ? 'font-weight-bold' : '' }}">
                                    <!-- Bấm vào đây để đi tới đơn hàng -->
                                    <a href="{{ route('notifications.redirect', $n->id) }}" class="text-dark text-decoration-none" style="white-space: normal; line-height: 1.4; font-size: 14px;">
                                        {{ $n->message }}
                                    </a>
                                    <!-- Nút Xóa -->
                                    <form action="{{ route('notifications.delete', $n->id) }}" method="POST" class="ml-2" onsubmit="return confirm('Xóa thông báo?')">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            @empty
                                <span class="dropdown-item text-muted text-center">Chưa có thông báo</span>
                            @endforelse
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center text-primary" href="{{ route('notifications.read.all') }}">Đánh dấu đã đọc</a>
                        </div>
                    </div>
                @endauth
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