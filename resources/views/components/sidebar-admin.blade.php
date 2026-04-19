{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<style>
    /* SIDEBAR */
    .sidebar {
        min-height: 100vh;
        width: 260px;
        background: #eef2ff;
    }

    /* BRAND / LOGO (STYLE ADMINLTE) */
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid #c0c1c4;
    }

    .sidebar-brand img {
        height: 30px; /* ukuran khas AdminLTE */
        width: auto;
        object-fit: contain;
    }

    .sidebar-brand-text {
        font-weight: 600;
        font-size: 18px;
        color: #1f2937;
    }

    /* NAV STYLE */
    .sidebar .nav-link {
        padding: 10px 14px;
        border-radius: 8px;
        font-weight: 500;
        transition: 0.2s;
    }

    .sidebar .nav-link i {
        font-size: 17px;
    }

    .sidebar .nav-item {
        margin-bottom: 5px;
    }
    .sidebar .nav {
    margin-top: 25px;
}
</style>

<div class="sidebar p-4" id="sidebar">

    {{-- LOGO + TEXT (ADMINLTE STYLE) --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo2.png') }}" alt="Logo"
            onerror="this.style.display='none'">

        <span class="sidebar-brand-text">FindKos</span>
    </div>

    {{-- MENU --}}
    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : 'text-dark' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.pemilik.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.pemilik.*') ? 'active bg-primary text-white' : 'text-dark' }}">
                <i class="bi bi-people me-2"></i> Data Pemilik Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.kos.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.kos.*') ? 'active bg-primary text-white' : 'text-dark' }}">
                <i class="bi bi-house me-2"></i> Verifikasi Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.log.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.log.index') ? 'active bg-primary text-white' : 'text-dark' }}">
                <i class="bi bi-clipboard-data me-2"></i> Log Aktivitas
            </a>
        </li>

    </ul>
</div>
