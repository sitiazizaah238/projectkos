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
            <a href="{{ route('pemilik.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>

        {{-- DATA KOS --}}
        <li class="nav-item">
            <a href="{{ route('pemilik.kos.index') }}"
                class="nav-link d-flex align-items-center
        {{ request()->routeIs('pemilik.kos.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-building me-2"></i>
                Data Kos
            </a>
        </li>


        {{-- DATA KAMAR --}}
        <li class="nav-item">
            <a href="{{ route('pemilik.kamar.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.kamar.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-door-open me-2"></i>
                Data Kamar
            </a>
        </li>

        {{-- DATA PENYEWA --}}
        <li class="nav-item">
            <a href="#"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.penyewa.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-people me-2"></i>
                Data Penyewa
            </a>
        </li>

        {{-- PEMBAYARAN --}}
        <li class="nav-item">
            <a href="#"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('pemilik.pembayaran.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-wallet me-2"></i>
                Manajemen Pembayaran
            </a>
        </li>
        {{-- VERIFIKASI PEMBAYARAN --}}
        <li class="nav-item">
            <a href="#"
                class="nav-link d-flex align-items-center
        {{ request()->routeIs('pemilik.pembayaran.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-credit-card me-2"></i>
                Verifikasi Pembayaran
            </a>
        </li>

    </ul>

</div>
