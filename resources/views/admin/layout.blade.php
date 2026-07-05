<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Trang Quản Trị - SachHay</title>
    <script>
if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark-mode');
}
</script>

    <!-- DÙNG LINK CDN ĐỂ CHỐNG LỖI MẤT FILE CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Link CSS gốc của SB Admin 2 lấy trực tiếp từ máy chủ -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- CSS ĐỒNG BỘ GIAO DIỆN NHÀ SÁCH CHO ADMIN -->
    <!-- ĐỒNG BỘ MÀU SẮC: XANH DƯƠNG - TRẮNG -->
    <!-- ĐỒNG BỘ MÀU SẮC: SIDEBAR XANH DƯƠNG - CHỮ TRẮNG -->
    <style>
        :root {
            --admin-primary: #1A73E8; /* Xanh dương Google */
        }
        
        .serif-font { font-family: 'Lora', serif; }

        /* 1. Đổi màu Sidebar sang XANH DƯƠNG GRADIENT */
        .bg-gradient-primary, 
        #accordionSidebar {
            background-color: var(--admin-primary) !important;
            /* Tạo hiệu ứng dải màu xanh từ nhạt xuống đậm cho đỡ nhàm chán */
            background-image: linear-gradient(180deg, #1A73E8 10%, #1557B0 100%) !important;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1) !important;
        }

        /* 2. Sửa màu chữ Menu Sidebar (thành màu trắng hơi trong suốt) */
        .sidebar-dark .nav-item .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
        .sidebar-dark .nav-item .nav-link i { color: rgba(255, 255, 255, 0.8) !important; }
        
        /* Hiệu ứng khi rê chuột vào menu (Sáng trắng lên) */
        .sidebar-dark .nav-item .nav-link:hover { color: #ffffff !important; }
        .sidebar-dark .nav-item .nav-link:hover i { color: #ffffff !important; }

        /* Logo Sidebar màu Trắng tinh */
        .sidebar-brand-text, .sidebar-brand-icon i { color: #ffffff !important; }
        /* Đường kẻ ngang chia menu mờ đi */
        .sidebar-dark hr.sidebar-divider { border-top: 1px solid rgba(255, 255, 255, 0.15); }

        /* Item ĐANG ĐƯỢC CHỌN trên Menu */
        .sidebar-dark .nav-item.active .nav-link {
            color: #ffffff !important;
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.15); /* Phủ một lớp nền trắng mờ */
            border-right: 4px solid #ffffff; /* Vạch trắng bên phải để đánh dấu */
        }
        .sidebar-dark .nav-item.active .nav-link i { color: #ffffff !important; }

        /* 3. Màu sắc cho vùng Nội dung (Content) */
        .text-primary { color: var(--admin-primary) !important; }
        .bg-primary { background-color: var(--admin-primary) !important; }

        /* Nút bấm toàn hệ thống */
        .btn-primary { 
            background-color: var(--admin-primary) !important; 
            border-color: var(--admin-primary) !important; 
            color: #fff !important;
            transition: all 0.3s;
        }
        .btn-primary:hover, .btn-primary:focus { 
            background-color: #1557B0 !important; /* Xanh đậm hơn khi hover */
            border-color: #1557B0 !important; 
            box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.25) !important; 
        }

        /* Phân trang (Pagination) và Nút cuộn lên */
        .page-item.active .page-link { 
            background-color: var(--admin-primary) !important; 
            border-color: var(--admin-primary) !important; 
        }
        .badge-primary { background-color: var(--admin-primary) !important; }
        .scroll-to-top { background-color: var(--admin-primary) !important; }

        /* Làm mềm các đường viền Bảng/Card */
        .card { border-radius: 10px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.08) !important; }
        .card-header { border-radius: 10px 10px 0 0 !important; background-color: #fff; border-bottom: 1px solid #f8f9fa; }
        /* ================= DARK MODE ================= */

/* ================= DARK MODE ================= */

html.dark-mode,
html.dark-mode #wrapper,
html.dark-mode #content-wrapper,
html.dark-mode #content {
    background: #121212 !important;
}

html.dark-mode .bg-light {
    background: #1a1a1a !important;
}

html.dark-mode .topbar {
    background: #1f1f1f !important;
}

html.dark-mode .card {
    background: #232323 !important;
    color: #fff !important;
}

html.dark-mode .card-header {
    background: #2c2c2c !important;
    color: #fff !important;
}

html.dark-mode .table {
    color: #fff !important;
}

html.dark-mode .table thead {
    background: #2d2d2d !important;
}

html.dark-mode .table td,
html.dark-mode .table th {
    border-color: #444 !important;
}

html.dark-mode .dropdown-menu {
    background: #2a2a2a !important;
}

html.dark-mode .dropdown-item {
    color: #fff !important;
}

html.dark-mode .dropdown-item:hover {
    background: #3a3a3a !important;
}

html.dark-mode .form-control,
html.dark-mode .form-select {
    background: #2b2b2b !important;
    color: #fff !important;
    border-color: #555 !important;
}

html.dark-mode .text-gray-800,
html.dark-mode .text-dark {
    color: #fff !important;
}

html.dark-mode #accordionSidebar {
    background: #1b1b1b !important;
    background-image: none !important;
}

