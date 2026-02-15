<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>{{ config('app.name', 'FINDKOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="font-sans antialiased">

{{-- LOADER --}}
<div id="pageLoader"
    style="
    position: fixed;
    inset: 0;
    background: white;
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
">
    <div style="
        width: 60px;
        height: 60px;
        border: 6px solid #e5e7eb;
        border-top: 6px solid #0d8bff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    "></div>
</div>

{{-- ISI HALAMAN FULL DARI DASHBOARD --}}
<main>
    @yield('content')
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const loader = document.getElementById("pageLoader");

    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function () {
            loader.style.display = "flex";
        });
    });

    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function () {
            if (!link.hasAttribute("data-no-loader")) {
                loader.style.display = "flex";
            }
        });
    });

});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
