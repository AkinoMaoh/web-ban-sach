<header class="modern-header py-4 shadow-sm transition-base">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('user.index') }}" class="text-decoration-none mr-3">
            <h2 class="serif-font mb-0 font-weight-bold" style="color: var(--primary-color, #FF7A00); letter-spacing: -0.5px; font-size: 28px;">BookZone</h2>
        </a>
        
        <!-- Menu -->
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.index') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.index') ? 'active' : '' }}">
                Trang chủ
            </a>
        </nav>
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.shop') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.shop','user.category', 'user.productDetails') ? 'active' : '' }}">
                Tủ sách
            </a>
        </nav>
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.news') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.news', 'user.news.show') ? 'active' : '' }}">
                Tin tức
            </a>
        </nav>
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.contact') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.contact') ? 'active' : '' }}">
                Liên hệ
            </a>
        </nav>

        <!-- Search & Actions -->
        <div class="d-flex align-items-center" style="gap: 12px;">
            
            <div class="search-wrapper position-relative d-none d-lg-block" id="headerSearchWrapper" style="width: 230px;">
                <form action="{{ route('user.shop') }}" method="GET" id="searchForm" class="m-0">
                    <div class="custom-search-bar position-relative w-100">
                        <input type="text" id="searchInput" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm tên sách..." class="form-control shadow-none w-100" style="height: 42px; border-radius: 21px; padding-right: 42px;" autocomplete="off">
                        <button class="btn text-white d-flex align-items-center justify-content-center glow-pill-btn position-absolute" type="submit" aria-label="Tìm kiếm" style="width: 34px; height: 34px; border-radius: 50%; right: 4px; top: 50%; transform: translateY(-50%); z-index: 10;">
                            <i class="fas fa-search" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </form>

                <!-- Hộp Dropdown kết quả tìm kiếm -->
                <div class="search-dropdown-menu shadow-lg mt-2" id="searchDropdown" style="width: 440px; right: 0; left: auto; max-height: 420px; overflow-y: auto;">
                    <div class="text-white text-center py-3 px-3 font-weight-bold sticky-top search-dropdown-header d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #FF7A00, #D35400); font-size: 14px; letter-spacing: 0.3px; gap: 8px; border-top-left-radius: 23px; border-top-right-radius: 23px;">
                        <i class="fas fa-compass"></i> Khám phá kho tàng tri thức
                    </div>
                    <div class="p-3" id="searchContentBox">
                        <div class="text-center text-muted my-3"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tải dữ liệu...</div>
                    </div>
                </div>
            </div>

            <!-- LẤY SỐ LƯỢNG GIỎ HÀNG VÀ WISHLIST -->
            @php
                $cartCount = 0;
                $wishlistCount = 0;
                if(auth()->check()){
                    $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count();
                    $wishlistCount = \Illuminate\Support\Facades\DB::table('wishlists')->where('user_id', auth()->id())->count();
                }else{
                    $cartCount = count(session('cart', []));
                }
            @endphp

            <!-- ICON YÊU THÍCH -->
            <a href="{{ route('user.wishlist') }}" class="header-icon-btn position-relative {{ request()->routeIs('user.wishlist*') ? 'active' : '' }}" title="Sách yêu thích">
                <i class="far fa-heart"></i>
                @if($wishlistCount > 0)
                    <span class="badge badge-danger rounded-circle header-badge">
                        {{ $wishlistCount }}
                    </span>
                @endif
            </a>

            <!-- GIỎ HÀNG -->
            <a href="{{ route('cart.index') }}" class="header-icon-btn position-relative {{ request()->routeIs('cart.*') ? 'active' : '' }}" title="Giỏ hàng">
                <i class="fas fa-shopping-bag"></i>
                @if($cartCount > 0)
                    <span class="badge badge-danger rounded-circle header-badge">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
            
            <!-- THÔNG BÁO -->
            @auth
                @php
                    $notifs = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
                    $count = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                <div class="dropdown">
                    <a class="header-icon-btn position-relative" href="#" data-toggle="dropdown" data-display="static" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        @if($count > 0) 
                            <span class="badge badge-danger rounded-circle header-badge">{{ $count }}</span> 
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-lg mt-2 search-dropdown-menu notification-dropdown" style="width: 360px; z-index: 9999; border-radius: 24px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08);">
                        <div class="py-3 px-4 font-weight-bold d-flex align-items-center justify-content-between notification-dropdown-header" style="font-size: 14px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                            <span class="text-dark dark-mode-link"><i class="fas fa-bell mr-2" style="color: var(--primary-color, #D35400);"></i> Thông báo của bạn</span>
                            @if($count > 0)
                                <span class="badge badge-pill px-2 py-1" style="font-size: 11px; background: rgba(211,84,0,0.1); color: var(--primary-color, #D35400) !important;">{{ $count }} mới</span>
                            @endif
                        </div>

                        <div class="notification-list px-2 py-2" style="max-height: 340px; overflow-y: auto;">
                            @forelse($notifs as $n)
                                <div class="dropdown-item d-flex align-items-start py-2 px-3 my-1 rounded-lg notification-item {{ !$n->is_read ? 'unread-notification' : '' }}" style="white-space: normal;">
                                    @if(!$n->is_read)
                                        <span class="unread-dot mr-2 mt-1" style="width: 8px; height: 8px; background-color: var(--primary-color, #D35400); border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 8px rgba(211,84,0,0.6);"></span>
                                    @else
                                        <span class="mr-2 mt-1" style="width: 8px; height: 8px; display: inline-block; flex-shrink: 0;"></span>
                                    @endif
                                    <a href="{{ route('notifications.redirect', $n->id) }}" class="text-dark text-decoration-none dark-mode-link flex-grow-1" style="line-height: 1.4; font-size: 13px;">
                                        {{ $n->message }}
                                    </a>
                                    <form action="{{ route('notifications.delete', $n->id) }}" method="POST" class="ml-2 flex-shrink-0" onsubmit="return confirm('Xóa thông báo?')">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-muted delete-notif-btn p-0" title="Xóa"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4" style="font-size: 13px;">
                                    <i class="far fa-bell-slash fa-2x mb-2 text-muted opacity-50"></i><br>Chưa có thông báo nào
                                </div>
                            @endforelse
                        </div>

                        @if($notifs->count() > 0)
                            <div class="p-2 text-center border-top notification-footer">
                                <a class="text-decoration-none font-weight-bold d-block py-2" href="{{ route('notifications.read.all') }}" style="font-size: 13px; color: var(--primary-color, #D35400);">
                                    Đánh dấu tất cả đã đọc <i class="fas fa-check-double ml-1"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endauth
            
            <!-- USER / LOGIN -->
            @auth
               <a href="{{ route('profile.edit') }}" class="user-profile-btn rounded-pill px-3 py-2 text-decoration-none shadow-sm d-flex align-items-center {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user-circle mr-2 user-profile-icon"></i>
                    <div class="user-name-container">
                        <span class="user-name-text">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn text-white text-decoration-none font-weight-bold shadow-sm glow-pill-btn px-4 py-2" style="border-radius: 50rem; white-space: nowrap; font-size: 14px;">
                    Đăng nhập
                </a>
            @endauth

            <!-- TRA CỨU ĐƠN -->
            @guest
                <a href="{{ url('/tra-cuu-don') }}" class="btn mr-3 d-inline-flex align-items-center tra-cuu-btn" style="border: 1.5px solid #f56a00; color: #f56a00; background-color: #fff; border-radius: 25px; padding: 6px 18px; text-decoration: none; transition: 0.3s;" title="Tra cứu đơn hàng">
                    <i class="fas fa-search-location" style="font-size: 1rem;"></i>
                    <span class="ml-2 d-none d-md-inline" style="font-weight: 600;">Tra đơn</span>
                </a>
            @endguest
            
            <style>
                .tra-cuu-btn:hover {
                    background-color: #f56a00 !important;
                    color: #fff !important;
                }
            </style>
        </div>
    </div>
</header>
<style>
/* =========================================================
   1. USER PROFILE BUTTON
========================================================= */
.user-profile-btn {
    display: inline-flex;
    align-items: center;
    max-width: 170px;
    background-color: #F8FAFC;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 50rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.user-profile-btn .user-name-text {
    color: #2C3E50 !important;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.user-profile-btn .user-profile-icon {
    color: #E67E22;
    font-size: 18px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.user-profile-btn:hover {
    background: linear-gradient(135deg, #E67E22 0%, #D35400 100%);
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(211, 84, 0, 0.25);
    transform: translateY(-1px);
}

.user-profile-btn:hover .user-name-text,
.user-profile-btn:hover .user-profile-icon {
    color: #FFFFFF !important;
}

.user-profile-btn.active {
    background: linear-gradient(135deg, #E67E22 0%, #D35400 100%) !important;
    box-shadow: 0 4px 15px rgba(211, 84, 0, 0.3);
}

.user-profile-btn.active .user-name-text,
.user-profile-btn.active .user-profile-icon {
    color: #FFFFFF !important;
}

.user-profile-btn:active {
    transform: scale(0.97);
}

/* =========================================================
   2. HEADER & SEARCH BAR
========================================================= */
.modern-header {
    background-color: #FFFFFF;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.custom-search-bar {
    background-color: #F8FAFC;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 50rem;
    padding: 3px 3px 3px 14px;
    height: 40px;
    transition: all 0.25s ease;
    overflow: hidden;
}

.custom-search-bar:focus-within {
    background-color: #FFFFFF;
    border-color: #E67E22;
    box-shadow: 0 4px 14px rgba(230, 126, 34, 0.15);
}

.custom-search-bar input {
    font-size: 14px;
    color: #2C3E50;
    height: 32px;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    border-radius: 50rem !important;
}

.custom-search-bar input::placeholder {
    color: #94A3B8;
}

.glow-pill-btn {
    background: linear-gradient(135deg, #E67E22, #D35400) !important;
    box-shadow: 0 2px 10px rgba(211, 84, 0, 0.25) !important;
    transition: all 0.25s ease !important;
    border: none !important;
}

.glow-pill-btn:hover {
    box-shadow: 0 4px 16px rgba(211, 84, 0, 0.4) !important;
    transform: translateY(-1px);
}

/* Header Action Buttons (Cart, Bell, Wishlist) */
.header-icon-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    background-color: #F1F5F9 !important;
    color: #2C3E50 !important;
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    border: none !important;
    cursor: pointer !important;
    outline: none !important;
    padding: 0 !important;
}

.header-icon-btn:hover {
    background: linear-gradient(135deg, #E67E22, #D35400) !important;
    box-shadow: 0 4px 12px rgba(211, 84, 0, 0.3) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px) !important;
}

.header-icon-btn i {
    font-size: 16px !important;
    transition: color 0.2s ease;
}

.header-icon-btn:hover i {
    color: #FFFFFF !important;
}

.header-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    font-size: 10px;
    font-weight: 700;
    border: 2px solid #FFFFFF;
    padding: 2px 5px;
    line-height: 1;
    z-index: 2;
}

/* =========================================================
   3. MAIN MENU - LIGHT MODE
========================================================= */
.main-menu .menu-item {
    color: #2C3E50 !important;
    font-weight: 500;
    font-size: 14.5px;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.25s ease;
    text-decoration: none !important;
}

.main-menu .menu-item i {
    color: #E67E22 !important;
    margin-right: 6px;
    transition: all 0.25s ease;
}

/* Hover Light Mode: Nền mờ nhạt sang trọng, chữ đổi sang cam đất */
.main-menu .menu-item:hover {
    color: #D35400 !important;
    background-color: rgba(230, 126, 34, 0.08) !important;
}

.main-menu .menu-item:hover i {
    color: #D35400 !important;
}

/* Active Light Mode: Gradient Amber cao cấp */
.main-menu .menu-item.active {
    color: #FFFFFF !important;
    background: linear-gradient(135deg, #E67E22 0%, #D35400 100%) !important;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(211, 84, 0, 0.25);
}

.main-menu .menu-item.active i {
    color: #FFFFFF !important;
}

/* =========================================================
   4. DROPDOWNS & COMPONENTS
========================================================= */
.search-wrapper { z-index: 1050; }
.dropdown-menu-right { right: 0 !important; left: auto !important; transform: none !important; }

.search-dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    background-color: #FFFFFF;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    overflow: hidden;
    display: none;
    z-index: 1060;
}

.show > .search-dropdown-menu,
.dropdown.show .search-dropdown-menu,
.search-wrapper.show .search-dropdown-menu { 
    display: block !important; 
}

.search-dropdown-menu::-webkit-scrollbar { width: 6px; }
.search-dropdown-menu::-webkit-scrollbar-thumb { background-color: rgba(0, 0, 0, 0.15); border-radius: 10px; }

.notification-item { border-radius: 10px !important; transition: background-color 0.2s ease; }
.notification-item:hover { background-color: rgba(230, 126, 34, 0.06) !important; }
.unread-notification { background-color: rgba(230, 126, 34, 0.04); }
.delete-notif-btn { opacity: 0.5; transition: opacity 0.2s ease; }
.notification-item:hover .delete-notif-btn { opacity: 1; }
.delete-notif-btn:hover { color: #E74C3C !important; }
.notification-footer { background-color: #F8FAFC !important; border-color: rgba(0,0,0,0.05) !important; }

.search-keyword-pill {
    background-color: #F8FAFC;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.search-keyword-pill:hover {
    background-color: rgba(230, 126, 34, 0.08);
    border-color: rgba(230, 126, 34, 0.3);
    color: #D35400 !important;
    transform: translateY(-1px);
}

.category-card { transition: all 0.2s ease; border-radius: 10px; }
.category-card:hover { background-color: rgba(230, 126, 34, 0.06); transform: translateY(-2px); }

/* User Marquee */
.user-name-container {
    width: 100px;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    -webkit-mask-image: linear-gradient(to right, #000 70%, transparent 100%);
    mask-image: linear-gradient(to right, #000 70%, transparent 100%);
}

.user-name-text { display: inline-block; padding-right: 15px; font-size: 14px; }
.needs-marquee { animation: scrollTextMarquee 6s linear infinite alternate; }

@keyframes scrollTextMarquee {
    0%, 20% { transform: translateX(0); }
    80%, 100% { transform: translateX(calc(100px - 100%)); }
}

/* =========================================================
   DARK MODE - HEADER ĐEN TUYỀN & FIX KHUNG TÌM KIẾM
========================================================= */

/* 1. Header màu đen tuyền sang trọng */
html.dark-mode .modern-header,
body.dark-mode .modern-header {
    background-color: #0d0d0d !important; /* Đen tuyền chuẩn Dark Mode */
    border-bottom-color: rgba(255, 255, 255, 0.08) !important;
}

/* =========================================================
   FIX TRIỆT ĐỂ LỖI LỒNG KHUNG TÌM KIẾM (DARK MODE)
========================================================= */

/* 1. Xóa toàn bộ viền và nền ở thẻ bao ngoài */
.search-wrapper,
html.dark-mode .search-wrapper,
body.dark-mode .search-wrapper {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}

/* 2. Tạo 1 khung duy nhất cho .custom-search-bar */
.custom-search-bar,
html.dark-mode .custom-search-bar,
body.dark-mode .custom-search-bar {
    background-color: #1a1a1a !important; /* Nền xám đen duy nhất */
    border: 1px solid rgba(255, 255, 255, 0.15) !important; /* Viền mảnh duy nhất */
    border-radius: 50rem !important; /* Bo góc dạng viên thuốc */
    padding: 3px 3px 3px 15px !important;
    height: 40px !important;
    box-shadow: none !important;
    transition: all 0.25s ease !important;
}

/* Hiệu ứng viền cam khi click/focus vào tìm kiếm */
.custom-search-bar:focus-within,
html.dark-mode .custom-search-bar:focus-within,
body.dark-mode .custom-search-bar:focus-within {
    border-color: #f97316 !important;
    box-shadow: 0 0 12px rgba(249, 115, 22, 0.3) !important;
    background-color: #222222 !important;
}

/* 3. Triệt tiêu hoàn toàn viền/nền của thẻ <input> bên trong */
.custom-search-bar input#searchInput,
html.dark-mode .custom-search-bar input#searchInput,
body.dark-mode .custom-search-bar input#searchInput {
    background: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    color: #ffffff !important;
    font-size: 13.5px !important;
    height: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Chỉnh màu chữ gợi ý (Placeholder) */
.custom-search-bar input#searchInput::placeholder,
html.dark-mode .custom-search-bar input#searchInput::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

/* 4. Nút Kính lúp bo tròn gọn gàng bên trong */
.custom-search-bar .glow-pill-btn {
    background: linear-gradient(135deg, #e65c00 0%, #f97316 100%) !important;
    border: none !important;
    box-shadow: 0 2px 8px rgba(230, 92, 0, 0.3) !important;
    flex-shrink: 0 !important;
}
/* 3. Đồng bộ các nút Icon (Giỏ hàng, Yêu thích, Chuông) */
html.dark-mode .header-icon-btn,
body.dark-mode .header-icon-btn {
    background-color: #1a1a1a !important;
    color: #f1f5f9 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

html.dark-mode .header-icon-btn:hover {
    background: linear-gradient(135deg, #E67E22, #D35400) !important;
    color: #ffffff !important;
    border-color: transparent !important;
}

/* 4. Nút User Profile */
html.dark-mode .user-profile-btn,
body.dark-mode .user-profile-btn {
    background-color: #1a1a1a !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
}

html.dark-mode .user-profile-btn .user-name-text,
body.dark-mode .user-profile-btn .user-name-text {
    color: #ffffff !important;
}

/* 5. Dropdown tìm kiếm & Thông báo */
html.dark-mode .search-dropdown-menu,
body.dark-mode .search-dropdown-menu {
    background-color: #161616 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.7) !important;
}

html.dark-mode .notification-footer,
body.dark-mode .notification-footer {
    background-color: #0d0d0d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
/* =========================================================
   1. MÃ MÀU CAM ACTIVE CHUẨN (TỪ NÚT TRANG CHỦ)
========================================================= */
:root {
    --orange-active-bg: linear-gradient(135deg, #e65c00 0%, #f97316 100%);
    --orange-active-shadow: 0 4px 15px rgba(230, 92, 0, 0.45);
}

/* =========================================================
   2. ÁP DỤNG ĐỒNG BỘ CHO TẤT CẢ PHẦN TỬ ACTIVE (LIGHT & DARK)
========================================================= */

/* --- Main Menu Items (.active) --- */
.main-menu .menu-item.active,
html.dark-mode .main-menu .menu-item.active,
body.dark-mode .main-menu .menu-item.active {
    background: var(--orange-active-bg) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    box-shadow: var(--orange-active-shadow) !important;
    border: none !important;
}

.main-menu .menu-item.active i,
html.dark-mode .main-menu .menu-item.active i,
body.dark-mode .main-menu .menu-item.active i {
    color: #ffffff !important;
}

/* --- User Profile Button (.active) --- */
.user-profile-btn.active,
html.dark-mode .user-profile-btn.active,
body.dark-mode .user-profile-btn.active {
    background: var(--orange-active-bg) !important;
    color: #ffffff !important;
    box-shadow: var(--orange-active-shadow) !important;
    border-color: transparent !important;
}

.user-profile-btn.active .user-name-text,
.user-profile-btn.active .user-profile-icon,
html.dark-mode .user-profile-btn.active .user-name-text,
html.dark-mode .user-profile-btn.active .user-profile-icon {
    color: #ffffff !important;
}

/* --- Header Action Icons: Yêu thích, Giỏ hàng, Thông báo (.active) --- */
.header-icon-btn.active,
html.dark-mode .header-icon-btn.active,
body.dark-mode .header-icon-btn.active {
    background: var(--orange-active-bg) !important;
    color: #ffffff !important;
    box-shadow: var(--orange-active-shadow) !important;
    border: none !important;
}

.header-icon-btn.active i,
html.dark-mode .header-icon-btn.active i,
body.dark-mode .header-icon-btn.active i {
    color: #ffffff !important;
}

/* --- Các nút chung hoặc thẻ liên kết khác có class .active --- */
.btn.active,
.nav-link.active,
html.dark-mode .btn.active,
html.dark-mode .nav-link.active {
    background: var(--orange-active-bg) !important;
    color: #ffffff !important;
    box-shadow: var(--orange-active-shadow) !important;
    border-color: transparent !important;
}
/* =========================================================
   FIX 3 NÚT ICON HEADER (FAVORITE, CART, NOTIFICATION)
========================================================= */

/* Trạng thái bình thường (Light Mode) */
.header-icon-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 42px !important;
    height: 42px !important;
    border-radius: 50% !important;
    background-color: #f1f2f6; /* Đã BỎ !important để cho phép override */
    color: #2C3E50;           /* Đã BỎ !important */
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    border: none !important;
    cursor: pointer !important;
    outline: none !important;
    position: relative;
}

/* ĐỔI MÀU CAM KHI ĐƯỢC CHỌN (.active) HOẶC FOCUS KHI CLICK */
.header-icon-btn.active,
.header-icon-btn:focus,
.header-icon-btn[aria-expanded="true"] {
    background: linear-gradient(135deg, #e65c00 0%, #f97316 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(230, 92, 0, 0.45) !important;
}

.header-icon-btn.active i,
.header-icon-btn:focus i,
.header-icon-btn[aria-expanded="true"] i {
    color: #ffffff !important;
}

/* Hover vẫn sáng màu cam */
.header-icon-btn:hover {
    background: linear-gradient(135deg, #e65c00 0%, #f97316 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(230, 92, 0, 0.808) !important;
    transform: translateY(-2px) !important;
}

.header-icon-btn:hover i {
    color: #ffffff !important;
}

/* Trong chế độ Dark Mode */
html.dark-mode .header-icon-btn,
body.dark-mode .header-icon-btn {
    background-color: #1a1a1a;
    color: #f1f5f9;
}
/* Phát sáng màu cam cho nút khi Active HOẶC khi Dropdown mở (.show / aria-expanded) */
.header-icon-btn.active,
.header-icon-btn[aria-expanded="true"],
.dropdown.show .header-icon-btn {
    background: linear-gradient(135deg, #e65c00 0%, #f97316 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(230, 92, 0, 0.808) !important;
}

.header-icon-btn.active i,
.header-icon-btn[aria-expanded="true"] i,
.dropdown.show .header-icon-btn i {
    color: #ffffff !important;
}
/* =========================================================
   HOVER MENU ITEMS - CHỮ CHUYỂN THÀNH MÀU TRẮNG
========================================================= */

/* 1. Trạng thái Hover chung cho Main Menu (Light & Dark Mode) */
.main-menu .menu-item:hover,
html.dark-mode .main-menu .menu-item:hover,
body.dark-mode .main-menu .menu-item:hover {
    color: #ffffff !important; /* Đổi màu chữ sang trắng */
    background-color: rgba(223, 93, 0, 0.918) !important; /* Nền mờ cam tối tinh tế */
    transition: all 0.25s ease !important;
}

/* 2. Đổi màu Icon (nếu có) sang màu trắng khi Hover */
.main-menu .menu-item:hover i,
html.dark-mode .main-menu .menu-item:hover i,
body.dark-mode .main-menu .menu-item:hover i {
    color: #ffffff !important;
}
/* =========================================================
   FIX THANH TÌM KIẾM: LIGHT MODE (XÁM NHẠT) vs DARK MODE (ĐEN)
========================================================= */

/* 1. MẶC ĐỊNH / LIGHT MODE: Nền xám nhạt tinh tế */
.custom-search-bar {
    background-color: #f1f5f9 !important; /* Màu xám nhạt */
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 50rem !important;
    padding: 3px 3px 3px 15px !important;
    height: 40px !important;
    box-shadow: none !important;
    transition: all 0.25s ease !important;
}

/* Hover/Focus ở Light Mode */
.custom-search-bar:focus-within {
    background-color: #ffffff !important;
    border-color: #f97316 !important;
    box-shadow: 0 0 10px rgba(249, 115, 22, 0.2) !important;
}

/* Input ở Light Mode: Chữ màu xám than tối */
.custom-search-bar input#searchInput {
    background: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    color: #2c3e50 !important; /* Màu chữ tối dễ đọc */
    font-size: 13.5px !important;
    height: 100% !important;
    padding: 0 !important;
}

.custom-search-bar input#searchInput::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}


/* 2. CHẾ ĐỘ DARK MODE: Chuyển sang màu xám đen */
html.dark-mode .custom-search-bar,
body.dark-mode .custom-search-bar {
    background-color: #1a1a1a !important; /* Nền tối ở Dark mode */
    border-color: rgba(255, 255, 255, 0.12) !important;
}

html.dark-mode .custom-search-bar:focus-within,
body.dark-mode .custom-search-bar:focus-within {
    background-color: #222222 !important;
    border-color: #f97316 !important;
    box-shadow: 0 0 10px rgba(249, 115, 22, 0.3) !important;
}

html.dark-mode .custom-search-bar input#searchInput,
body.dark-mode .custom-search-bar input#searchInput {
    color: #ffffff !important; /* Chữ trắng ở Dark mode */
}

/* =========================================================
   SEARCH DROPDOWN
   ========================================================= */

.search-wrapper {
    position: relative;
}

/* Dropdown chính */
.search-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 10px);
    right: 0;

    width: 440px;
    max-height: 430px;

    background: #fff;
    border-radius: 22px;

    overflow-y: auto;
    overflow-x: hidden;

    z-index: 9999;

    border: 1px solid rgba(0, 0, 0, 0.08);

    box-shadow:
        0 12px 35px rgba(0, 0, 0, 0.15);

    scrollbar-width: thin;
}

/* Thanh cuộn */
.search-dropdown-menu::-webkit-scrollbar {
    width: 5px;
}

.search-dropdown-menu::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.search-dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}


/* =========================================================
   HEADER "KHÁM PHÁ KHO TÀNG TRI THỨC"
   ========================================================= */

.search-dropdown-header {
    background: linear-gradient(
        135deg,
        #ff7a00,
        #e85d00
    );

    color: #fff;

    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    font-size: 14px;
    font-weight: 700;

    border-radius: 22px 22px 0 0;

    letter-spacing: 0.2px;
}

.search-dropdown-header i {
    font-size: 13px;
}


/* =========================================================
   CONTENT
   ========================================================= */

.search-content-box {
    padding: 14px 14px 18px;
}


/* =========================================================
   TIÊU ĐỀ CÁC SECTION
   ========================================================= */

.search-section-title {
    display: flex;
    align-items: center;

    margin: 2px 0 10px;

    font-size: 13px;
    font-weight: 700;

    color: #444;

    text-transform: uppercase;

    letter-spacing: 0.3px;
}

.search-section-title i {
    color: #e85d00;

    width: 18px;

    margin-right: 4px;

    font-size: 12px;
}


/* =========================================================
   TÌM KIẾM GẦN ĐÂY
   ========================================================= */

.recent-search-list {
    display: flex;
    flex-wrap: wrap;

    gap: 8px;

    margin-bottom: 18px;
}

.recent-search-item {
    display: inline-flex;
    align-items: center;

    padding: 8px 11px;

    background: #f5f5f5;

    border-radius: 8px;

    color: #555;

    font-size: 12px;

    cursor: pointer;

    transition: all 0.2s ease;
}

.recent-search-item i {
    margin-right: 7px;

    color: #888;

    font-size: 11px;
}

.recent-search-item:hover {
    background: rgba(255, 122, 0, 0.1);

    color: #e85d00;

    transform: translateY(-1px);
}


/* =========================================================
   TỪ KHÓA HOT
   ========================================================= */

.hot-keyword-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 8px 28px;

    margin-bottom: 20px;
}

.hot-keyword-item {
    display: flex;
    align-items: center;

    min-height: 38px;

    padding: 7px 9px;

    background: #f8f9fa;

    border: 1px solid #e8e8e8;

    border-radius: 5px;

    color: #555;

    font-size: 13px;

    cursor: pointer;

    transition: all 0.2s ease;
}

.hot-keyword-item:hover {
    background: #fff5ed;

    border-color: #ffb27a;

    color: #e85d00;

    transform: translateY(-1px);
}


/* Số #1 #2 #3 #4 */

.hot-number {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 24px;
    height: 24px;

    margin-right: 8px;

    flex-shrink: 0;

    border-radius: 50%;

    background: #fff0e5;

    color: #e85d00;

    font-size: 11px;
    font-weight: 700;
}


/* =========================================================
   DANH MỤC NỔI BẬT
   ========================================================= */

.category-grid {
    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 8px;

    text-align: center;

    margin-top: 5px;
}

.category-item {
    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: flex-start;

    padding: 5px 2px;

    text-decoration: none !important;

    color: #444 !important;

    border-radius: 8px;

    transition: all 0.2s ease;
}

.category-item:hover {
    transform: translateY(-2px);

    background: #fff8f3;
}


/* Ảnh danh mục */

.category-item img {
    width: 48px;
    height: 64px;

    object-fit: cover;

    border-radius: 4px;

    border: 1px solid #ddd;

    margin-bottom: 5px;

    box-shadow:
        0 2px 5px rgba(0, 0, 0, 0.08);
}


/* Tên danh mục */

.category-name {
    width: 100%;

    font-size: 11px;

    font-weight: 600;

    line-height: 1.25;

    color: #555;

    display: -webkit-box;

    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;

    overflow: hidden;
}


/* =========================================================
   KẾT QUẢ TÌM KIẾM SÁCH
   ========================================================= */

.search-product-item {
    display: flex;

    align-items: center;

    padding: 9px;

    margin-bottom: 7px;

    text-decoration: none !important;

    color: #333 !important;

    border-radius: 8px;

    border-bottom: 1px solid #eee;

    transition: all 0.2s ease;
}

.search-product-item:hover {
    background: #fff7f1;

    transform: translateX(2px);
}


/* Ảnh sách */

.search-product-img {
    width: 50px;
    height: 68px;

    object-fit: cover;

    border-radius: 4px;

    border: 1px solid #ddd;

    margin-right: 11px;

    flex-shrink: 0;
}


/* Tên sách */

.search-product-name {
    font-size: 13px;

    font-weight: 700;

    color: #333;

    line-height: 1.35;

    margin-bottom: 5px;
}


/* Giá */

.search-product-price {
    font-size: 12px;

    font-weight: 600;

    color: #e85d00;
}


/* =========================================================
   KHÔNG TÌM THẤY
   ========================================================= */

.search-empty {
    text-align: center;

    padding: 35px 15px;

    color: #777;

    font-size: 13px;
}

.search-empty::before {
    content: "\f002";

    font-family: "Font Awesome 5 Free";

    font-weight: 900;

    display: block;

    margin-bottom: 10px;

    font-size: 28px;

    color: #ddd;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    .search-dropdown-menu {
        width: 400px;
    }

}

@media (max-width: 576px) {

    .search-dropdown-menu {
        width: calc(100vw - 30px);

        right: -10px;

        max-height: 400px;
    }

    .hot-keyword-grid {
        gap: 7px;
    }

    .category-grid {
        gap: 5px;
    }

}
</style>

<script>
    document.querySelectorAll('.header-icon-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Nếu muốn chỉ 1 nút sáng tại 1 thời điểm:
        document.querySelectorAll('.header-icon-btn').forEach(btn => btn.classList.remove('active'));
        
        // Bật/Tắt class active cho nút vừa bấm
        this.classList.toggle('active');
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const userNameText = document.querySelector('.user-name-text');
    const userNameContainer = document.querySelector('.user-name-container');
    if (userNameText && userNameContainer) {
        if (userNameText.scrollWidth > userNameContainer.clientWidth) {
            userNameText.classList.add('needs-marquee');
            userNameContainer.style.textAlign = 'left';
        } else {
            userNameContainer.style.textAlign = 'center';
            userNameContainer.style.width = '100%';
        }
    }

    function renderSuggestions(data) {
        let html = ``;
        if(data.hot_keywords && data.hot_keywords.length > 0) {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold mb-0 text-dark dark-mode-link" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-chart-line mr-2" style="color: #D35400;"></i> Từ khóa hot
                    </h6>
                </div>
                <div class="row mb-3">
            `;
            data.hot_keywords.forEach((kw, index) => {
                html += `
                    <div class="col-6 mb-2">
                        <a href="/shop?keyword=${encodeURIComponent(kw)}" 
                           class="d-flex align-items-center text-decoration-none text-dark p-2 rounded search-keyword-pill">
                            <span class="badge badge-light mr-2 text-primary font-weight-bold" style="font-size: 11px; background: rgba(211,84,0,0.1); color: var(--primary-color, #D35400) !important;">#${index + 1}</span>
                            <span class="text-truncate font-weight-500 dark-mode-link" style="font-size: 13px;">${kw}</span>
                        </a>
                    </div>
                `;
            });
            html += `</div><hr class="my-2 border-light opacity-25">`;
        }

        if(data.categories && data.categories.length > 0) {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold mb-0 text-dark dark-mode-link" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-th-large mr-2" style="color: var(--primary-color, #D35400);"></i> Danh mục nổi bật
                    </h6>
                </div>
                <div class="row text-center mt-2">
            `;
            data.categories.forEach(cat => {
                html += `
                    <div class="col-3 px-1">
                        <a href="/shop/category/${cat.id}" class="text-decoration-none text-dark d-block category-card p-2">
                            <img src="${cat.image}" class="rounded shadow-sm mb-1 object-fit-cover border" style="width: 48px; height: 64px;" alt="${cat.name}">
                            <div class="font-weight-bold text-truncate-2 dark-mode-link" style="font-size: 11px; line-height: 1.3;">${cat.name}</div>
                        </a>
                    </div>
                `;
            });
            html += `</div>`;
        }
        searchContentBox.innerHTML = html;
    }

    function renderProducts(products, keyword) {
        if(products.length === 0) {
            searchContentBox.innerHTML = `<div class="text-center text-muted my-4"><i class="fas fa-search mb-2 fa-2x"></i><br>Không tìm thấy sách nào cho từ khóa "<b>${keyword}</b>"</div>`;
            return;
        }

        let html = `<div class="font-weight-bold mb-2 text-muted" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Sách tìm thấy</div>`;
        products.forEach(product => {
            let img = product.image ? `/uploads/products/${product.image}` : 'https://via.placeholder.com/60x80?text=No+Image';
            let authorName = product.author ? product.author.name : 'Đang cập nhật';
            let priceHtml = '';
            let formatPrice = (p) => new Intl.NumberFormat('vi-VN').format(p) + 'đ';

            if (product.first_variant) {
                let variant = product.first_variant;
                if (variant.sale_price && variant.sale_price > 0 && variant.sale_price < variant.price) {
                    priceHtml = `
                        <span class="font-weight-bold mr-2" style="font-size: 14px; color: var(--primary-color, #D35400);">${formatPrice(variant.sale_price)}</span>
                        <del class="text-muted" style="font-size: 12px;">${formatPrice(variant.price)}</del>
                    `;
                } else {
                    priceHtml = `<span class="font-weight-bold" style="font-size: 14px; color: var(--primary-color, #D35400);">${formatPrice(variant.price)}</span>`;
                }
            } else {
                let fallbackPrice = product.price ? formatPrice(product.price) : 'Liên hệ';
                priceHtml = `<span class="font-weight-bold" style="font-size: 14px; color: var(--primary-color, #D35400);">${fallbackPrice}</span>`;
            }

            html += `
                <a href="/product/${product.id}" class="d-flex align-items-center p-2 mb-2 search-item-result border-bottom text-decoration-none text-dark rounded">
                    <img src="${img}" style="width: 50px; height: 70px;" class="object-fit-cover rounded mr-3 shadow-sm border" alt="${product.name}">
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="mb-1 text-truncate-2 font-weight-bold dark-mode-link" style="font-size: 14px; line-height: 1.4;">${product.name}</h6>
                        <div class="text-muted mb-1" style="font-size: 12px;"><i class="fas fa-pen-nib mr-1"></i> ${authorName}</div>
                        <div>${priceHtml}</div>
                    </div>
                </a>
            `;
        });
        
        html += `
            <div class="text-center mt-3 pb-2">
                <a href="/shop?keyword=${encodeURIComponent(keyword)}" onclick="document.getElementById('searchForm').submit(); return false;" class="text-decoration-none font-weight-bold hover-primary" style="font-size: 14px; color: var(--primary-color, #D35400);">
                    Xem tất cả kết quả <i class="fas fa-angle-right ml-1"></i>
                </a>
            </div>
        `;
        searchContentBox.innerHTML = html;
    }
});
</script>