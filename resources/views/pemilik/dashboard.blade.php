@extends('layouts.app')

@section('content')
<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('components.sidebar-pemilik')

    <div class="flex-grow-1">

        {{-- TOPBAR --}}
        <div class="topbar d-flex justify-content-end align-items-center px-4">
            <button type="button"
                class="btn text-white d-flex align-items-center"
                data-bs-toggle="modal"
                data-bs-target="#profileModal">

                <span class="me-2">{{ Auth::user()->name }}</span>
                <i class="bi bi-person-circle fs-3"></i>
            </button>
        </div>

        {{-- CONTENT --}}
        <div class="p-4">

            <h3 class="fw-bold">Dashboard! Selamat Datang {{ Auth::user()->name }}</h3>
            <small class="text-muted">Dashboard / Ringkasan</small>

            {{-- ====== CARD STATISTIK ====== --}}
            <div class="row mt-4 g-3">

                {{-- TOTAL KOS --}}
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Total Kos</small>
                                <h3 class="fw-bold">3</h3>
                                <small class="text-primary">Lihat Semua Kos</small>
                            </div>
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="bi bi-house-door-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOTAL KAMAR --}}
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-start border-4 border-danger">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Total Kamar</small>
                                <h3 class="fw-bold">25</h3>
                                <small class="text-danger">Lihat Semua Kamar</small>
                            </div>
                            <div class="bg-danger text-white p-3 rounded">
                                <i class="bi bi-door-open-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KAMAR TERSEDIA --}}
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-start border-4 border-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Kamar Tersedia</small>
                                <h3 class="fw-bold">8</h3>
                                <small class="text-success">Lihat Semua Data</small>
                            </div>
                            <div class="bg-success text-white p-3 rounded">
                                <i class="bi bi-door-closed-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOTAL PENYEWA --}}
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-start border-4 border-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Total Penyewa</small>
                                <h3 class="fw-bold">37</h3>
                                <small class="text-info">Lihat Semua Data</small>
                            </div>
                            <div class="bg-info text-white p-3 rounded">
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ====== GRAFIK ====== --}}
            <div class="row mt-4">

                {{-- BAR CHART --}}
                <div class="col-md-8">
                    <div class="card p-3 shadow-sm">
                        <h6 class="fw-bold">Grafik Penyewaan Bulanan</h6>
                        <div style="height:260px;">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- DONUT CHART --}}
                <div class="col-md-4">
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
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
        datasets: [{
            label: 'Jumlah Penyewaan',
            data: [3,6,9,12,15,10],
        }]
    },
    options:{
        maintainAspectRatio:false
    }
});

/* DONUT STATUS KAMAR */
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data:{
        labels:['Kosong','Terisi'],
        datasets:[{
            data:[32,68],
            backgroundColor:['#22c55e','#0d6efd']
        }]
    },
    options:{
        maintainAspectRatio:false,
        cutout:'70%'
    }
});
</script>

@endsection
