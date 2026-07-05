<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">
    
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" style="color: var(--admin-orange);">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Tìm kiếm tài liệu, đơn hàng..." aria-label="Search" style="border-radius: 6px 0 0 6px;">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" style="border-radius: 0 6px 6px 0;">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-800 small font-weight-bold">
                    {{ Auth::check() ? Auth::user()->name : 'Admin' }}
                </span>
                <!-- Có thể thay logo này bằng một logo admin nam/nữ hoặc logo sách cho đẹp -->
                <img class="img-profile rounded-circle border" src="{{ asset('img/undraw_profile.svg') }}">
            </a>
            
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow-sm animated--grow-in border-0 mt-2" aria-labelledby="userDropdown" style="border-radius: 10px;">
                <a class="dropdown-item py-2" href="{{ route('admin.profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Hồ sơ cá nhân
                </a>
                
                <div class="dropdown-divider"></div>
                
                <a class="dropdown-item py-2" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i> Đăng xuất
                </a>
            </div>
        </li>
    </ul>
</nav>