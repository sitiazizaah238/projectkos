<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>FindKos</title>
<link rel="icon" href="{{ asset('images/logo2.png') }}" type="image/png">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GLOBAL CSS DASHBOARD -->
    <style>
        body {
            background: #f5f7fb;
            font-family: 'Figtree', sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .sidebar .nav-link {
            color: #333;
            margin-bottom: 8px;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .sidebar .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }

        .topbar {
            height: 64px;
            background: #0d6efd;
            color: white;
        }

        .card-stat {
            border-radius: 14px;
            border: none;
        }

        .card-stat .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
        /* Sidebar hover biru solid */
.sidebar .nav-link {
    transition: all 0.2s ease;
}

.sidebar .nav-link:hover {
    background: #0d6efd;   /* biru solid */
    color: #fff;
}

.sidebar .nav-link:hover i {
    color: #fff;
}

/* Active tetap */
.sidebar .nav-link.active {
    background: #0d6efd;
    color: #fff;
}
    </style>
</head>

<body>

{{-- LOADER --}}
<div id="pageLoader"
    style="position: fixed; inset: 0; background: white; display: none;
    justify-content: center; align-items: center; z-index: 9999;">
    <div style="
        width: 60px;
        height: 60px;
        border: 6px solid #e5e7eb;
        border-top: 6px solid #0d8bff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    "></div>
</div>

<main>
    @yield('content')
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("pageLoader");

    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", () => loader.style.display = "flex");
    });

    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
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