html.dark-mode .sidebar-dark .nav-item .nav-link {
    color: #d1d5db !important;
}

html.dark-mode .sidebar-dark .nav-item .nav-link:hover {
    color: #fff !important;
    background: #2d2d2d !important;
}

html.dark-mode .sidebar-brand {
    background: #111 !important;
}

html.dark-mode .container-fluid {
    background: #181818 !important;
}
html.dark-mode footer,
html.dark-mode .sticky-footer {
    background-color: #1f1f1f !important;
    color: #fff !important;
}

html.dark-mode .sticky-footer span,
html.dark-mode .sticky-footer p,
html.dark-mode .sticky-footer small {
    color: #fff !important;
}
html.dark-mode .pagination .page-link {
    background-color: #2b2b2b !important;
    color: #fff !important;
    border-color: #444 !important;
}

html.dark-mode .pagination .page-item.active .page-link {
    background-color: #1A73E8 !important;
    border-color: #1A73E8 !important;
    color: #fff !important;
}

html.dark-mode .pagination .page-item.disabled .page-link {
    background-color: #222 !important;
    color: #777 !important;
    border-color: #444 !important;
}

html.dark-mode .pagination .page-link:hover {
    background-color: #3a3a3a !important;
    color: #fff !important;
}
html.dark-mode select,
html.dark-mode .form-select {
    background-color: #2b2b2b !important;
    color: #fff !important;
    border: 1px solid #555 !important;
}
/* Header của card */
.custom-card-header{
    background:#fff;
    padding:1rem 1.25rem;
    border-bottom:1px solid #e3e6f0;
    border-radius:10px 10px 0 0;
}

/* Dark Mode */
html.dark-mode .custom-card-header{
    background:#1f1f1f !important;
    border-bottom:1px solid #444 !important;
}

html.dark-mode .custom-card-header h6{
    color:#fff !important;
}

html.dark-mode .custom-card-header i{
    color:#1A73E8 !important;
}
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        @include('admin.sidebar')

        <div id="content-wrapper" class="d-flex flex-column bg-light">

            <div id="content">

                <!-- Header -->
                @include('admin.header')

                <!-- Main Content -->
                <div class="container-fluid pt-4">
                    @yield('admin_content')
                </div>
            </div>
            
            <!-- Footer -->
            @include('admin.footer')

        </div>
    </div>
    
    <!-- Nút cuộn lên đầu trang -->
    <a class="scroll-to-top rounded" href="#page-top" style="background: var(--admin-orange);">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal Đăng xuất -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title font-weight-bold text-dark">Bạn muốn đăng xuất?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-gray-700">Chọn "Đăng xuất" bên dưới nếu bạn đã sẵn sàng kết thúc phiên làm việc.</div>
                <div class="modal-footer border-0">
                    <button class="btn btn-secondary rounded-pill px-4" type="button" data-dismiss="modal">Hủy</button>
                    <a class="btn btn-primary rounded-pill px-4" href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form-modal').submit();">Đăng xuất</a>
                    <form id="admin-logout-form-modal" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DÙNG LINK CDN CHO JAVASCRIPT ĐỂ CHỐNG LỖI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

    @stack('scripts')
</body>
</html>