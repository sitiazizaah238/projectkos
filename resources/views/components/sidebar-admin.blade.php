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
                class="nav-link d-flex align-items-center
   {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pemilik.index') }}"
                class="nav-link d-flex align-items-center
   {{ request()->routeIs('admin.pemilik.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">

                <i class="bi bi-people me-2"></i>
                Data Pemilik Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.kos.index') }}"
                class="nav-link d-flex align-items-center
{{ request()->routeIs('admin.kos.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-house me-2"></i>
                Data Kos
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.log.index') }}"
                class="nav-link {{ request()->routeIs('admin.log.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                Log Aktivitas
            </a>
        </li>

    </ul>
</div>
