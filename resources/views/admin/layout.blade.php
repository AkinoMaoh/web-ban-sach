<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Trang Quản Trị - SachHay</title>

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