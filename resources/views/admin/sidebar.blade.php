<!-- CSS Custom đồng bộ màu Sub-menu với Menu cha -->
<style>
    /* Chuyển nền của box chứa sub-menu thành trong suốt hoặc cùng tông tối */
    .sidebar .collapse-inner {
        background-color: rgba(0, 0, 0, 0.15) !important;
    }
    /* Chuyển màu chữ các item con thành màu trắng đục giống menu cha */
    .sidebar .collapse-inner .collapse-item {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 400;
        transition: all 0.2s ease;
    }
    /* Effect khi Hover vào item con */
    .sidebar .collapse-inner .collapse-item:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
    /* Trạng thái Active của item con */
    .sidebar .collapse-inner .collapse-item.active {
        color: #ffffff !important;
        font-weight: 700 !important;
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand Logo -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin/dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-book-open" style="color: var(--admin-orange, #f0f0f0);"></i>
        </div>
        <div class="sidebar-brand-text mx-3 serif-font" style="text-transform: none; font-size: 22px; letter-spacing: 0;">
            SachHay<span style="color: var(--admin-orange, #bebebe);">.</span>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Quản lý hệ thống</div>

    <!-- Nhánh Gộp 1: Quản lý Sản phẩm -->
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

    <!-- Nhánh Gộp 2: Quản lý Tài khoản -->
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
                <a class="collapse-item {{ request()->routeIs('admin.manage*') ? 'active' : '' }}" href="{{ route('admin.manage') }}">Duyệt tài khoản Admin</a>
                <a class="collapse-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Quản lý Người dùng</a>
            </div>
        </div>
    </li>

    <!-- Các menu khác -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.vouchers.index') }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Quản lý Voucher</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orders') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Quản lý Đơn hàng</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.news*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.news.index') }}">
            <i class="fas fa-fw fa-newspaper"></i>
            <span>Quản lý Tin tức</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.reviews.index') }}">
            <i class="fas fa-fw fa-comments"></i>
            <span>Quản lý Bình luận</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.banners.index') }}">
            <i class="fas fa-fw fa-images"></i>
            <span>Quản lý Banner</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.contact.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.contact.index') }}">
            <i class="fas fa-fw fa-envelope"></i>
            <span>Quản lý Liên hệ</span>
        </a>
    </li>
</ul>