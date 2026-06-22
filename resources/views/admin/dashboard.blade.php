@extends('layouts.app')
@php
    use App\Models\User;
    use App\Models\Kos;
    use App\Models\LogAktivitas;

    $totalKos = Kos::count();
    $totalPemilik = User::where('role', 'pemilik')->count();
    $totalLog = LogAktivitas::count();

    $disetujui = Kos::where('status', 'disetujui')->count();
    $ditolak = Kos::where('status', 'ditolak')->count();
    $menunggu = Kos::where('status', 'menunggu')->count();
@endphp

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        {{-- MAIN --}}
        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex flex-wrap justify-content-end align-items-center px-4 gap-3">

                @include('components.notif-admin')

                {{-- PROFILE --}}
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="text-white">{{ Auth::user()->name }}</span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="
                width:35px;
                height:35px;
                border-radius:50%;
                object-fit:cover;
            ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>

            </div>



            {{-- CONTENT --}}
            <div class="p-3 p-md-4">
                <h3 class="fw-bold" style="font-size: clamp(20px,2vw,30px);">
                    Dashboard Admin
                </h3>
                <small class="text-muted">Dashboard / Ringkasan</small>

                {{-- CARDS --}}
                <div class="row mt-4 g-3">

                    <div class="col-12 col-md-4">
                        <div class="card card-stat p-3 shadow-sm">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Daftar Kos</div>
                                    <h3 class="fw-bold">{{ $totalKos }}</h3>
                                    <small class="text-primary">
                                        Disetujui: {{ $disetujui }} |
                                        Ditolak: {{ $ditolak }} |
                                        Menunggu: {{ $menunggu }}
                                    </small>
                                </div>
                                <div class="icon bg-primary">
                                    <i class="bi bi-house-door-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <a href="{{ route('admin.pemilik.index') }}" class="text-decoration-none text-dark">
                            <div class="card card-stat p-3 shadow-sm hover-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-muted">Pemilik Kos</div>
                                        <h3 class="fw-bold">{{ $totalPemilik }}</h3>
                                        <small class="text-success">Lihat Semua Data</small>
                                    </div>
                                    <div class="icon bg-success">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>


                    <div class="col-12 col-md-4">
                        <a href="{{ route('admin.log.index') }}" class="text-decoration-none text-dark">
                            <div class="card card-stat p-3 shadow-sm hover-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-muted">Log Aktivitas</div>
                                        <h3 class="fw-bold">{{ $totalLog }}</h3>
                                        <small class="text-warning">Lihat Semua Data</small>
                                    </div>
                                    <div class="icon bg-warning">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                {{-- GRAFIK --}}
                <div class="row mt-4 g-3">

                    <div class="col-12 col-lg-8">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold">Monitoring Status Pengajuan Kos</h6>
                            <div style="height:260px;">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card p-3 shadow-sm d-flex flex-column justify-content-between">
                            <div class="fw-bold pb-2 mb-3" style="border-bottom: 3px solid #6b7280; width: 100%;">
                                Persentase Akun
                            </div>
                            <div class="d-flex justify-content-center align-items-center"
                                style="height:200px; margin-top:-10px;">
                                <canvas id="donutChart"></canvas>
                            </div>
                            <div class="mt-1 border-top pt-1" style="line-height:1.2;">
                                <div class="d-flex justify-content-between mb-1" style="margin-bottom:2px;">
                                    <span class="text-muted">Akun Pemilik</span>
                                    <strong>{{ $totalPemilik }}</strong>
                                </div>

                                <div class="d-flex justify-content-between" style="margin-top:2px;">
                                    <span class="text-muted">Total Kos</span>
                                    <strong>{{ $totalKos }}</strong>
                                </div>
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
                type: 'bar',
                data: {
                    labels: ['Disetujui', 'Ditolak', 'Menunggu'],
                    datasets: [{
                        label: 'Jumlah Kos',
                        data: [
                            {{ $disetujui }},
                            {{ $ditolak }},
                            {{ $menunggu }}
                        ],
                        backgroundColor: ['#198754', '#dc3545', '#ffc107'],
                        barPercentage: 0.9,
                        categoryPercentage: 0.9,
                        maxBarThickness: 60
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pemilik', 'Total Kos'],
                    datasets: [{
                        data: [{{ $totalPemilik }}, {{ $totalKos }}],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '65%',
                    radius: '65%',

                    layout: {
                        padding: {
                            top: 2
                        }
                    }
                }
            });
        </script>

        {{-- RESPONSIVE ONLY (AMAN TANPA RUSAK DESKTOP) --}}
        <style>
            @media (max-width: 768px) {
                .topbar {
                    flex-wrap: wrap !important;
                    gap: 10px !important;
                    padding: 10px !important;
                }

                .card-stat {
                    margin-bottom: 10px;
                }

                canvas {
                    height: 240px !important;
                }

                h3 {
                    font-size: 22px !important;
                }
            }
        </style>
    @endsection
