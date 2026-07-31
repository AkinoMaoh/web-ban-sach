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
        <div class="d-flex align-items-center" style="gap: 12px;">
            
            <!-- Ô TÌM KIẾM AJAX -->
            <div class="search-wrapper position-relative d-none d-lg-block" id="headerSearchWrapper" style="width: 230px;">
                <form action="{{ route('user.shop') }}" method="GET" id="searchForm">
                    <div class="custom-search-bar position-relative d-flex align-items-center">
                        <input type="text" id="searchInput" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm tên sách..." class="form-control shadow-none" autocomplete="off">
                        <button class="btn text-white d-flex align-items-center justify-content-center custom-search-btn" type="submit" aria-label="Tìm kiếm">
                            <i class="fas fa-search" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </form>

                <!-- Hộp Dropdown kết quả tìm kiếm -->
                <div class="search-dropdown-menu shadow-lg mt-2" id="searchDropdown" style="width: 440px; right: 0; left: auto; max-height: 420px; overflow-y: auto;">
                    <div class="text-white text-center py-3 px-3 font-weight-bold sticky-top search-dropdown-header d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #FF7A00, #D35400); font-size: 14px; letter-spacing: 0.3px; gap: 8px;">
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
            
            <!-- THÔNG BÁO -->
            @auth
                @php
                    $notifs = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
                    $count = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                <div class="dropdown">
                    <a class="header-icon-btn position-relative" href="#" data-toggle="dropdown" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        @if($count > 0) 
                            <span class="badge badge-danger rounded-circle header-badge">{{ $count }}</span> 
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0 mt-2 search-dropdown-menu" style="width: 300px; z-index: 9999; border-radius: 16px; overflow: hidden;">
                        <h6 class="dropdown-header text-uppercase font-weight-bold py-2 bg-light dark-mode-header-text">Thông báo</h6>
                        @forelse($notifs as $n)
                            <div class="dropdown-item d-flex justify-content-between align-items-start py-2 border-bottom dark-mode-item {{ !$n->is_read ? 'font-weight-bold bg-light' : '' }}">
                                <a href="{{ route('notifications.redirect', $n->id) }}" class="text-dark text-decoration-none dark-mode-link" style="white-space: normal; line-height: 1.4; font-size: 13px;">
                                    {{ $n->message }}
                                </a>
                                <form action="{{ route('notifications.delete', $n->id) }}" method="POST" class="ml-2" onsubmit="return confirm('Xóa thông báo?')">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0"><i class="fas fa-times"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="dropdown-item text-muted text-center py-3">Chưa có thông báo</div>
                        @endforelse
                        <a class="dropdown-item text-center text-primary font-weight-bold py-2 bg-white dark-mode-footer" href="{{ route('notifications.read.all') }}">Đánh dấu đã đọc</a>
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
    }
    .glow-pill-btn:hover {
        box-shadow: 0 0 18px 5px rgba(255, 122, 0, 0.55) !important;
        transform: translateY(-1px);
    }

    /* THANH TÌM KIẾM BO TRÒN TUYỆT ĐỐI */
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
    .custom-search-btn {
        background: linear-gradient(135deg, #FF7A00, #D35400);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        flex-shrink: 0;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(211, 84, 0, 0.3);
    }
    .custom-search-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(211, 84, 0, 0.4);
    }

    /* Dark Mode cho thanh tìm kiếm */
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

    /* Dropdown tìm kiếm */
    .search-dropdown-menu {
        position: absolute;
        top: calc(100% + 10px);
        background-color: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
        overflow: hidden;
        display: none;
        animation: dropdownFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1060;
    }
    .search-wrapper.show .search-dropdown-menu { display: block; }
    
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

    /* Dark Mode cho Dropdown */
    html.dark-mode .search-dropdown-menu,
    body.dark-mode .search-dropdown-menu,
    .dark-mode .search-dropdown-menu {
        background-color: #1a1d20 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
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
        transition: all 0.2s ease !important;
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
        background-color: var(--primary-color, #D35400) !important;
        color: #fff !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 10px rgba(211, 84, 0, 0.25) !important;
    }

    .header-icon-btn i {
        font-size: 17px !important;
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
    
    @keyframes dropdownFadeIn { 
        from { opacity: 0; transform: translateY(12px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
</style>

<script>
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

    const searchInput = document.getElementById('searchInput');
    const searchWrapper = document.getElementById('headerSearchWrapper');
    const searchContentBox = document.getElementById('searchContentBox');
    let typingTimer;
    let cachedSuggestions = null;

    if(searchInput && searchWrapper) {
        searchInput.addEventListener('focus', function() {
            searchWrapper.classList.add('show');
            performSearch(this.value);
        });

        document.addEventListener('click', function(event) {
            if (!searchWrapper.contains(event.target)) {
                searchWrapper.classList.remove('show');
            }
        });

        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            let keyword = this.value;
            typingTimer = setTimeout(() => performSearch(keyword), 300);
        });
    }

    function performSearch(keyword) {
        if(keyword === '' && cachedSuggestions !== null) {
            renderSuggestions(cachedSuggestions);
            return;
        }

        let url = `{{ route('api.search') }}?keyword=${encodeURIComponent(keyword)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if(data.status === 'suggestions') {
                    cachedSuggestions = data;
                    renderSuggestions(data);
                } else if(data.status === 'products') {
                    renderProducts(data.data, keyword);
                }
            })
            .catch(error => {
                searchContentBox.innerHTML = `<div class="text-center text-danger my-3">Có lỗi xảy ra khi tìm kiếm.</div>`;
            });
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