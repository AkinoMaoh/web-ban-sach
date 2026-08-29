<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BookZone - Tiệm sách trực tuyến</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Nunito+Sans:wght@300;400;600;700&display=swap"
          rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CSS giao diện sáng -->
    <link rel="stylesheet"
          href="{{ asset('css/bookstore.css') }}">

    <!-- CSS Dark Mode -->
    <link rel="stylesheet"
          href="{{ asset('css/darkMode.css') }}">

    <!-- CSS riêng của layout -->
    <style>
        :root {
            --primary-color: #D35400;
            --text-main: #2C3E50;
        }

        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #FAFAFA;
            color: var(--text-main);
        }

        .serif-font {
            font-family: 'Lora', serif;
            font-weight: 700;
        }

        /* Header */
        .modern-header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Footer */
        .modern-footer {
            background: #fff;
            border-top: 1px solid #eee;
            padding-top: 40px;
            margin-top: 40px;
        }

        /* Navigation */
        .nav-link {
            color: var(--text-main);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .nav-link:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        /* Book grid */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            transition: 0.3s;
            text-align: center;
            display: block;
            text-decoration: none !important;
            color: var(--text-main);
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            color: var(--text-main);
        }

        .book-cover {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 15px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }

        .book-title {
            font-size: 15px;
            font-weight: 700;
            height: 42px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .book-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 16px;
            margin: 0;
        }

        /* Button */
        .btn-orange {
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }

        .btn-orange:hover {
            background-color: #a64200;
            color: #fff;
        }

        /* Wishlist */
        .btn-wishlist-v2.position-absolute {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: none !important;
            padding: 0 !important;
            background-color: rgba(255,255,255,0.95) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important;
            transition: all 0.2s ease-in-out !important;
            z-index: 15 !important;
        }

        .btn-wishlist-v2.position-absolute:hover {
            background-color: #fff !important;
            transform: scale(1.15) !important;
            box-shadow: 0 4px 12px rgba(211,84,0,0.25) !important;
        }

        .btn-wishlist-v2.position-absolute i {
            font-size: 16px !important;
            transform: translateY(1px);
        }
        /* Nút Back to Top */
#backToTopBtn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999;
    width: 45px;
    height: 45px;
    border: none;
    outline: none;
    background-color: var(--primary-color, #D35400); /* Dùng màu cam mặc định hoặc variable dự án */
    color: #ffffff;
    cursor: pointer;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    
    /* Hiệu ứng ẩn/hiện mượt mà */
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease-in-out;
    
    /* Căn giữa icon */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Hiệu ứng hover */
#backToTopBtn:hover {
    background-color: #e67e22;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
}

/* Class hiển thị nút */
#backToTopBtn.show {
    opacity: 1;
    visibility: visible;
}
    </style>

    <!-- Chống flash Dark/Light -->
    <script>
        (function () {

            const isLoggedIn = @json(auth()->check());

            if (!isLoggedIn) {

                // Chưa đăng nhập → luôn Light
                document.documentElement.classList.remove('dark-mode');

                localStorage.setItem('theme', 'light');

                return;
            }

            // Đã đăng nhập
            const theme = localStorage.getItem('theme');

            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }

        })();
    </script>

</head>


<body>

    <!-- Header -->
    @include('User.header')


    <!-- Nội dung -->
    <main style="min-height: 70vh;">
        @yield('content')
    </main>


    <!-- Footer -->
    @include('User.footer')



    <!-- jQuery - CHỈ LOAD 1 LẦN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/search.js') }}"></script>


    <!-- Wishlist -->
    <script>
        $(document).ready(function () {

            $('.btn-wishlist').click(function (e) {

                e.preventDefault();

                let btn = $(this);
                let productId = btn.data('id');
                let icon = btn.find('i');

                axios.post('{{ route('user.wishlist.toggle') }}', {

                    product_id: productId,
                    _token: '{{ csrf_token() }}'

                })
                .then(function (response) {

                    if (response.data.status === 'added') {

                        icon.removeClass('far').addClass('fas');

                        alert(response.data.message);

                    } else {

                        icon.removeClass('fas').addClass('far');

                        alert(response.data.message);

                    }

                })
                .catch(function (error) {

                    if (error.response && error.response.status === 401) {

                        alert(
                            "Vui lòng đăng nhập để thêm vào danh sách yêu thích!"
                        );

                        window.location.href = '/login';
                    }

                });

            });

        });
    </script>


    <!-- Dark Mode -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const btn = document.getElementById("theme-toggle");
            const root = document.documentElement;

            const isLoggedIn = @json(auth()->check());


            /* =========================================
               CHƯA ĐĂNG NHẬP
            ========================================= */

            if (!isLoggedIn) {

                root.classList.remove("dark-mode");

                localStorage.setItem("theme", "light");

                if (btn) {
                    btn.innerHTML =
                        '<i class="bi bi-moon-fill"></i>';
                }

                return;
            }


            /* =========================================
               ĐÃ ĐĂNG NHẬP
            ========================================= */

            if (!btn) {
                return;
            }

            const saved =
                localStorage.getItem("theme");


            if (saved === "dark") {

                root.classList.add("dark-mode");

                btn.innerHTML =
                    '<i class="bi bi-sun-fill"></i>';

            } else {

                root.classList.remove("dark-mode");

                btn.innerHTML =
                    '<i class="bi bi-moon-fill"></i>';
            }


            /* =========================================
               NÚT CHUYỂN DARK / LIGHT
            ========================================= */

            btn.addEventListener("click", function () {

                const isDark =
                    root.classList.toggle("dark-mode");


                localStorage.setItem(
                    "theme",
                    isDark ? "dark" : "light"
                );


                btn.innerHTML = isDark

                    ? '<i class="bi bi-sun-fill"></i>'

                    : '<i class="bi bi-moon-fill"></i>';

            });

        });
    </script>


    @stack('scripts')
<!-- Nút Back to Top -->
<button id="backToTopBtn" title="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>
<!-- Cấp quyền cho Alpine.js hoạt động qua CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const backToTopBtn = document.getElementById("backToTopBtn");

    if (backToTopBtn) {
        // Lắng nghe sự kiện cuộn trang
        window.addEventListener("scroll", function () {
            // Khi cuộn xuống quá 300px thì hiện nút
            if (window.scrollY > 300) {
                backToTopBtn.classList.add("show");
            } else {
                backToTopBtn.classList.remove("show");
            }
        });

        // Xử lý khi click vào nút
        backToTopBtn.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth" // Cuộn mượt mà lên đầu
            });
        });
    }
});
</script>
</body>

</html>
