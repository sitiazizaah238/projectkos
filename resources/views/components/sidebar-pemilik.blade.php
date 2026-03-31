{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="sidebar p-4" id="sidebar">
    <h4 class="fw-bold text-primary mb-3 d-flex align-items-center"
        style="
        gap:10px;
        font-size:clamp(18px,1.6vw,22px);
        letter-spacing:0.3px;
        font-weight:600;
        margin-top:-6px;
    ">

        {{-- Logo jika ada --}}
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
            style="
            height:clamp(36px,4vw,48px);
            width:auto;
            object-fit:contain;
         "
            onerror="this.style.display='none'">


        <span
            style="
        font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
        color:#1f2937;
    ">
            FindKos
            <HR>
            </HR>
        </span>

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
                            <i class="bi bi-credit-card me-2"></i> Verifikasi Pembayaran
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
