<div class="sidebar p-4">

    <h4 class="fw-bold text-primary mb-4">
        <i class="bi bi-house-door-fill"></i> FINDKOS
    </h4>

    <div class="mb-4">
        <small>Welcome</small>
        <div class="fw-bold">{{ Auth::user()->email }}</div>
    </div>

    <ul class="nav flex-column">

        {{-- DASHBOARD --}}
        <li class="nav-item">
            <a href="{{ route('penyewa.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>

        {{-- CARI KOS --}}
        <li class="nav-item">
            <a href="{{ route('penyewa.cari.kos') }}"
                class="nav-link d-flex align-items-center
    {{ request()->routeIs('penyewa.cari.kos') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-search me-2"></i>
                Cari Kos
            </a>
        </li>

        {{-- REKOMENDASI --}}
        <li class="nav-item">
            <a href="{{ route('penyewa.rekomendasi') }}"
                class="nav-link d-flex align-items-center
             {{ request()->routeIs('penyewa.rekomendasi') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-star me-2"></i>
                Rekomendasi Kos
            </a>
        </li>

        {{-- PENGAJUAN SEWA --}}
        <li class="nav-item">
            <a href="#" class="nav-link d-flex align-items-center text-dark">

                <i class="bi bi-file-earmark-text me-2"></i>
                Pengajuan Sewa
            </a>
        </li>

        {{-- PEMBAYARAN --}}
        <li class="nav-item">
            <a href="#" class="nav-link d-flex align-items-center text-dark">

                <i class="bi bi-wallet me-2"></i>
                Pembayaran
            </a>
        </li>

    </ul>

</div>
