@extends('layouts.app')

@section('content')
<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('components.sidebar-penyewa')

    <div class="flex-grow-1">

        {{-- ================= TOPBAR (SAMA KAYA DASHBOARD) ================= --}}
        <div class="topbar d-flex justify-content-end align-items-center px-4">

            <button type="button"
                class="btn text-white d-flex align-items-center"
                data-bs-toggle="modal"
                data-bs-target="#profileModal">

                <span class="me-2">{{ Auth::user()->name }}</span>
                <i class="bi bi-person-circle fs-3"></i>
            </button>

        </div>

        {{-- ================= CONTENT ================= --}}
        <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold mb-0">{{ $kos->nama_kos }}</h2>

                @php
                    $total = $kos->kamars()->count();
                    $tersedia = $kos->kamars()->where('status', 'tersedia')->count();
                    $terisi = $kos->kamars()->where('status', '!=', 'tersedia')->count();
                @endphp

                <span class="badge bg-secondary p-2">
                    Total Kamar : {{ $total }}
                    Terisi : {{ $terisi }}
                    Tersedia : {{ $tersedia }}
                </span>
            </div>

            {{-- ================= FOTO + INFO ================= --}}
            <div class="row g-4">

                {{-- FOTO --}}
                <div class="col-md-7">
                    @if ($kos->foto)
                        <img src="{{ asset('storage/' . $kos->foto) }}"
                             class="img-fluid rounded-4 shadow-sm"
                             style="height:280px; object-fit:cover; width:100%;">
                    @endif
                </div>

                {{-- INFORMASI KOS --}}
                <div class="col-md-5">
                    <div class="card shadow-sm rounded-4 p-3 h-100">

                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle"></i> Informasi Kos
                        </h6>

                        <div class="row mb-2">
                            <div class="col-6 text-muted">Nama Kos</div>
                            <div class="col-6 fw-semibold text-end">
                                {{ $kos->nama_kos }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6 text-muted">Lokasi Kos</div>
                            <div class="col-6 fw-semibold text-end">
                                {{ $kos->lokasi }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6 text-muted">Tipe Kos</div>
                            <div class="col-6 fw-semibold text-end">
                                {{ $kos->tipe_kos }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">Deskripsi</small>
                            <p class="mb-0">{{ $kos->deskripsi }}</p>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ================= FASILITAS KOS ================= --}}
            @if ($kos->fasilitas)
                <div class="card mt-4 shadow-sm rounded-4 p-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="bi bi-house-door"></i> Fasilitas Kos
                    </h6>

                    <div class="row">
                        @foreach ($kos->fasilitas as $f)
                            <div class="col-md-3 mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $f }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ================= TABEL KAMAR ================= --}}
            <div class="card mt-4 shadow-sm rounded-4 overflow-hidden">

                <div class="bg-dark text-white p-3 fw-semibold">
                    <i class="bi bi-door-open"></i>
                    Daftar Kamar Yang Tersedia
                </div>

                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Kamar</th>
                                <th>Harga</th>
                                <th>Fasilitas Kamar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($kos->kamars as $index => $kamar)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $kamar->nama_kamar }}</td>

                                    <td>
                                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if ($kamar->fasilitas)
                                            {{ implode(', ', $kamar->fasilitas) }}
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-success">
                                            {{ $kamar->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary rounded-pill">
                                            Ajukan Sewa >
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">
                                        Tidak ada kamar tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- ================= BUTTON KEMBALI ================= --}}
            <div class="text-end mt-4">
                <a href="{{ route('penyewa.dashboard') }}"
                   class="btn btn-primary rounded-pill px-4">
                    ← Kembali
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ================= PROFILE MODAL ================= --}}
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
