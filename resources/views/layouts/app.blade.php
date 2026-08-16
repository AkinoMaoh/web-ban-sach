<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

   

    <style>
        html {
            visibility: hidden;
            background: #fff;
        }

        html.dark-mode {
            background: #121212;
            color: #fff;
        }

        body {
            background: inherit;
        }

        html.loaded {
            visibility: visible;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<script>
document.addEventListener('DOMContentLoaded', function () {

    const isLoggedIn = @json(auth()->check());

    if (!isLoggedIn) {
        // CHƯA ĐĂNG NHẬP → LUÔN LIGHT MODE
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.remove('dark-mode');

        document.body.classList.remove('dark');
        document.body.classList.remove('dark-mode');

        localStorage.setItem('theme', 'light');

        return;
    }

    // ĐÃ ĐĂNG NHẬP → dùng theme đã lưu
    const theme = localStorage.getItem('theme');

    if (theme === 'dark') {
        document.documentElement.classList.add('dark-mode');
    } else {
        document.documentElement.classList.remove('dark-mode');
    }

});
</script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function () {
            document.documentElement.classList.add('loaded');
        });
    </script>
</body>
</html>