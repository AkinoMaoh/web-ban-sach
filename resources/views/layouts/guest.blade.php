<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Thư Viện Sách - Hệ thống</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- BOOTSTRAP 5 CSS (Thay thế cho Vite & Tailwind) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <style>
            body {
                font-family: 'figtree', sans-serif;
                /* Background ảnh lấy từ code cũ của ông */
                background-image: url("{{ asset('img/bgWeb.jpg') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
                position: relative;
                margin: 0;
            }

            /* Lớp phủ màu đen làm mờ nền (Blur overlay) */
            body::before {
                content: "";
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: -1;
            }

            /* Khung chứa nội dung căn giữa toàn màn hình */
            .guest-wrapper {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }

            /* CSS cho Logo bo tròn y hệt Tailwind cũ */
            .guest-logo {
                width: 96px;
                height: 96px;
                object-fit: cover;
                border-radius: 50%;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
                border: 2px solid rgba(255, 255, 255, 0.5);
                margin-bottom: 0.5rem;
                transition: transform 0.3s ease;
            }

            .guest-logo:hover {
                transform: scale(1.05);
            }
            
            .slot-container {
                width: 100%;
            }
        </style>
    </head>
    <body class="antialiased">
        
        <div class="guest-wrapper">
            
            <!-- Logo Thư Viện -->
            <div class="text-center">
                <a href="/">
                    <img src="{{ asset('img/logoWebMoi.png') }}" alt="Logo Thư Viện Sách" class="guest-logo">
                </a>
            </div>

            <!-- Nội dung các trang Login/Register (Biến $slot) -->
            <div class="slot-container">
                {{ $slot }}
            </div>
            
        </div>

        <!-- BOOTSTRAP 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>