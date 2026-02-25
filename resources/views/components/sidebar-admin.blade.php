{{-- Overlay --}} <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()">
    </div>

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
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.pemilik.index') }}"
                    class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.pemilik.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                    <i class="bi bi-people me-2"></i> Data Pemilik Kos
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.kos.index') }}"
                    class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.kos.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                    <i class="bi bi-house me-2"></i> Data Kos
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.log.index') }}"
                    class="nav-link d-flex align-items-center
                {{ request()->routeIs('admin.log.index') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                    <i class="bi bi-clipboard-data me-2"></i> Log Aktivitas
                </a>
            </li>

        </ul>
    </div>
