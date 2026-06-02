{{-- Overlay --}}
<div class="sidebar-overlay d-none" id="sidebarOverlay" onclick="toggleSidebar()" style="pointer-events:none;"></div>

<style>
    /* SIDEBAR */
    .sidebar {
        min-height: 100vh;
        width: 260px;
        background: #eceff3;
        height: 100vh;
        overflow: hidden;
    }

    /* BRAND (ADMINLTE STYLE) */
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
    .sidebar .nav {
        margin-top: 25px;
    }

    .sidebar .nav-link {
        padding: 10px 14px;
      border-radius: 10px;
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



    .sidebar .nav-item {
        margin-bottom: 5px;
    }
</style>

<div class="sidebar p-4" id="sidebar">

    {{-- ✅ GANTI HEADER LAMA JADI INI (LEBIH RAPI) --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo2.png') }}" alt="Logo"
            onerror="this.style.display='none'">

        <span class="sidebar-brand-text">FindKos</span>
    </div>

    {{-- MENU --}}
    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ route('penyewa.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-grid me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.cari.kos') }}"
                class="nav-link d-flex align-items-center
               {{ request()->routeIs('penyewa.cari.kos') || request('from') == 'cari'
    ? 'active bg-primary text-white rounded'
    : 'text-dark' }}">
                <i class="bi bi-compass me-2"></i> Cari Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.rekomendasi') }}"
                class="nav-link d-flex align-items-center
               {{ request()->routeIs('penyewa.rekomendasi') || request('from') == 'rekomendasi'
    ? 'active bg-primary text-white rounded'
    : 'text-dark' }}">
                <i class="bi bi-stars me-2"></i> Rekomendasi Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.pengajuan.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.pengajuan.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-clipboard-check me-2"></i> Pengajuan Sewa
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.pembayaran.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.pembayaran.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-credit-card me-2"></i> Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.riwayat.pembayaran') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.riwayat.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-clock-history me-2"></i> Riwayat Pembayaran
            </a>
        </li>

    </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const overlay = document.getElementById("sidebarOverlay");

        function updateOverlay() {
            if (window.innerWidth <= 768) {
                overlay.style.pointerEvents = "auto";
            } else {
                overlay.classList.add("d-none");
                overlay.style.pointerEvents = "none";
            }
        }

        updateOverlay();
        window.addEventListener("resize", updateOverlay);
    });
</script>
