@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f5f7fb;
    }
    .sidebar {
        width: 260px;
        min-height: 100vh;
        background: #fdfdfd;
        border-right: 1px solid #e5e7eb;
    }
    .sidebar .nav-link {
        color: #333;
        margin-bottom: 8px;
        border-radius: 8px;
    }
    .sidebar .nav-link.active {
        background: #0d6efd;
        color: #fff;
    }
    .topbar {
        height: 64px;
        background: #0d6efd;
        color: white;
    }
    .card-stat {
        border-radius: 14px;
        border: none;
    }
    .card-stat .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: white;
    }
</style>

<div class="d-flex">

    <!-- SIDEBAR -->
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
                <a class="nav-link active" href="#">
                    <i class="bi bi-speedometer2"></i> Dashboard
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

    <!-- MAIN -->
    <div class="flex-grow-1">

<!-- TOPBAR -->
<div class="topbar d-flex justify-content-end align-items-center px-4">
    <button class="btn text-white d-flex align-items-center"
            data-bs-toggle="modal"
            data-bs-target="#profileModal">
        <span class="me-2">{{ Auth::user()->name }}</span>
     <i class="bi bi-person-circle fs-3"></i>
    </button>
</div>

        <!-- CONTENT -->
        <div class="p-4">
            <h3 class="fw-bold">Dashboard Admin</h3>
            <small class="text-muted">Dashboard / Ringkasan</small>

            <!-- CARDS -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card card-stat p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Daftar Kos</div>
                                <h3 class="fw-bold">3</h3>
                                <small class="text-primary">Lihat Semua Kos</small>
                            </div>
                            <div class="icon bg-primary">
                                <i class="bi bi-house-door-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Pemilik Kos</div>
                                <h3 class="fw-bold">67</h3>
                                <small class="text-success">Lihat Semua Data</small>
                            </div>
                            <div class="icon bg-success">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted">Log</div>
                                <h3 class="fw-bold">14</h3>
                                <small class="text-warning">Lihat Semua Data</small>
                            </div>
                            <div class="icon bg-warning">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!-- MODAL PROFILE (FIXED, NO FOTO) -->
<div class="modal fade" id="profileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content p-3 text-center"
         style="border-radius:20px;">

        <div class="mb-3">
            <div class="fw-bold">{{ Auth::user()->name }}</div>
            <small class="text-muted">{{ Auth::user()->email }}</small>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="btn btn-primary w-100 mb-2"
           style="border-radius:12px;">
            Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="btn btn-danger w-100"
                style="border-radius:12px;">
                Logout
            </button>
        </form>

    </div>
  </div>
</div>
            <!-- GRAFIK -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card p-3 shadow-sm">
                        <h6 class="fw-bold">Grafik Pemesanan Perbulan</h6>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 shadow-sm">
                        <h6 class="fw-bold">Persentase Akun</h6>
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
        datasets: [{
            label: 'Pemesanan',
            data: [5,18,10,22,15,20],
            borderColor: '#0d6efd',
            tension: 0.4
        }]
    }
});

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pemilik','Penyewa'],
        datasets: [{
            data: [75,25],
            backgroundColor: ['#198754','#dc3545']
        }]
    }
});
</script>
@endsection
