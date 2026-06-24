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

    @stack('styles')

    <!-- GLOBAL CSS DASHBOARD -->
    <style>
        /* Fix for Bootstrap collapse conflict with Tailwind */
        .collapse:not(.show) {
            display: none !important;
        }

        .collapse.show {
            display: block !important;
            visibility: visible !important;
        }

        .collapsing {
            height: 0;
            overflow: hidden;
            transition: height .35s ease;
        }

        body {
            background: #f5f7fb;
            font-family: 'Figtree', sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar .nav-link {
            color: #333;
            margin-bottom: 8px;
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }

        .sidebar .nav-link:hover {
            background: #0d6efd !important;
            color: #fff !important;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link:hover * {
            color: #fff !important;
        }

        .sidebar .nav-link:hover i {
            color: #fff !important;
            background: rgba(255,255,255,0.2) !important;
        }

        .topbar {
            height: 64px;
            background: #0d6efd;
            color: white;
            position: sticky;
            top: 0;
            z-index: 900;
            overflow: visible;
        }

        .topbar .dropdown-menu {
            z-index: 1200;
            max-height: calc(100vh - 88px) !important;
            overflow-y: auto !important;
            overflow-x: hidden;
        }

        .topbar .dropdown-menu.notif-dropdown-menu,
        .topbar .dropdown-menu[style*='width:300px'],
        .topbar .dropdown-menu[style*='width:320px'] {
            width: min(92vw, 340px) !important;
        }

        .topbar .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        .topbar .dropdown-menu .notif-item {
            border-bottom: 1px solid #eef0f3;
        }

        .topbar .dropdown-menu .notif-item:last-child {
            border-bottom: none;
        }

        .topbar .dropdown-menu .notif-title {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
            max-width: 220px;
        }

        .topbar .dropdown-menu .notif-message {
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }

        .topbar .dropdown-menu .notif-read-badge {
            font-size: 10px;
            white-space: nowrap;
            flex-shrink: 0;
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
            100% {
                transform: rotate(360deg);
            }
        }

        /* Desktop: collapsed = width 0 */
        .sidebar.collapsed {
            width: 0;
            padding: 0 !important;
            overflow: hidden;
        }

        .sidebar-toggle {
            display: none;
            /* default disembunyikan */
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1000;
            border: none;
            background: transparent;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }

        /* Hanya muncul di mobile */
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 998;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 999;
                width: 260px;
                transform: translateX(-100%);
                /* Default TERSEMBUNYI di mobile */
            }

            .sidebar.open {
                transform: translateX(0);
                /* Terbuka saat klik toggle */
            }

            /* Di mobile tidak perlu collapsed class */
            .sidebar.collapsed {
                width: 260px;
                padding: 1.5rem !important;
                overflow: visible;
            }
        }
    </style>
</head>

<body>

    {{-- LOADER --}}
    <div id="pageLoader"
        style="position: fixed; inset: 0; background: white; display: none;
    justify-content: center; align-items: center; z-index: 9999;">
        <div
            style="
        width: 60px;
        height: 60px;
        border: 6px solid #e5e7eb;
        border-top: 6px solid #0d8bff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    ">
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById("pageLoader");
            const hasSuccess = {{ session()->has('success') ? 'true' : 'false' }};

            if (hasSuccess) return;

            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", () => {
                    if (!form.hasAttribute("data-no-loader")) {
                        loader.style.display = "flex";
                    }
                });
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- SUCCESS MODAL --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    {{-- Tombol toggle, taruh di dalam topbar masing-masing halaman --}}
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const icon = document.getElementById('toggleIcon');

            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                // Mobile: toggle class 'open'
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
                icon.className = sidebar.classList.contains('open') ?
                    'bi bi-x-lg' :
                    'bi bi-list';
            } else {
                // Desktop: toggle class 'collapsed'
                sidebar.classList.toggle('collapsed');
                icon.className = sidebar.classList.contains('collapsed') ?
                    'bi bi-list' :
                    'bi bi-x-lg';
            }
        }

        // Tutup sidebar kalau klik overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                    document.getElementById('toggleIcon').className = 'bi bi-list';
                });
            }

            // Reset saat resize ke desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    if (sidebar) sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('show');
                    document.getElementById('toggleIcon').className = 'bi bi-list';
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
