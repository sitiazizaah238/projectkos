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

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                            style="
            width:40px;
            height:40px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid white;
         ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>

            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold">Kos Tersedia</h3>
                <small class="text-muted">Penyewaan Kos / Mencari Kos</small>

                <form method="GET" action="{{ route('penyewa.cari.kos') }}">

                    {{-- search bar --}}
                    <div class="mt-3 mb-4">
                        <div class="input-group" style="max-width:400px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari nama atau lokasi...">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>

                    <div class="row">
                        {{-- filter --}}
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 p-4" style="border-radius:16px;">
                                <h6 class="fw-bold mb-4">Filter Pencarian</h6>

                                {{-- HARGA --}}
                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">Harga</label>
                                    <input type="number" name="min_harga" value="{{ request('min_harga') }}"
                                        class="form-control mb-2" placeholder="Min Harga">
                                    <input type="number" name="max_harga" value="{{ request('max_harga') }}"
                                        class="form-control" placeholder="Max Harga">
                                </div>

                                {{-- TIPE KOS --}}
                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">Tipe Kos</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_kos" value="putra"
                                            {{ request('tipe_kos') == 'putra' ? 'checked' : '' }}>
                                        <label class="form-check-label">Putra</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_kos" value="putri"
                                            {{ request('tipe_kos') == 'putri' ? 'checked' : '' }}>
                                        <label class="form-check-label">Putri</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_kos" value="campur"
                                            {{ request('tipe_kos') == 'campur' ? 'checked' : '' }}>
                                        <label class="form-check-label">Putra/Putri</label>
                                    </div>
                                </div>

                                {{-- FASILITAS KAMAR --}}
                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">Fasilitas Kamar</label>
                                    @php $reqFas = request('fasilitas') ?? []; @endphp
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Kamar Mandi Dalam"
                                                    {{ in_array('Kamar Mandi Dalam', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">KM Dalam</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="AC" {{ in_array('AC', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">AC</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Meja" {{ in_array('Meja', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Meja</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Lemari" {{ in_array('Lemari', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Lemari</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TIPE HARGA --}}
                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">Tipe Harga</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_harga" value="bulanan"
                                            {{ request('tipe_harga') == 'bulanan' ? 'checked' : '' }}>
                                        <label class="form-check-label">Bulanan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_harga" value="tahunan"
                                            {{ request('tipe_harga') == 'tahunan' ? 'checked' : '' }}>
                                        <label class="form-check-label">Tahunan</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                </form> {{-- TUTUP FORM DI SINI --}}

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

            {{-- ================= PROFILE MODAL ================= --}}
            <div class="modal fade" id="profileModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content p-3 text-center" style="border-radius:20px;">

                        <div class="mb-3">
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </div>

                        <a href="{{ route('penyewa.profile') }}" class="btn btn-primary w-100 mb-2">
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
