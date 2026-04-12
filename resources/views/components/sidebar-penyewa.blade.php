<div class="sidebar-overlay d-none" id="sidebarOverlay" onclick="toggleSidebar()" style="pointer-events:none;"></div>
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
            <a href="{{ route('penyewa.dashboard') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.dashboard') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-grid me-2" style="font-size:18px;"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.cari.kos') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.cari.kos') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-compass me-2" style="font-size:18px;"></i> Cari Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.rekomendasi') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.rekomendasi') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-stars me-3" style="font-size:18px;"></i>Rekomendasi Kos
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.pengajuan.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.pengajuan.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-clipboard-check me-2" style="font-size:18px;"></i> Pengajuan Sewa
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('penyewa.pembayaran.index') }}"
                class="nav-link d-flex align-items-center
                {{ request()->routeIs('penyewa.pembayaran.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-credit-card me-2" style="font-size:18px;"></i>Pembayaran
            </a>
        </li>
        {{-- 🔥 RIWAYAT PEMBAYARAN --}}
        <li class="nav-item">
            <a href="{{ route('penyewa.riwayat.pembayaran') }}"
                class="nav-link d-flex align-items-center
            {{ request()->routeIs('penyewa.riwayat.*') ? 'active bg-primary text-white rounded' : 'text-dark' }}">
                <i class="bi bi-clock-history me-2" style="font-size:18px;"></i> Riwayat Pembayaran
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
