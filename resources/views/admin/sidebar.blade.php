<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin/dashboard') }}">
        <div class="sidebar-brand-icon">
            <!-- Đổi thành icon quyển sách và tô màu Cam cháy -->
            <i class="fas fa-book-open" style="color: var(--admin-orange, #f0f0f0);"></i>
        </div>
        <!-- Đổi tên thương hiệu, dùng font Serif sang trọng -->
        <div class="sidebar-brand-text mx-3 serif-font" style="text-transform: none; font-size: 22px; letter-spacing: 0;">
            SachHay<span style="color: var(--admin-orange, #bebebe);">.</span>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Quản lý hệ thống</div>

    <li class="nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.products') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Quản lý Sản phẩm</span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.categories') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Quản lý Danh mục</span>
        </a>
    </li>
    
    <li class="nav-item {{ request()->routeIs('admin.publishers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.publishers.index') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>Quản lý Nhà xuất bản</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.authors*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.authors') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Quản lý Tác giả</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orders') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Quản lý Đơn hàng</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.manage*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.manage') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Duyệt tài khoản Admin</span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.reviews.index') }}">
            <i class="fas fa-fw fa-comments"></i>
            <span>Quản lý Bình luận</span>
        </a>
    </li>
</ul>