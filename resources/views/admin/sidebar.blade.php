<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin/dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
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
    <li class="nav-item {{ request()->routeIs('admin.authors*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.authors') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Quản lý Nhà xuất bản</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Quản lý Đơn hàng</span>
        </a>
    </li>
</ul>