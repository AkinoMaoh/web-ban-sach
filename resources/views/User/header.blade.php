<header class="modern-header">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a href="{{ route('user.index') }}" class="text-decoration-none mr-2">
            <h2 class="serif-font mb-0" style="color: var(--primary-color, #D35400);">SachHay.</h2>
        </a>
        
        <!-- Menu -->
        <nav class="main-menu d-flex align-items-center">
            <a href="{{ route('user.index') }}" class="menu-item {{ request()->routeIs('user.index') ? 'active' : '' }}">
                Trang chủ
            </a>
            <a href="{{ route('user.shop') }}" class="menu-item {{ request()->routeIs('user.shop','user.category', 'user.productDetails') ? 'active' : '' }}">
                Tủ sách
            </a>
            <a href="{{ route('user.news') }}" class="menu-item {{ request()->routeIs('user.news', 'user.news.show') ? 'active' : '' }}">
                Tin tức
            </a>
            <a href="{{ route('user.contact') }}" class="menu-item {{ request()->routeIs('user.contact') ? 'active' : '' }}">
                Liên hệ
            </a>
        </nav>

        <!-- Search & Auth -->
        <div class="d-flex align-items-center">
            
            <!-- BẮT ĐẦU: KHU VỰC TÌM KIẾM AJAX -->
            <div class="search-wrapper position-relative mr-3 d-none d-lg-block" id="headerSearchWrapper" style="width: 230px;">
                <form action="{{ route('user.shop') }}" method="GET" id="searchForm">
                    <div class="input-group search-input-group shadow-sm" style="border-radius: 25px; overflow: hidden; background-color: #f1f2f6; border: 1px solid #e0e0e0;">
                        <input type="text" id="searchInput" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm tên sách..." class="form-control border-0 bg-transparent shadow-none" style="padding: 8px 15px; font-size: 14px;" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn text-white px-3" type="submit" style="background: linear-gradient(135deg, #FF7A00, #D35400); border: none;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Hộp Dropdown Trống để JS đổ dữ liệu vào -->
                <div class="search-dropdown-menu shadow-lg rounded bg-white mt-2" id="searchDropdown" style="width: 450px; right: 0; left: auto; max-height: 400px; overflow-y: auto;">
                    <div class="text-white text-center py-2 font-weight-bold sticky-top" style="background: linear-gradient(90deg, #FF7A00, #ff9900); border-radius: 6px 6px 0 0; font-size: 14px; z-index: 10;">
                        Khám phá kho tàng tri thức
                    </div>
                    
                    <!-- Khung chứa nội dung được Render từ JS -->
                    <div class="p-3" id="searchContentBox">
                        <div class="text-center text-muted my-3"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tải dữ liệu...</div>
                    </div>
                </div>
            </div>
            <!-- KẾT THÚC: KHU VỰC TÌM KIẾM AJAX -->

            <!-- ICON YÊU THÍCH -->
            <a href="{{ route('user.wishlist') }}" class="text-dark mr-2 position-relative" title="Sách yêu thích">
                <i class="far fa-heart fa-lg"></i>
            </a>
            
            <!-- GIỎ HÀNG -->
            @php
                if(auth()->check()){
                    $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count();
                }else{
                    $cartCount = count(session('cart', []));
                }
            @endphp
            <a href="{{ route('cart.index') }}" class="text-dark mr-2 position-relative">
                <i class="fas fa-shopping-bag fa-lg"></i>
                @if($cartCount > 0)
                    <span class="badge badge-danger rounded-circle position-absolute" style="top: -8px; right: -8px; font-size: 10px;">
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
                <div class="dropdown mr-2">
                    <a class="text-dark position-relative" href="#" data-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        @if($count > 0) <span class="badge badge-danger rounded-circle" style="position: absolute; top: -10px; right: -10px; font-size: 10px;">{{ $count }}</span> @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="width: 300px; z-index: 9999;">
                        <h6 class="dropdown-header">Thông báo</h6>
                        @forelse($notifs as $n)
                            <div class="dropdown-item d-flex justify-content-between align-items-start py-2 {{ !$n->is_read ? 'font-weight-bold' : '' }}">
                                <a href="{{ route('notifications.redirect', $n->id) }}" class="text-dark text-decoration-none" style="white-space: normal; line-height: 1.4; font-size: 14px;">
                                    {{ $n->message }}
                                </a>
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

            <!-- ĐỔI GIAO DIỆN SÁNG / TỐI -->
            <button id="theme-toggle" type="button" class="btn mr-2" title="Chuyển giao diện" style="padding: 0 5px;">
                <i class="fas fa-moon fa-lg"></i>
            </button>
            
            <!-- USER / LOGIN -->
            @auth
                <a href="{{ route('profile.edit') }}" class="text-dark font-weight-bold text-decoration-none" style="white-space: nowrap;">
                    <i class="fas fa-user-circle mr-1" style="color: var(--primary-color, #D35400);"></i> {{ Auth::user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn text-white text-decoration-none font-weight-bold" style="background-color: var(--primary-color, #D35400); border-radius: 20px; padding: 6px 18px; white-space: nowrap;">
                    Đăng nhập
                </a>
            @endauth
        </div>
    </div>
</header>

<style>
    .main-menu .menu-item { white-space: nowrap !important; margin: 0 5px; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hover-primary { transition: color 0.2s ease, background-color 0.2s ease; }
    .hover-primary:hover { color: var(--primary-color, #D35400) !important; }

    .search-wrapper { z-index: 1050; }
    .search-input-group:focus-within { box-shadow: 0 0 0 0.2rem rgba(211, 84, 0, 0.25) !important; border-color: var(--primary-color, #D35400) !important; background-color: #fff !important; }
    .search-input-group input:focus { outline: none; box-shadow: none; }

    .search-dropdown-menu { position: absolute; top: 100%; display: none; border: 1px solid #e0e0e0; animation: fadeIn 0.2s ease-in-out; }
    .search-wrapper.show .search-dropdown-menu { display: block; }
    
    .search-dropdown-menu::-webkit-scrollbar { width: 6px; }
    .search-dropdown-menu::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }

    .search-item-result { transition: background-color 0.2s; border-radius: 6px; }
    .search-item-result:hover { background-color: #FFF6F0; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("theme-toggle");
    const root = document.documentElement;

    if (btn) {
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
            btn.innerHTML = isDark ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
        });
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
                    <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 14px;">
                        <i class="fas fa-chart-line mr-2" style="color: #D35400;"></i> Từ khóa hot
                    </h6>
                </div>
                <div class="row mb-3">
            `;
            data.hot_keywords.forEach((kw, index) => {
                html += `
                    <div class="col-6 mb-2">
                        <a href="/shop?keyword=${encodeURIComponent(kw)}" 
                           class="d-flex align-items-center text-decoration-none text-dark p-1 rounded" style="background: #f8f9fa;">
                            <span class="font-weight-bold" style="font-size: 12px; padding-left: 5px;">${index + 1}. ${kw}</span>
                        </a>
                    </div>
                `;
            });
            html += `</div><hr class="my-2 border-light">`;
        }

        if(data.categories && data.categories.length > 0) {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 14px;">
                        <i class="fas fa-th-large mr-2" style="color: var(--primary-color, #D35400);"></i> Danh mục nổi bật
                    </h6>
                </div>
                <div class="row text-center mt-2">
            `;
            data.categories.forEach(cat => {
                html += `
                    <div class="col-3 px-1">
                        <a href="/shop/category/${cat.id}" class="text-decoration-none text-dark d-block hover-primary">
                            <img src="${cat.image}" class="rounded shadow-sm mb-1 object-fit-cover border" style="width: 45px; height: 60px;" alt="${cat.name}">
                            <div class="font-weight-bold text-truncate-2" style="font-size: 11px;">${cat.name}</div>
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

        let html = `<div class="font-weight-bold mb-2 text-muted" style="font-size: 13px;">Sách tìm thấy</div>`;
        
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
                <a href="/product/${product.id}" class="d-flex align-items-center p-2 mb-2 search-item-result border-bottom text-decoration-none text-dark">
                    <img src="${img}" style="width: 50px; height: 70px;" class="object-fit-cover rounded mr-3 shadow-sm border" alt="${product.name}">
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="mb-1 text-truncate-2 font-weight-bold" style="font-size: 14px; line-height: 1.4;">${product.name}</h6>
                        <div class="text-muted mb-1" style="font-size: 12px;"><i class="fas fa-pen-nib mr-1"></i> ${authorName}</div>
                        <div>${priceHtml}</div>
                    </div>
                </a>
            `;
        });
        
        html += `
            <div class="text-center mt-3">
                <a href="/shop?keyword=${encodeURIComponent(keyword)}" onclick="document.getElementById('searchForm').submit(); return false;" class="text-decoration-none font-weight-bold hover-primary" style="font-size: 14px;">
                    Xem tất cả kết quả <i class="fas fa-angle-right"></i>
                </a>
            </div>
        `;

        searchContentBox.innerHTML = html;
    }

});
</script>