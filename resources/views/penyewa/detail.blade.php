@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

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
                            <img src="{{ asset('storage/' . $kos->foto) }}" class="img-fluid rounded-4 shadow-sm"
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
                                    <th>#</th>
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
                    <a href="{{ route('penyewa.dashboard') }}" class="btn btn-primary rounded-pill px-4">
                        ← Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
