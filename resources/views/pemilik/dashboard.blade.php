@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
    use App\Models\Pembayaran;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $userId = Auth::id();

    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $totalKos = Kos::where('user_id', $userId)->count();
    $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
    $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
    $totalPenyewa = PengajuanSewa::whereIn('kos_id', $kosIds)->where('status', 'aktif')->count();

    // =====================
    // NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', 0)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', 0)->latest()->get();
    $notifPembayaran = Pembayaran::whereHas('pengajuan.kos', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->where('status', 'menunggu')
        ->latest()
        ->get();
    $jumlahNotif = $notifKos->count() + $notifPengajuan->count() + $notifPembayaran->count();
@endphp
@section('content')
  <div class="d-flex flex-column flex-md-row">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">


            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-3 px-md-4 gap-2 flex-wrap">
                @include('components.notif-pemilik')
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold text-white small">
                        {{ Auth::user()->name }}
                    </span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="
                    width:35px;
                    height:35px;
                    min-width:35px;
                    min-height:35px;
                    border-radius:50%;
                    object-fit:cover;
                ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>
            </div>
            {{-- CONTENT --}}
            <div class="p-4">

                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Selamat Datang {{ Auth::user()->name }}!
                </h3>
                <small class="text-muted">Dashboard / Ringkasan</small>

                {{-- ====== CARD STATISTIK ====== --}}
                {{-- ====== STYLE HOVER CARD ====== --}}
                <style>
                    .dashboard-card-link {
                        text-decoration: none;
                        color: inherit;
                        display: block;
                    }

                    .dashboard-card {
                        transition: all 0.3s ease;
                        cursor: pointer;
                        border-radius: 14px;
                    }

                    .dashboard-card:hover {
                        transform: translateY(-6px);
                        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12) !important;
                    }

                    .dashboard-card:hover .card-icon {
                        transform: scale(1.08);
                    }

                    .card-icon {
                        transition: 0.3s ease;
                    }
                </style>


                {{-- ====== CARD STATISTIK ====== --}}
                <div class="row mt-4 g-3">

                    {{-- TOTAL KOS --}}
                  <div class="col-12 col-md-3">
                        <a href="{{ route('pemilik.kos.index') }}" class="dashboard-card-link">
                            <div class="card dashboard-card p-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Kos</small>
                                        <h3 class="fw-bold">{{ $totalKos }}</h3>
                                        <span class="small text-primary fw-semibold">
                                            Lihat Semua Kos
                                        </span>
                                    </div>
                                    <div class="bg-primary text-white p-3 rounded card-icon">
                                        <i class="bi bi-house-door-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- TOTAL KAMAR --}}
                    <div class="col-md-3">
                        <a href="{{ route('pemilik.kamar.index') }}" class="dashboard-card-link">
                            <div class="card dashboard-card p-3 shadow-sm border-start border-4 border-danger">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Kamar</small>
                                        <h3 class="fw-bold">{{ $totalKamar }}</h3>
                                        <span class="small text-danger fw-semibold">
                                            Lihat Semua Kamar
                                        </span>
                                    </div>
                                    <div class="bg-danger text-white p-3 rounded card-icon">
                                        <i class="bi bi-door-open-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- KAMAR TERSEDIA --}}
                    <div class="col-md-3">
                        <a href="{{ route('pemilik.kamar.index') }}" class="dashboard-card-link">
                            <div class="card dashboard-card p-3 shadow-sm border-start border-4 border-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Kamar Tersedia</small>
                                        <h3 class="fw-bold">{{ $kamarTersedia }}</h3>
                                        <span class="small text-success fw-semibold">
                                            Lihat Semua Data
                                        </span>
                                    </div>
                                    <div class="bg-success text-white p-3 rounded card-icon">
                                        <i class="bi bi-door-closed-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- TOTAL PENYEWA --}}
                    <div class="col-md-3">
                        <a href="{{ route('pemilik.pengajuan.index') }}" class="dashboard-card-link">
                            <div class="card dashboard-card p-3 shadow-sm border-start border-4 border-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Penyewa</small>
                                        <h3 class="fw-bold">{{ $totalPenyewa }}</h3>
                                        <span class="small text-info fw-semibold">
                                            Lihat Semua Data
                                        </span>
                                    </div>
                                    <div class="bg-info text-white p-3 rounded card-icon">
                                        <i class="bi bi-person-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                {{-- ====== GRAFIK ====== --}}
                <div class="row mt-4">

                    {{-- BAR CHART --}}
                 <div class="col-12 col-md-8 mb-3 mb-md-0">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold">Grafik Penyewaan Bulanan</h6>
                            <div style="height:260px;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- DONUT CHART --}}
                   <div class="col-12 col-md-4">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold">Status Kamar</h6>
                            <div style="height:260px;">
                                <canvas id="donutChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    {{-- PROFILE MODAL --}}
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">
                <div class="mb-3">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>

                <a href="{{ route('pemilik.profile') }}" class="btn btn-primary w-100 mb-2">
                    Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* BAR CHART (seperti figma) */
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Jumlah Penyewaan',
                    data: [3, 6, 9, 12, 15, 10],
                }]
            },
            options: {
                maintainAspectRatio: false
            }
        });

        /* DONUT STATUS KAMAR */
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Kosong', 'Terisi'],
                datasets: [{
                    data: [32, 68],
                    backgroundColor: ['#22c55e', '#0d6efd']
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%'
            }
        });
    </script>
    <style>
@media (max-width: 768px) {

    .p-4 {
        padding: 12px !important;
    }

    h3 {
        font-size: 20px !important;
    }

    .card h3 {
        font-size: 18px !important;
    }

    canvas {
        max-height: 220px !important;
    }

}
</style>
@endsection
