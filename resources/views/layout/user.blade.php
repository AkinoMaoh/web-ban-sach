<!DOCTYPE html>
<html lang="vi">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SachHay - Tiệm sách trực tuyến</title>

    <!-- Font chữ sang trọng -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Dùng trực tiếp Bootstrap và FontAwesome trên mạng để chống lỗi mất CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/bookstore.css') }}">

    <!-- CSS TRỰC TIẾP (Không sợ lỗi đường dẫn file) -->
    <style>
        :root {
            --primary-color: #D35400; /* Cam cháy */
            --text-main: #2C3E50;
        }
        body { font-family: 'Nunito Sans', sans-serif; background-color: #FAFAFA; color: var(--text-main); }
        .serif-font { font-family: 'Lora', serif; font-weight: 700; }
        
        /* Header & Footer */
        .modern-header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 0; position: sticky; top: 0; z-index: 1000; }
        .modern-footer { background: #fff; border-top: 1px solid #eee; padding-top: 40px; margin-top: 40px; }
        
        /* Nav Links */
        .nav-link { color: var(--text-main); font-weight: 600; text-transform: uppercase; font-size: 14px; }
        .nav-link:hover { color: var(--primary-color); text-decoration: none; }
        
        /* Khung chứa sách */
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .book-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 15px; transition: 0.3s; text-align: center; display: block; text-decoration: none !important; color: var(--text-main); height: 100%;}
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); color: var(--text-main); }
        .book-cover { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 4px; margin-bottom: 15px; box-shadow: 2px 2px 8px rgba(0,0,0,0.1); }
        .book-title { font-size: 15px; font-weight: 700; height: 42px; overflow: hidden; margin-bottom: 8px; }
        .book-price { color: var(--primary-color); font-weight: 700; font-size: 16px; margin: 0; }
        
        /* Buttons */
        .btn-orange { background-color: var(--primary-color); color: #fff; border-radius: 20px; padding: 8px 20px; font-weight: bold; border: none; transition: 0.3s; }
        .btn-orange:hover { background-color: #a64200; color: #fff; }
    </style>
</head>
<script>
(function () {
    const theme = localStorage.getItem("theme");

    if (theme === "dark") {
        document.documentElement.classList.add("dark-mode");
    }
})();
</script>

<style>
html {
    background: #FAFAFA;
}

html.dark-mode {
    background: #121212;
    color: #fff;
}

body {
    background: inherit;
}
</style>
<body>

    <!-- Gọi file Header -->
    @include('User.header')

    <!-- Phần lõi thay đổi theo từng trang -->
    <main style="min-height: 70vh;">
        @yield('content')
    </main>

    <!-- Gọi file Footer -->
    @include('User.footer')

    <!-- Load jQuery trước -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Biến route -->
    <script>
    const searchUrl = "{{ route('search.product') }}";
    </script>

    <!-- Cuối cùng mới load search.js -->
    <script src="{{ asset('js/search.js') }}"></script>

    @stack('scripts')
</body>
</html>









<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("theme-toggle");
    const root = document.documentElement;

    if (!btn) return;

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

        btn.innerHTML = isDark
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-fill"></i>';
    });

});
</script>