<header class="modern-header py-4 shadow-sm transition-base">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('user.index') }}" class="text-decoration-none mr-3">
            <h2 class="serif-font mb-0 font-weight-bold" style="color: var(--primary-color, #D35400); letter-spacing: -0.5px; font-size: 28px;">SachHay.</h2>
        </a>
        
        <!-- Menu -->
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.index') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.index') ? 'active' : '' }}">
                Trang chủ
            </a>
            <a href="{{ route('user.shop') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.shop','user.category', 'user.productDetails') ? 'active' : '' }}">
                Tủ sách
            </a>
            <a href="{{ route('user.news') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.news', 'user.news.show') ? 'active' : '' }}">
                Tin tức
            </a>
            <a href="{{ route('user.contact') }}" class="menu-item px-3 py-2 rounded-pill {{ request()->routeIs('user.contact') ? 'active' : '' }}">
                Liên hệ
            </a>
        </nav>

        <!-- Search & Actions -->
        <div class="search-wrapper position-relative d-none d-lg-block"
            id="headerSearchWrapper"
            style="width: 230px;">

            <form action="{{ route('user.shop') }}"
                method="GET"
                id="searchForm">

                <div class="custom-search-bar position-relative d-flex align-items-center">

                    <input type="text"
                        id="searchInput"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Tìm tên sách..."
                        class="form-control shadow-none"
                        autocomplete="off">

                    <button type="submit"
                            class="btn text-white d-flex align-items-center justify-content-center glow-pill-btn"
                            style="width:34px;height:34px;border-radius:50%;">

                        <i class="fas fa-search"></i>

                    </button>

                </div>

            </form>

            <div id="searchDropdown"
                class="search-dropdown-menu shadow-lg">

                <div class="search-dropdown-header">
                    <i class="fas fa-compass"></i>
                    Khám phá kho tàng tri thức
                </div>

                <div id="searchContentBox"
                    class="search-content-box">
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
            <a href="{{ route('user.wishlist') }}" class="header-icon-btn position-relative" title="Sách yêu thích">
                <i class="far fa-heart"></i>
                @if($wishlistCount > 0)
                    <span class="badge badge-danger rounded-circle header-badge">
                        {{ $wishlistCount }}
                    </span>
                @endif
            </a>
            
            <!-- GIỎ HÀNG -->
            <a href="{{ route('cart.index') }}" class="header-icon-btn position-relative" title="Giỏ hàng">
                <i class="fas fa-shopping-bag"></i>
                @if($cartCount > 0)
                    <span class="badge badge-danger rounded-circle header-badge">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
            
            <!-- THÔNG BÁO (THIẾT KẾ MỚI TỐI GIẢN, SANG TRỌNG, KHÔNG LỖI VIỀN) -->
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
                        <!-- Header tối giản, sang trọng -->
                        <div class="py-3 px-4 font-weight-bold d-flex align-items-center justify-content-between notification-dropdown-header" style="font-size: 14px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                            <span class="text-dark dark-mode-link"><i class="fas fa-bell mr-2" style="color: var(--primary-color, #D35400);"></i> Thông báo của bạn</span>
                            @if($count > 0)
                                <span class="badge badge-pill px-2 py-1" style="font-size: 11px; background: rgba(211,84,0,0.1); color: var(--primary-color, #D35400) !important;">{{ $count }} mới</span>
                            @endif
                        </div>

                        <!-- Danh sách thông báo -->
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

                        <!-- Footer thông báo -->
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
                <a href="{{ route('profile.edit') }}" class="user-profile-btn rounded-pill px-3 py-2 text-decoration-none shadow-sm d-flex align-items-center">
                    <i class="fas fa-user-circle mr-2" style="font-size: 20px; color: var(--primary-color, #D35400); flex-shrink: 0;"></i> 
                    <div class="user-name-container">
                        <span class="user-name-text" style="font-size: 14px; font-weight: 500;">{{ Auth::user()->name }}</span>
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn text-white text-decoration-none font-weight-bold shadow-sm glow-pill-btn px-4 py-2" style="border-radius: 50rem; white-space: nowrap; font-size: 14px;">
                    Đăng nhập
                </a>
            @endauth
        </div>
    </div>
</header>

<style>
    .modern-header {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .main-menu .menu-item {
        color: #555;
        font-weight: 500;
        font-size: 14.5px;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    
    .main-menu .menu-item:hover {
        color: var(--primary-color, #D35400);
        background-color: rgba(211, 84, 0, 0.05);
    }
    
    .main-menu .menu-item.active {
        color: var(--primary-color, #D35400);
        background-color: rgba(211, 84, 0, 0.08);
        font-weight: 600;
    }

    .glow-pill-btn {
        background: linear-gradient(135deg, #FF7A00, #D35400) !important;
        box-shadow: 0 0 12px 3px rgba(255, 122, 0, 0.35) !important;
        transition: all 0.25s ease !important;
        border: none !important;
    }
    .glow-pill-btn:hover {
        box-shadow: 0 0 18px 5px rgba(255, 122, 0, 0.55) !important;
        transform: translateY(-1px);
    }

    .custom-search-bar {
        background-color: #f8f9fa;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 50rem;
        padding: 3px 3px 3px 14px;
        height: 40px;
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .custom-search-bar:focus-within {
        background-color: #ffffff;
        border-color: rgba(211, 84, 0, 0.4);
        box-shadow: 0 4px 12px rgba(211, 84, 0, 0.1);
    }
    .custom-search-bar input {
        font-size: 14px;
        color: #333;
        height: 32px;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        border-radius: 50rem !important;
    }
    .custom-search-bar input::placeholder {
        color: #999;
    }

    html.dark-mode .custom-search-bar,
    body.dark-mode .custom-search-bar,
    .dark-mode .custom-search-bar {
        background-color: #212529 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    html.dark-mode .custom-search-bar:focus-within,
    body.dark-mode .custom-search-bar:focus-within,
    .dark-mode .custom-search-bar:focus-within {
        background-color: #2a2f35 !important;
        border-color: rgba(255, 153, 0, 0.4) !important;
        box-shadow: 0 4px 12px rgba(255, 153, 0, 0.15);
    }
    html.dark-mode .custom-search-bar input,
    body.dark-mode .custom-search-bar input,
    .dark-mode .custom-search-bar input {
        color: #fff !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .dropdown-menu-right {
        right: 0 !important;
        left: auto !important;
        transform: none !important;
    }

    .search-dropdown-menu {
        position: absolute;
        top: calc(100% + 10px);
        background-color: #ffffff;
        border-radius: 24px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
        overflow: hidden;
        display: none;
        z-index: 1060;
    }
    .show > .search-dropdown-menu,
    .dropdown.show .search-dropdown-menu,
    .search-wrapper.show .search-dropdown-menu { 
        display: block !important; 
    }

    .notification-item {
        border-radius: 12px !important;
        transition: background-color 0.2s ease;
    }
    .notification-item:hover {
        background-color: rgba(211, 84, 0, 0.06) !important;
    }
    .unread-notification {
        background-color: rgba(255, 122, 0, 0.04);
    }
    .delete-notif-btn {
        opacity: 0.5;
        transition: opacity 0.2s ease;
    }
    .notification-item:hover .delete-notif-btn {
        opacity: 1;
    }
    .delete-notif-btn:hover {
        color: #dc3545 !important;
    }
    .notification-footer {
        background-color: #f8f9fa !important;
        border-color: rgba(0,0,0,0.05) !important;
    }
    .notification-footer a:hover {
        text-decoration: underline !important;
    }
    
    .search-keyword-pill {
        background-color: #f8f9fa;
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }
    .search-keyword-pill:hover {
        background-color: rgba(211, 84, 0, 0.06);
        border-color: rgba(211, 84, 0, 0.2);
        transform: translateY(-1px);
    }

    .category-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }
    .category-card:hover {
        background-color: rgba(211, 84, 0, 0.05);
        transform: translateY(-2px);
    }

    html.dark-mode .search-dropdown-menu,
    body.dark-mode .search-dropdown-menu,
    .dark-mode .search-dropdown-menu {
        background-color: #1a1d20 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
    }
    html.dark-mode .notification-dropdown-header,
    body.dark-mode .notification-dropdown-header,
    .dark-mode .notification-dropdown-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    html.dark-mode .notification-item:hover,
    body.dark-mode .notification-item:hover,
    .dark-mode .notification-item:hover {
        background-color: rgba(255, 153, 0, 0.1) !important;
    }
    html.dark-mode .unread-notification,
    body.dark-mode .unread-notification,
    .dark-mode .unread-notification {
        background-color: rgba(255, 153, 0, 0.06) !important;
    }
    html.dark-mode .notification-footer,
    body.dark-mode .notification-footer,
    .dark-mode .notification-footer {
        background-color: #212529 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    html.dark-mode .search-keyword-pill,
    body.dark-mode .search-keyword-pill,
    .dark-mode .search-keyword-pill {
        background-color: rgba(255, 255, 255, 0.04) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    html.dark-mode .search-keyword-pill:hover,
    body.dark-mode .search-keyword-pill:hover,
    .dark-mode .search-keyword-pill:hover {
        background-color: rgba(255, 153, 0, 0.15) !important;
        border-color: rgba(255, 153, 0, 0.3) !important;
    }
    html.dark-mode .category-card:hover,
    body.dark-mode .category-card:hover,
    .dark-mode .category-card:hover {
        background-color: rgba(255, 153, 0, 0.1) !important;
    }

    html.dark-mode .search-item-result,
    body.dark-mode .search-item-result,
    .dark-mode .search-item-result {
        color: #e2e8f0 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    html.dark-mode .search-item-result:hover,
    body.dark-mode .search-item-result:hover,
    .dark-mode .search-item-result:hover {
        background-color: rgba(255, 153, 0, 0.1) !important;
    }
    html.dark-mode .search-item-result .text-dark,
    body.dark-mode .search-item-result .text-dark,
    .dark-mode .search-item-result .text-dark {
        color: #f8f9fa !important;
    }
    html.dark-mode .dark-mode-item,
    body.dark-mode .dark-mode-item,
    .dark-mode .dark-mode-item {
        background-color: transparent !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    html.dark-mode .dark-mode-link,
    body.dark-mode .dark-mode-link,
    .dark-mode .dark-mode-link {
        color: #e2e8f0 !important;
    }
    html.dark-mode .dark-mode-footer,
    body.dark-mode .dark-mode-footer,
    .dark-mode .dark-mode-footer {
        background-color: #212529 !important;
        color: #ff9900 !important;
    }
    html.dark-mode .dark-mode-header-text,
    body.dark-mode .dark-mode-header-text,
    .dark-mode .dark-mode-header-text {
        background-color: #212529 !important;
        color: #e2e8f0 !important;
    }

    .header-icon-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 42px !important;
        height: 42px !important;
        border-radius: 50% !important;
        background-color: #f1f2f6 !important;
        color: #2C3E50 !important;
        text-decoration: none !important;
        transition: all 0.25s ease !important;
        border: none !important;
        cursor: pointer !important;
        outline: none !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    
    html.dark-mode .header-icon-btn,
    body.dark-mode .header-icon-btn,
    .dark-mode .header-icon-btn {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #e2e8f0 !important;
    }

    .header-icon-btn:hover {
        background: linear-gradient(135deg, #FF7A00, #D35400) !important;
        box-shadow: 0 0 14px 4px rgba(255, 122, 0, 0.45) !important;
        color: #fff !important;
        transform: translateY(-2px) !important;
    }

    .header-icon-btn:hover i {
        color: #fff !important;
    }

    .header-icon-btn i {
        font-size: 17px !important;
        transition: color 0.2s ease;
    }

    .header-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        font-size: 10px;
        font-weight: 700;
        border: 2px solid #fff;
        padding: 2px 5px;
        line-height: 1;
        z-index: 2;
    }
    html.dark-mode .header-badge,
    body.dark-mode .header-badge,
    .dark-mode .header-badge {
        border-color: #161a1d !important;
    }

    .user-profile-btn {
        display: inline-flex;
        align-items: center;
        max-width: 170px;
        background-color: #f8f9fa;
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.2s ease;
    }
    .user-profile-btn:hover {
        border-color: var(--primary-color, #D35400);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    html.dark-mode .user-profile-btn,
    body.dark-mode .user-profile-btn,
    .dark-mode .user-profile-btn {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: #e2e8f0 !important;
    }

    .user-name-container {
        width: 100px;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
        -webkit-mask-image: linear-gradient(to right, #000 70%, transparent 100%);
        mask-image: linear-gradient(to right, #000 70%, transparent 100%);
    }
    
    .user-name-text {
        display: inline-block;
        padding-right: 15px;
        font-size: 14px;
    }
    
    .needs-marquee {
        animation: scrollTextMarquee 6s linear infinite alternate;
    }
    
    @keyframes scrollTextMarquee {
        0%, 20% { transform: translateX(0); }
        80%, 100% { transform: translateX(calc(100px - 100%)); }
    }

    .search-wrapper { z-index: 1050; }
    .search-dropdown-menu::-webkit-scrollbar { width: 6px; }
    .search-dropdown-menu::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.15); border-radius: 10px; }

    




    /* CSS tìm kiếm dropdown */
    /* =====================================================
    SEARCH DROPDOWN
    ===================================================== */

    .search-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 440px;
        max-height: 420px;
        overflow-y: auto;

        background: #fff;
        border-radius: 0 0 22px 22px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);

        z-index: 9999;
    }


    /* =====================================================
    HEADER - KHÁM PHÁ KHO TÀNG TRI THỨC
    ===================================================== */

    .search-dropdown-header {
        background: linear-gradient(135deg, #ff7a00, #d35400);
        color: #fff;

        padding: 12px 16px;

        font-size: 14px;
        font-weight: 700;

        display: flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        border-radius: 22px 22px 0 0;
    }

    .search-dropdown-header i {
        font-size: 13px;
    }


    /* =====================================================
    CONTENT
    ===================================================== */

    .search-content-box {
        padding: 12px 14px 14px;
    }


    /* =====================================================
    TIÊU ĐỀ SECTION
    ===================================================== */

    .search-section-title {
        display: flex;
        align-items: center;

        margin: 4px 0 8px;

        font-size: 13px;
        font-weight: 700;

        color: #333;
    }

    .search-section-title i {
        color: #d35400;
        margin-right: 8px;
        font-size: 13px;
    }


    /* =====================================================
    TÌM KIẾM GẦN ĐÂY
    ===================================================== */

    .recent-search-list {
        display: flex;
        flex-wrap: wrap;

        gap: 7px;

        margin-bottom: 12px;
    }

    .recent-search-item {
        display: flex;
        align-items: center;

        padding: 7px 10px;

        background: #f5f5f5;
        border-radius: 7px;

        font-size: 12px;
        color: #555;

        cursor: pointer;

        transition: 0.2s;
    }

    .recent-search-item i {
        margin-right: 7px;
        color: #888;
        font-size: 11px;
    }

    .recent-search-item:hover {
        background: #fff1e6;
        color: #d35400;
    }


    /* =====================================================
    TỪ KHÓA HOT
    ===================================================== */

    .hot-keyword-grid {
        display: grid;

        grid-template-columns: 1fr 1fr;

        column-gap: 18px;
        row-gap: 7px;

        margin-bottom: 14px;
    }

    .hot-keyword-item {
        display: flex;
        align-items: center;

        min-height: 34px;

        padding: 6px 8px;

        border-radius: 7px;

        font-size: 13px;
        color: #555;

        cursor: pointer;

        transition: 0.2s;
    }

    .hot-keyword-item:hover {
        background: #fff5ed;
        color: #d35400;
    }


    /* số #1 #2 #3 #4 */

    .hot-number {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 25px;
        height: 25px;

        margin-right: 8px;

        border-radius: 50%;

        background: #fff1e6;
        color: #d35400;

        font-size: 11px;
        font-weight: 700;

        flex-shrink: 0;
    }


    /* =====================================================
    DANH MỤC
    ===================================================== */

    .category-grid {
        display: grid;

        grid-template-columns: repeat(4, 1fr);

        gap: 6px;

        text-align: center;

        margin-top: 2px;
    }

    .category-item {
        display: flex;

        flex-direction: column;
        align-items: center;

        text-decoration: none;

        padding: 4px;

        color: #333;

        transition: 0.2s;
    }

    .category-item img {
        width: 52px;
        height: 68px;

        object-fit: cover;

        border-radius: 4px;

        border: 1px solid #ddd;

        margin-bottom: 5px;
    }

    .category-name {
        width: 100%;

        font-size: 11px;
        font-weight: 600;

        line-height: 1.25;

        color: #444;
    }

    .category-item:hover .category-name {
        color: #d35400;
    }


    /* =====================================================
    MOBILE
    ===================================================== */

    @media (max-width: 576px) {

        .search-dropdown-menu {
            width: 350px;
            max-width: calc(100vw - 20px);
        }

        .hot-keyword-grid {
            column-gap: 5px;
        }

        .category-item img {
            width: 45px;
            height: 60px;
        }
    }
    /* =====================================================
    KẾT QUẢ SẢN PHẨM KHI NHẬP TỪ KHÓA
    ===================================================== */

    .search-product-item {
        display: flex;
        align-items: center;

        width: 100%;

        padding: 8px;

        margin-bottom: 6px;

        text-decoration: none;

        border-radius: 8px;

        color: #333;

        transition: 0.2s;

        overflow: hidden;
    }

    .search-product-item:hover {
        background: #fff5ed;
        text-decoration: none;
    }


    /* Ảnh sách */

    .search-product-img {
        width: 48px !important;
        height: 64px !important;

        min-width: 48px;
        min-height: 64px;

        object-fit: cover;

        border-radius: 4px;

        border: 1px solid #ddd;

        margin-right: 10px;
    }


    /* Khối thông tin */

    .search-product-item > div {
        flex: 1;

        min-width: 0;
    }


    /* Tên sách */

    .search-product-name {
        font-size: 13px;

        font-weight: 600;

        color: #333;

        line-height: 1.35;

        margin-bottom: 4px;

        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;

        overflow: hidden;
    }


    /* Giá */

    .search-product-price {
        font-size: 12px;

        font-weight: 600;

        color: #d35400;
    }


    /* Không tìm thấy */

    .search-empty {
        text-align: center;

        padding: 25px 10px;

        color: #777;

        font-size: 13px;
    }
    /* =====================================================
    CÂN CHỈNH CỤM BÊN PHẢI HEADER
    ===================================================== */

    /* Ô tìm kiếm */
    #headerSearchWrapper {
        width: 260px !important;
    }

    #headerSearchWrapper .custom-search-bar {
        height: 48px;
    }

    #headerSearchWrapper #searchInput {
        height: 48px;
        font-size: 14px;
        padding-left: 18px;
        padding-right: 50px;
    }

    /* Nút kính lúp */
    #headerSearchWrapper .glow-pill-btn {
        width: 38px !important;
        height: 38px !important;
        right: 5px;
    }


    /* =====================================================
    YÊU THÍCH / GIỎ HÀNG / THÔNG BÁO
    ===================================================== */

    .header-icon-btn {
        width: 48px;
        height: 48px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        font-size: 16px;

        flex-shrink: 0;

        text-decoration: none;

        transition: all 0.2s ease;
    }

    .header-icon-btn i {
        font-size: 16px;
    }

    .header-icon-btn:hover {
        transform: translateY(-1px);
    }


    /* =====================================================
    NÚT ĐĂNG NHẬP
    ===================================================== */

    a.glow-pill-btn {
        min-width: 120px;
        height: 48px;

        display: inline-flex !important;
        align-items: center;
        justify-content: center;

        padding: 0 24px !important;

        border-radius: 50rem !important;

        font-size: 14px !important;

        white-space: nowrap;
    }


    /* =====================================================
    CĂN GIỮA TOÀN BỘ CỤM ACTION
    ===================================================== */

    .header-icon-btn,
    a.glow-pill-btn,
    #headerSearchWrapper {
        margin-top: 0;
        margin-bottom: 0;
    }
</style>

<script src="{{ asset('js/search.js') }}"></script>