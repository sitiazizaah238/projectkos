{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<style>
    /* SIDEBAR */
   .sidebar {
    min-height: 100vh;
    width: 260px;
    background: #eceff3;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

    /* BRAND / LOGO */
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid #d6d9df;
    }

    .sidebar-brand img {
        height: 30px;
        width: auto;
        object-fit: contain;
    }

    .sidebar-brand-text {
        font-weight: 700;
        font-size: 20px;
        color: #1e3a8a;
    }

    /* NAV STYLE */
    .sidebar .nav {
        margin-top: 25px;
    }

    .sidebar .nav-item {
        margin-bottom: 8px;
    }

    .sidebar .nav-link {
        padding: 11px 14px;
        border-radius: 12px;
        font-weight: 500;
        transition: 0.25s;
        display: flex;
        align-items: center;
    }

    /* ICON BOX */
    .sidebar .nav-link i {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 12px;
    }

    /* ACTIVE MENU */
    .sidebar .nav-link.active {
        background: #2563eb;
        color: white !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .sidebar .nav-link.active i {
        background: rgba(255,255,255,0.2);
        color: white;
    }


    .sidebar .nav-link.text-dark i {
        background: #dbeafe;
        color: #2563eb;
    }

    /* PROFILE */
    .sidebar-profile {
        border-top: 1px solid #d6d9df;
        padding-top: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-profile .profile-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #dbeafe;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 18px;
    }

    .sidebar-profile .profile-name {
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .sidebar-profile .profile-role {
        font-size: 12px;
        color: #6b7280;
    }
</style>

<div class="sidebar p-4" id="sidebar">

    <div>
        {{-- LOGO --}}
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo2.png') }}" alt="Logo"
                onerror="this.style.display='none'">

            <span class="sidebar-brand-text">FindKos</span>
        </div>

        {{-- MENU --}}
        <ul class="nav flex-column">

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.dashboard') ? 'active text-white' : 'text-dark' }}">

                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.pemilik.index') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.pemilik.*') ? 'active text-white' : 'text-dark' }}">

                    <i class="bi bi-people"></i>
                    Data Pemilik Kos
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.kos.index') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.kos.*') ? 'active text-white' : 'text-dark' }}">

                    <i class="bi bi-house"></i>
                    Verifikasi Kos
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.log.index') }}"
                    class="nav-link
                    {{ request()->routeIs('admin.log.index') ? 'active text-white' : 'text-dark' }}">

                    <i class="bi bi-clipboard-data"></i>
                    Log Aktivitas
                </a>
            </li>

        </ul>
    </div>

    {{-- PROFILE --}}
    <div class="sidebar-profile">

        <div class="profile-icon">
            <i class="bi bi-person"></i>
        </div>

        <div>
            <div class="profile-name">
                {{ Auth::user()->name ?? 'Admin' }}
            </div>

            <div class="profile-role">
                Super Admin
            </div>
        </div>

    </div>

</div>
