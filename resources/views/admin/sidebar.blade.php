<!-- CSS Custom đồng bộ màu Sub-menu với Menu cha -->
<style>
    .sidebar .collapse-inner {
        background-color: rgba(0, 0, 0, 0.15) !important;
    }
    .sidebar .collapse-inner .collapse-item {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 400;
        transition: all 0.2s ease;
    }
    .sidebar .collapse-inner .collapse-item:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
    .sidebar .collapse-inner .collapse-item.active {
        color: #ffffff !important;
        font-weight: 700 !important;
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand Logo -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ Auth::check() && Auth::user()->role == 1 ? url('/admin/dashboard') : route('admin.products') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-book-open" style="color: var(--admin-orange, #f0f0f0);"></i>
        </div>
        <div class="sidebar-brand-text mx-3 serif-font" style="text-transform: none; font-size: 22px; letter-spacing: 0;">
            SachHay<span style="color: var(--admin-orange, #bebebe);">.</span>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    {{-- ==================== CHỈ ADMIN TỐI CAO MỚI THẤY KHỐI NÀY ==================== --}}
    @if(Auth::check() && (Auth::id() == 1 || Auth::user()->email === 'ankinoto20@gmail.com'))

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Quản trị hệ thống</div>

    <!-- Quản lý Tài khoản -->
    @php
        $isUserGroupActive = request()->routeIs('admin.manage*') || 
                             request()->routeIs('admin.users*');
    @endphp
    <li class="nav-item {{ $isUserGroupActive ? 'active' : '' }}">
        <a class="nav-link {{ $isUserGroupActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseUserManagement"
            aria-expanded="{{ $isUserGroupActive ? 'true' : 'false' }}" aria-controls="collapseUserManagement">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Quản lý Tài khoản</span>
        </a>
        <div id="collapseUserManagement" class="collapse {{ $isUserGroupActive ? 'show' : '' }}" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('admin.manage*') ? 'active' : '' }}" href="{{ route('admin.manage') }}">Tài khoản nhân viên</a>
                <a class="collapse-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Tài khoản Khách hàng</a>
            </div>
        </div>
    </li>

    <!-- Quản lý Banner -->
    <li class="nav-item {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.banners.index') }}">
            <i class="fas fa-fw fa-images"></i>
            <span>Quản lý Banner</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    @endif
    {{-- ==================== KẾT THÚC KHỐI ADMIN TỐI CAO ==================== --}}


    {{-- ==================== DÀNH CHO CẢ ADMIN VÀ NHÂN VIÊN ==================== --}}
    <div class="sidebar-heading">Quản lý bán hàng</div>

    <!-- Quản lý Sản phẩm -->
    @php
        $isProductGroupActive = request()->routeIs('admin.products*') || 
                                request()->routeIs('admin.variants*') || 
                                request()->routeIs('admin.categories*') || 
                                request()->routeIs('admin.authors*') || 
                                request()->routeIs('admin.publishers*');
    @endphp
    <li class="nav-item {{ $isProductGroupActive ? 'active' : '' }}">
        <a class="nav-link {{ $isProductGroupActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseProductManagement"
            aria-expanded="{{ $isProductGroupActive ? 'true' : 'false' }}" aria-controls="collapseProductManagement">
            <i class="fas fa-fw fa-box"></i>
            <span>Sản phẩm</span>
        </a>
        <div id="collapseProductManagement" class="collapse {{ $isProductGroupActive ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products') }}">Danh sách sản phẩm</a>
                <a class="collapse-item {{ request()->routeIs('admin.variants*') ? 'active' : '' }}" href="{{ route('admin.variants') }}">Quản lý Biến thể</a>
                <a class="collapse-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">Quản lý Danh mục</a>
                <a class="collapse-item {{ request()->routeIs('admin.authors*') ? 'active' : '' }}" href="{{ route('admin.authors') }}">Quản lý Tác giả</a>
                <a class="collapse-item {{ request()->routeIs('admin.publishers*') ? 'active' : '' }}" href="{{ route('admin.publishers.index') }}">Quản lý NXB</a>
            </div>
        </div>
    </li>

    <!-- Quản lý Đơn hàng -->
    <li class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orders') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Quản lý Đơn hàng</span>
        </a>
    </li>

    <!-- Quản lý Voucher -->
    <li class="nav-item {{ request()->routeIs('admin.vouchers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.vouchers.index') }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Quản lý Voucher</span>
        </a>
    </li>

    <!-- Quản lý Tin tức -->
    <li class="nav-item {{ request()->routeIs('admin.news*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.news.index') }}">
            <i class="fas fa-fw fa-newspaper"></i>
            <span>Quản lý Tin tức</span>
        </a>
    </li>

    <!-- Quản lý Tương tác (Gộp Bình luận & Liên hệ) -->
    @php
        $isInteractionGroupActive = request()->routeIs('admin.reviews*') || 
                                   request()->routeIs('admin.contact*');
    @endphp
    <li class="nav-item {{ $isInteractionGroupActive ? 'active' : '' }}">
        <a class="nav-link {{ $isInteractionGroupActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseInteractionManagement"
            aria-expanded="{{ $isInteractionGroupActive ? 'true' : 'false' }}" aria-controls="collapseInteractionManagement">
            <i class="fas fa-fw fa-comments"></i>
            <span>Quản lý Tương tác</span>
        </a>
        <div id="collapseInteractionManagement" class="collapse {{ $isInteractionGroupActive ? 'show' : '' }}" aria-labelledby="headingInteraction" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">Bình luận & Đánh giá</a>
                <a class="collapse-item {{ request()->routeIs('admin.contact*') ? 'active' : '' }}" href="{{ route('admin.contact.index') }}">Liên hệ từ khách hàng</a>
            </div>
        </div>
    </li>

</ul>