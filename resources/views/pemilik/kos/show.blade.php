@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
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
            <div class="p-4 mt-4">

                {{-- BREADCRUMB --}}
                <div class="mb-3">
                    <h2 class="fw-bold mb-1" style="font-size:2.2rem;">
                        Detail Kos
                    </h2>
                    <small class="text-muted fs-6">
                        <a href="{{ route('pemilik.kos.index') }}">Manajemen Kos</a> / Detail Kos
                    </small>
                </div>

              <h3 class="fw-bold mb-4" style="font-size:1.5rem;">
    {{ $kos->nama_kos }}
</h3>

                <div class="row">

                    {{-- KIRI --}}
                    <div class="col-md-7">

                        {{-- FOTO --}}
                        <div class="mb-3">
                            @if ($kos->foto)
                                <img src="{{ asset('storage/' . $kos->foto) }}" class="img-fluid rounded-4 shadow"
                                    style="width:100%; height:400px; object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-4"
                                    style="height:240px;">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>

                        {{-- MAP --}}
                        <div class="card shadow rounded-4">
                            <div class="card-body p-0">
                                <iframe src="https://www.google.com/maps?q={{ urlencode($kos->lokasi) }}&output=embed"
                                    width="100%" height="200" style="border:0;" allowfullscreen loading="lazy">
                                </iframe>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN --}}
                    <div class="col-md-5">

                        {{-- INFORMASI --}}
                        <div class="card shadow rounded-4 mb-3" style="background:linear-gradient(135deg,#f8f9fa,#ffffff);">
                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-4">
                                    <i class="bi bi-info-circle"></i> Informasi Kos
                                </h5>

                                <div class="row mb-3 fs-5">
                                    <div class="col-5 text-muted">Nama</div>
                                    <div class="col-7 fw-semibold">{{ $kos->nama_kos }}</div>
                                </div>

                                <div class="row mb-3 fs-5">
                                    <div class="col-5 text-muted">Lokasi</div>
                                    <div class="col-7 fw-semibold">{{ $kos->lokasi }}</div>
                                </div>

                                <div class="row mb-3 fs-5">
                                    <div class="col-5 text-muted">Tipe</div>
                                    <div class="col-7 fw-semibold">{{ $kos->tipe_kos }}</div>
                                </div>

                                <div class="row mb-3 fs-5">
                                    <div class="col-5 text-muted">Status</div>
                                    <div class="col-7">
                                        @if ($kos->status == 'disetujui')
                                            <span class="badge bg-success fs-6 px-3 py-2">Disetujui</span>
                                        @elseif($kos->status == 'ditolak')
                                            <span class="badge bg-danger fs-6 px-3 py-2">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">Menunggu</span>
                                        @endif
                                    </div>
                                </div>

                                <hr>

                                <div>
                                    <div class="text-muted mb-2 fs-6">Deskripsi</div>
                                    <div class="fs-6" style="line-height:1.7;">
                                        {{ $kos->deskripsi }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- FASILITAS --}}
                        <div class="card shadow rounded-4 mb-3" style="background:#f8f9fc;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-list-check"></i> Fasilitas Kos
                                </h5>

                                <div class="row fs-6">
                                    @if ($kos->fasilitas)
                                        @foreach ($kos->fasilitas as $f)
                                            <div class="col-6 mb-2">
                                                <i class="bi bi-check-circle text-success"></i> {{ $f }}
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted">Tidak ada fasilitas</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL --}}
                        <a href="{{ route('pemilik.kos.index') }}" class="btn btn-primary w-100 py-2 fs-5 rounded-3">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
