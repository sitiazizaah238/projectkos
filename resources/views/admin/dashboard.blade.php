@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        {{-- MAIN --}}
        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">
                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>
            </div>


            {{-- CONTENT --}}
            <div class="p-4">
                <h3 class="fw-bold">Dashboard Admin</h3>
                <small class="text-muted">Dashboard / Ringkasan</small>

                {{-- CARDS --}}
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card card-stat p-3 shadow-sm">
                            <div class="d-flex justify-content-between">
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
                            <div class="d-flex justify-content-between">
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
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Log Aktivitas
                                    </div>
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
                {{-- GRAFIK --}}
                <div class="row mt-4">

                    <div class="col-md-8">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold">Grafik Pemesanan Perbulan</h6>
                            <div style="height:270px;">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold">Persentase Akun</h6>
                            <div style="height:270px;">
                                <canvas id="donutChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- MODAL PROFILE --}}
        <div class="modal fade" id="profileModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content p-3 text-center" style="border-radius:20px;">
                    <div class="mb-3">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>

                    <a href="{{ route('admin.profile') }}" class="btn btn-primary w-100 mb-2">
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

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Pemesanan',
                        data: [5, 18, 10, 22, 15, 20],
                        borderColor: '#0d6efd',
                        tension: 0.4
                    }]
                },
                options: {
                    maintainAspectRatio: false
                }
            });

            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pemilik', 'Penyewa'],
                    datasets: [{
                        data: [75, 25],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
options: {
    maintainAspectRatio: false,
    cutout: '70%',
    radius: '85%'
}
            });
        </script>
    @endsection
