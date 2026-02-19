@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR (SAMA KAYA DASHBOARD) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>

            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold">Kos Tersedia</h3>
                <small class="text-muted">Penyewaan Kos / Mencari Kos</small>

                {{-- SEARCH --}}
                <form method="GET" action="{{ route('penyewa.cari.kos') }}" class="mt-3 mb-4">
                    <div class="input-group" style="max-width:400px;">
                        <input type="text" name="search" value="{{ $search }}" class="form-control"
                            placeholder="Search">
                        <button class="btn btn-primary">Cari</button>
                    </div>
                </form>

                <div class="row">

                    {{-- FILTER --}}
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 p-4" style="border-radius:16px;">

                            <h6 class="fw-bold mb-4">Filter Pencarian</h6>

                            {{-- ================= HARGA ================= --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">Harga</label>
                                <input type="text" name="min_harga" class="form-control mb-2" placeholder="Min Harga">
                                <input type="text" name="max_harga" class="form-control" placeholder="Max Harga">
                            </div>

                            {{-- ================= TIPE KOS ================= --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">Tipe Kos</label>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_kos" value="putra">
                                    <label class="form-check-label">Putra</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_kos" value="putri">
                                    <label class="form-check-label">Putri</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_kos" value="campur">
                                    <label class="form-check-label">Putra/Putri</label>
                                </div>
                            </div>

                            {{-- ================= FASILITAS KAMAR ================= --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">Fasilitas Kamar</label>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="kamar_mandi_dalam">
                                            <label class="form-check-label">KM Dalam</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="ac">
                                            <label class="form-check-label">AC</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="kipas_angin">
                                            <label class="form-check-label">Kipas</label>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="meja">
                                            <label class="form-check-label">Meja</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="lemari">
                                            <label class="form-check-label">Lemari</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                value="cermin">
                                            <label class="form-check-label">Cermin</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= TIPE HARGA ================= --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">Tipe Harga</label>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_harga" value="bulanan">
                                    <label class="form-check-label">Bulanan</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe_harga" value="tahunan">
                                    <label class="form-check-label">Tahunan</label>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 py-2">
                                Terapkan Filter
                            </button>

                        </div>
                    </div>

                    {{-- LIST KOS --}}
                    <div class="col-md-9">
                        <div class="row g-4">

                            @forelse($kos as $k)
                                <div class="col-md-6 col-lg-4">

                                    <div class="card shadow-sm border-0 h-100" style="border-radius:12px;">

                                        @if ($k->foto)
                                            <img src="{{ asset('storage/' . $k->foto) }}" class="card-img-top"
                                                style="height:150px;object-fit:cover;border-radius:12px 12px 0 0;">
                                        @endif

                                        <div class="card-body d-flex flex-column">

                                            @php
                                                $kamarTermurah = $k->kamars->sortBy('harga')->first();
                                            @endphp

                                            <h6 class="fw-bold text-truncate mb-1">
                                                {{ $k->nama_kos }}
                                            </h6>

                                            <small class="text-muted mb-1">
                                                {{ $k->lokasi }}
                                            </small>

                                            {{-- TIPE KOS --}}
                                            <small class="mb-2">
                                                <span class="badge bg-light text-dark border">
                                                    {{ ucfirst($k->tipe_kos) }}
                                                </span>
                                            </small>

                                            {{-- HARGA + TIPE HARGA --}}
                                            <div class="mt-1 small text-nowrap">

                                                @if ($kamarTermurah)
                                                    <span class="text-muted">
                                                        Mulai Dari :
                                                    </span>

                                                    <span class="fw-bold text-dark">
                                                        Rp.{{ number_format($kamarTermurah->harga, 0, ',', '.') }}
                                                    </span>

                                                    <span class="text-muted">
                                                        /{{ ucfirst($kamarTermurah->tipe_harga) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        Belum ada kamar
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-auto">
                                                <a href="{{ route('penyewa.kos.detail', $k->id) }}"
                                                    class="btn btn-sm btn-primary w-100 mt-3">
                                                    Detail
                                                </a>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            @empty
                                <div class="col-12 text-center text-muted py-4">
                                    Tidak ada kos ditemukan
                                </div>
                            @endforelse

                        </div>
                    </div>

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
