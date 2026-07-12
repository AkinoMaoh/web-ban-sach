<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Nút chuyển đổi chế độ sáng/tối -->
        <li class="nav-item mr-3">
            <button id="theme-toggle"
                    type="button"
                    class="btn btn-light"
                    style="border-radius: 6px; border: 1px solid var(--admin-orange); color: var(--admin-orange);">
                <i class="bi bi-moon-fill"></i>
            </button>
        </li>

        <!-- SCRIPT FIX NHÁY GIAO DIỆN (Thực thi ngay lập tức) -->
        <script>
            (function() {
                const root = document.documentElement;
                const toggle = document.getElementById("theme-toggle");
                if (localStorage.getItem("theme") === "dark") {
                    root.classList.add("dark-mode");
                    toggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
                }
            })();
        </script>

        <!-- Thông báo Admin (Ngay sau nút Dark Mode) -->
        @if(Auth::check())
            @php
                // Lấy thông báo theo ID của Admin hiện tại
                $notifs = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
                $count = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
            @endphp
            <li class="nav-item dropdown no-arrow mr-3">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw" style="color: var(--admin-orange); font-size: 1.2rem;"></i>
                    <!-- Counter - Alerts -->
                    @if($count > 0)
                        <span class="badge badge-danger badge-counter" style="position: absolute; top: 15px; right: 5px;">{{ $count }}</span>
                    @endif
                </a>
                
                <!-- Dropdown - Alerts -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in border-0 mt-2" aria-labelledby="alertsDropdown" style="width: 320px; border-radius: 10px;">
                    <h6 class="dropdown-header " style="background-color: var(--admin-orange); border-color: var(--admin-orange); border-radius: 10px 10px 0 0;">
                        Thông báo đơn hàng mới
                    </h6>
                    
                    @forelse($notifs as $n)
                        <div class="dropdown-item d-flex justify-content-between align-items-start py-3 border-bottom {{ !$n->is_read ? 'bg-light font-weight-bold' : '' }}">
                            <a href="{{ route('notifications.redirect', $n->id) }}" class="text-dark text-decoration-none" style="white-space: normal; line-height: 1.4; font-size: 13px; width: 90%;">
                                {{ $n->message }}
                            </a>
                            <form action="{{ route('notifications.delete', $n->id) }}" method="POST" class="ml-2" onsubmit="return confirm('Xóa thông báo này?')">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                    @empty
                        <div class="dropdown-item text-center text-muted small py-4">
                            Không có thông báo mới
                        </div>
                    @endforelse
                    
                    <a class="dropdown-item text-center small text-primary py-2 font-weight-bold" href="{{ route('notifications.read.all') }}">
                        Đánh dấu đã đọc tất cả
                    </a>
                </div>
            </li>
        @endif

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle"
               href="#"
               id="userDropdown"
               role="button"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-800 small font-weight-bold">
                    {{ Auth::check() ? Auth::user()->name : 'Admin' }}
                </span>

                <img class="img-profile rounded-circle border"
                     src="{{ asset('img/undraw_profile.svg') }}">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow-sm animated--grow-in border-0 mt-2"
                 aria-labelledby="userDropdown"
                 style="border-radius: 10px;">

                <a class="dropdown-item py-2" href="{{ route('admin.profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Hồ sơ cá nhân
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item py-2"
                   href="#"
                   data-toggle="modal"
                   data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                    Đăng xuất
                </a>

            </div>
        </li>

    </ul>
</nav>

<script>
    // Logic Click cho Dark Mode
    document.getElementById("theme-toggle").addEventListener("click", function (e) {
        e.preventDefault();
        const root = document.documentElement;
        root.classList.toggle("dark-mode");
        
        if (root.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
            this.innerHTML = '<i class="bi bi-sun-fill"></i>';
        } else {
            localStorage.setItem("theme", "light");
            this.innerHTML = '<i class="bi bi-moon-fill"></i>';
        }
    });
</script>