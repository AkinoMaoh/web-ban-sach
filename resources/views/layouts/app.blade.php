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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
html {
    visibility: hidden;
}

html.loaded {
    visibility: visible;
}
</style>
<style>
html {
    visibility: hidden;
}

html.loaded {
    visibility: visible;
}
</style>
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
    </body>
</html>
<style>
html {
    visibility: hidden; /* chặn flash */
}
html.ready {
    visibility: visible;
}
</style>

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
    background: #fff;
}

html.dark-mode {
    background: #121212;
    color: #fff;
}

body {
    background: inherit;
}
</style>
<script>
window.addEventListener("DOMContentLoaded", function () {
    document.documentElement.classList.add("loaded");
});
</script>
