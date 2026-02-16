<div class="sidebar p-4">
    <h4 class="fw-bold text-primary mb-4">
        <i class="bi bi-house-door-fill"></i> FINDKOS
    </h4>

    <div class="mb-4">
        <small>Welcome</small>
        <div class="fw-bold">{{ Auth::user()->email }}</div>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>

        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-person"></i> Data Pemilik Kos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-house"></i> Data Kos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-clock-history"></i> Log Aktivitas
            </a>
        </li>
    </ul>
</div>
