@extends('layouts.app')
@php
    use App\Models\Kos;

    $kos = Kos::with('kamars')
        ->where('status', 'disetujui')
        ->latest()
        ->take(3) // ⬅️ BATASI 3 DATA
        ->get();
@endphp

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>

            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold">Dashboard Penyewa</h3>
                <small class="text-muted">Halo! Selamat Datang {{ Auth::user()->name }}</small>

                {{-- ================= CARD STATISTIK ================= --}}
                <div class="row mt-4 g-3 align-items-stretch">

                    {{-- TOTAL KOS --}}
                    <div class="col-md-3 d-flex">
                        <div class="card shadow-sm border-start border-4 border-warning w-100 h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Total Kos Di Sewa</small>
                                    <h3 class="fw-bold mb-0">0</h3>
                                    <small class="text-primary">Lihat Semua Kos →</small>
                                </div>

                                <div class="bg-warning text-white rounded d-flex align-items-center justify-content-center"
                                    style="width:55px;height:55px;">
                                    <i class="bi bi-house-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEWA AKTIF --}}
                    <div class="col-md-3 d-flex">
                        <div class="card shadow-sm border-start border-4 border-success w-100 h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Sewa Aktif</small>
                                    <h3 class="fw-bold mb-0">0</h3>
                                    <small class="text-primary">Lihat Semua Sewa →</small>
                                </div>

                                <div class="bg-success text-white rounded d-flex align-items-center justify-content-center"
                                    style="width:55px;height:55px;">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MENUNGGU PERSETUJUAN --}}
                    <div class="col-md-3 d-flex">
                        <div class="card shadow-sm border-start border-4 border-primary w-100 h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Menunggu Persetujuan</small>
                                    <h3 class="fw-bold mb-0">0</h3>
                                    <small class="text-primary">Lihat data persetujuan →</small>
                                </div>

                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                    style="width:55px;height:55px;">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIWAYAT --}}
                    <div class="col-md-3 d-flex">
                        <div class="card shadow-sm border-start border-4 border-danger w-100 h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Riwayat Sewa</small>
                                    <h3 class="fw-bold mb-0">0</h3>
                                    <small class="text-primary">Lihaat Semua riwayat →</small>
                                </div>

                                <div class="bg-danger text-white rounded d-flex align-items-center justify-content-center"
                                    style="width:55px;height:55px;">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ================= KOS SAAT INI + STATUS ================= --}}
                <div class="row mt-4 g-3 align-items-stretch">


                    {{-- KOS SAAT INI --}}
                    <div class="col-md-6 d-flex">
                        <div class="card p-3 shadow-sm w-100 h-100">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-1"></i> Kos Saat Ini
                            </h6>

                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><b>Kos Ungu</b></p>
                                    <small>Kamar : A01</small><br>
                                    <small>Durasi Sewa : 3 Bulan</small><br>
                                    <small>Status : Aktif</small>
                                </div>

                                <div class="col-6">
                                    <small>Tanggal Pengajuan</small>
                                    <p>01-01-2026</p>

                                    <small>Tanggal Selesai</small>
                                    <p>01-03-2026</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STATUS PEMBAYARAN --}}
                    <div class="col-md-6">
                        <div class="card p-3 shadow-sm">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-1"></i> Status Pembayaran
                            </h6>

                            <table class="table table-borderless">
                                <tr>
                                    <td>Status</td>
                                    <td>Lunas</td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>QRIS</td>
                                </tr>
                                <tr>
                                    <td>Terakhir Bayar</td>
                                    <td>01-01-2026</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ================= REKOMENDASI KOS ================= --}}
                <div class="card mt-4 p-3 shadow-sm">

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-star-fill"></i> Rekomendasi Kos Untuk Anda
                    </h6>

                    <div class="row g-4">

                        @forelse($kos as $k)
                            <div class="col-md-4 d-flex">
                                <div class="card shadow-sm w-100 h-100">

                                    @if ($k->foto)
                                        <img src="{{ asset('storage/' . $k->foto) }}" class="card-img-top"
                                            style="height:180px; object-fit:cover;">
                                    @else
                                        <img src="https://via.placeholder.com/300x180" class="card-img-top">
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        @php
                                            $minHarga = $k->kamars->min('harga');
                                            $maxHarga = $k->kamars->max('harga');
                                        @endphp
                                        <h6 class="fw-bold text-truncate">
                                            {{ $k->nama_kos }}
                                        </h6>

                                        <p class="small mb-1">
                                            <strong>Lokasi:</strong><br>
                                            <span class="text-muted">{{ $k->lokasi }}</span>
                                        </p>

                                        <p class="small">
                                            <strong>Tipe:</strong>
                                            {{ $k->tipe_kos }}
                                        </p>
                                        <p class="small mb-2">
                                            <strong>Harga:</strong><br>

                                            @if ($minHarga)
                                                <span class="text-success fw-bold">
                                                    Rp {{ number_format($minHarga, 0, ',', '.') }}
                                                    @if ($minHarga != $maxHarga)
                                                        - Rp {{ number_format($maxHarga, 0, ',', '.') }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">Belum ada kamar</span>
                                            @endif
                                        </p>
                                        <div class="mt-auto">
                                            <a href="{{ route('penyewa.kos.detail', $k->id) }}"
                                                class="btn btn-sm btn-primary w-100">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        @empty
                            <div class="col-12 text-center">
                                <div class="text-muted py-4">
                                    Belum ada kos yang disetujui
                                </div>
                            </div>
                        @endforelse

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

                            <a href="#" class="btn btn-primary w-100 mb-2">
                                Profil
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-danger w-100">
                                    Logout
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @endsection
