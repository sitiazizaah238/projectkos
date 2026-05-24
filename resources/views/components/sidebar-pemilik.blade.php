{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<style>
    /* SIDEBAR */
    .sidebar {
        min-height: 100vh;
        width: 260px;
        background: #eceff3;
        height: 100vh;
        overflow: hidden;
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
        height: 30px;
        /* ukuran khas AdminLTE */
        width: auto;
        object-fit: contain;
    }

  .sidebar-brand-text {
    font-weight: 600;
    font-size: 18px;
    letter-spacing: 0.3px;
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

    width: 34px;
    height: 34px;
    border-radius: 10px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #dbeafe;
    color: #2563eb;

    transition: 0.2s;
}
.sidebar .nav-link.active i {
    background: rgba(255,255,255,0.2);
    color: white;
}
.sidebar .nav-link:hover i {
    background: #bfdbfe;
    color: #1d4ed8;
}

    .sidebar .nav-item {
        margin-bottom: 5px;
    }

    .sidebar .nav {
        margin-top: 25px;
    }

    /* SCROLL HILANG TAPI MASIH BISA DIGESER */
    #menuKeuangan {
        max-height: 200px;
        overflow-y: auto;

        scrollbar-width: none;
        /* Firefox */
    }

    #menuKeuangan::-webkit-scrollbar {
        display: none;
        /* Chrome, Edge, Safari */
    }
</style>

<div class="sidebar p-4" id="sidebar">

    {{-- LOGO + TEXT (ADMINLTE STYLE) --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo2.png') }}" alt="Logo" onerror="this.style.display='none'">
        <span class="sidebar-brand-text">FindKos</span>
    </div>
    </h4>



    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ route('pemilik.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pemilik.kos.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.kos.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-building me-2"></i> Data Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pemilik.kamar.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.kamar.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-door-open me-2"></i> Data Kamar
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pemilik.pengajuan.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.pengajuan.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-people me-2"></i> Data Pengajuan
            </a>
        </li>

        {{-- Dropdown Manajemen Keuangan --}}
        <li class="nav-item">
            <a href="#menuKeuangan" data-bs-toggle="collapse" data-no-loader="true"
                class="nav-link d-flex align-items-center {{ request()->routeIs('pemilik.pembayaran.*', 'pemilik.verifikasi.*', 'pemilik.laporan.*') ? 'text-primary fw-bold' : 'text-dark' }}"
                aria-expanded="{{ request()->routeIs('pemilik.pembayaran.*', 'pemilik.verifikasi.*', 'pemilik.laporan.*') ? 'true' : 'false' }}">
                <i class="bi bi-wallet2 me-2"></i> Manajemen Keuangan
                <i class="bi bi-chevron-down ms-auto" style="font-size: 12px;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('pemilik.pembayaran.*', 'pemilik.verifikasi.*', 'pemilik.laporan.*') ? 'show' : '' }}"
                id="menuKeuangan">
                <ul class="nav flex-column ms-3 mt-1" style="border-left: 2px solid #e9ecef; padding-left: 10px;">
                    <li class="nav-item">
                        <a href="{{ route('pemilik.pembayaran.index') }}"
                            class="nav-link mb-1 d-flex align-items-center {{ request()->routeIs('pemilik.pembayaran.*') && !request()->routeIs('pemilik.verifikasi.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                            <i class="bi bi-receipt me-2"></i> Manajemen Pembayaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemilik.verifikasi.index') }}"
                            class="nav-link mb-1 d-flex align-items-center {{ request()->routeIs('pemilik.verifikasi.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                            <i class="bi bi-credit-card me-2"></i> Pembayaran Penyewa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemilik.laporan.index') }}"
                            class="nav-link mb-1 d-flex align-items-center {{ request()->routeIs('pemilik.laporan.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                            <i class="bi bi-cash-stack me-2"></i> Laporan Keuangan
                        </a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
