@extends('layouts.app')

@section('content')
    <style>
        /* khusus filter panel biar tidak ganggu komponen lain */
        #filterPanel .form-check-input {
            accent-color: #0d6efd;
            border: 1px solid #0d6efd;
            background-color: #fff;
        }

        #filterPanel .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-penyewa')
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
            {{-- CONTENT --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold" style="font-size: 30px;">Pencarian Kos Tersedia</h3>
                <small class="text-muted">Menampilkan/Mencari kos berdasarkan nama atau lokasi ter</small>

                {{-- FORM SEARCH (terpisah dari form filter) --}}
                <div class="mt-3 mb-4 d-flex gap-2 align-items-center flex-wrap">
                    <form method="GET" action="{{ route('penyewa.cari.kos') }}"
                        class="d-flex gap-2 align-items-center flex-wrap">
                        {{-- Pertahankan nilai filter saat search --}}
                        @foreach (request()->except('search') as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="input-group" style="max-width:400px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari nama kos...">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </form>

                    {{-- Tombol toggle filter (di luar form!) --}}
                    <button type="button" id="filterToggleBtn"
                        class="btn {{ request()->hasAny(['min_harga', 'max_harga', 'tipe_kos', 'fasilitas', 'tipe_harga']) ? 'btn-primary' : 'btn-outline-primary' }} d-flex align-items-center gap-1"
                        onclick="toggleFilter()">
                        <i class="bi bi-funnel"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                    </button>
                </div>

                {{-- ROW: FILTER + LIST --}}
                <div class="row" id="mainRow">

                    {{-- FILTER PANEL (form terpisah) --}}
                    <div class="col-md-3 mb-3" id="filterPanel" style="display:none;">
                        <form method="GET" action="{{ route('penyewa.cari.kos') }}">
                            {{-- Pertahankan nilai search saat filter --}}
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

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
                                        <label class="form-check-label">Campur</label>
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
                                                    value="Lemari Pakaian"
                                                    {{ in_array('Lemari Pakaian', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Lemari Pakaian</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Kipas Angin"
                                                    {{ in_array('Kipas Angin', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Kipas Angin</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Cermin" {{ in_array('Cermin', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Cermin</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                    value="Tempat Tidur"
                                                    {{ in_array('Tempat Tidur', $reqFas) ? 'checked' : '' }}>
                                                <label class="form-check-label">Tempat Tidur</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TIPE HARGA --}}
                                <div class="mb-4">
                                    <label class="fw-semibold mb-2">Tipe Pembayaran</label>
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
                        </form>
                    </div>

                    {{-- LIST KOS --}}
                    <div class="col" id="kosListCol">
                        <div class="row g-4">

                            @php
                                $fasilitasIcon = [
                                    'Parkir' => 'bi-p-circle',
                                    'Wifi' => 'bi-wifi',
                                    'CCTV' => 'bi-camera-video',
                                    'Dapur' => 'bi-cup-hot',
                                    'Musola' => 'bi-moon',
                                ];
                            @endphp

                            @forelse($kos as $k)
                                @php
                                    $tipeIcon = match ($k->tipe_kos) {
                                        'putra' => 'bi-gender-male',
                                        'putri' => 'bi-gender-female',
                                        'campur' => 'bi-gender-ambiguous',
                                        default => 'bi-house',
                                    };
                                    $fasKos = is_array($k->fasilitas) ? $k->fasilitas : [];
                                    $kamarTersedia = $k->kamars->where('status', 'tersedia');
                                    $kamarTermurah = $kamarTersedia->sortBy('harga')->first();
                                    $kamarTermahal = $kamarTersedia->sortBy('harga')->last();
                                    $tipeHargaList = $kamarTersedia->pluck('tipe_harga')->unique();
                                @endphp

                                <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm border-0 h-100"
                                        style="border-radius:16px; overflow:hidden;">

                                        {{-- Foto --}}
                                        {{-- Foto --}}
                                        @php
                                            $fotos = is_array($k->foto) ? $k->foto : json_decode($k->foto, true);
                                        @endphp

                                        @if ($fotos && count($fotos) > 0)
                                            <div id="carouselKos{{ $k->id }}" class="carousel slide"
                                                data-bs-ride="carousel" data-bs-interval="3000">

                                                <div class="carousel-inner">

                                                    @foreach ($fotos as $index => $foto)
                                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                            <img src="{{ asset('storage/' . $foto) }}"
                                                                class="d-block w-100"
                                                                style="height:180px; object-fit:cover;">
                                                        </div>
                                                    @endforeach

                                                </div>

                                                @if (count($fotos) > 1)
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#carouselKos{{ $k->id }}"
                                                        data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </button>

                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#carouselKos{{ $k->id }}"
                                                        data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </button>
                                                @endif

                                            </div>
                                        @else
                                            <div style="height:180px; background:#dee2e6;"
                                                class="d-flex align-items-center justify-content-center">
                                                <i class="bi bi-house fs-1 text-muted"></i>
                                            </div>
                                        @endif

                                        <div class="card-body d-flex flex-column">

                                            {{-- Nama + Tipe --}}
                                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                                <h6 class="fw-bold mb-0" style="min-width:0;">{{ $k->nama_kos }}</h6>
                                                <span class="badge bg-primary flex-shrink-0">
                                                    <i
                                                        class="bi {{ $tipeIcon }} me-1"></i>{{ ucfirst($k->tipe_kos) }}
                                                </span>
                                            </div>

                                            {{-- Alamat --}}
                                            <small class="text-muted mb-2 d-block">
                                                <i class="bi bi-geo-alt"></i> {{ $k->lokasi }}
                                            </small>

                                            {{-- Fasilitas Kos --}}
                                            @if (count($fasKos) > 0)
                                                <div class="mb-2">
                                                    <small class="text-muted fw-semibold">
                                                        <i class="bi bi-building me-1 text-primary"></i>Fasilitas:
                                                    </small>
                                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                                        @foreach ($fasKos as $fas)
                                                            <span class="badge text-dark d-flex align-items-center gap-1"
                                                                style="background:linear-gradient(135deg,#e8f4fd,#d1ecf1);border:1px solid #bee5eb;font-weight:500;">
                                                                <i
                                                                    class="bi {{ $fasilitasIcon[$fas] ?? 'bi-check-circle' }} text-primary"></i>
                                                                {{ $fas }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Harga --}}
                                            <div class="mb-3">
                                                @if ($kamarTermurah)
                                                    <small class="text-muted fw-semibold">
                                                        <i class="bi bi-tag me-1 text-primary"></i>Harga mulai dari:
                                                    </small>
                                                    <div>
                                                        <span class="fw-bold text-dark">
                                                            Rp{{ number_format($kamarTermurah->harga, 0, ',', '.') }}
                                                        </span>
                                                        @if ($kamarTermahal && $kamarTermahal->harga != $kamarTermurah->harga)
                                                            <span class="text-muted"> – </span>
                                                            <span class="fw-bold text-dark">
                                                                Rp{{ number_format($kamarTermahal->harga, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                        <span
                                                            class="text-muted">/{{ ucfirst($kamarTermurah->tipe_harga) }}</span>
                                                    </div>
                                                    @if ($tipeHargaList->contains('tahunan'))
                                                        <div class="small text-primary mt-1">
                                                            Tersedia: Tahunan
                                                        </div>
                                                    @endif
                                                @else
                                                    @if ($k->kamars->count() > 0)
                                                        <span class="badge bg-danger">Kamar Penuh</span>
                                                    @else
                                                        <small class="text-muted">Belum ada kamar</small>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Tombol --}}
                                            <div class="mt-auto">
                                                <a href="{{ route('penyewa.kos.detail', ['id' => $k->id, 'from' => 'cari']) }}"
                                                    class="btn btn-primary w-100">Detail</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="col-12 text-center text-muted py-4">
                                    Tidak ada kos ditemukan
                                </div>
                            @endforelse
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $kos->links() }}
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
                        <a href="{{ route('penyewa.profile') }}" class="btn btn-primary w-100 mb-2">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger w-100">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let filterVisible = false;

        // Buka otomatis jika ada filter aktif dari request
        const hasFilter =
            {{ request()->hasAny(['min_harga', 'max_harga', 'tipe_kos', 'fasilitas', 'tipe_harga']) ? 'true' : 'false' }};

        if (hasFilter) {
            filterVisible = true;
            document.getElementById('filterPanel').style.display = 'block';
            document.getElementById('kosListCol').classList.remove('col');
            document.getElementById('kosListCol').classList.add('col-md-9');
        }

        function toggleFilter() {
            const panel = document.getElementById('filterPanel');
            const kosListCol = document.getElementById('kosListCol');
            const btn = document.getElementById('filterToggleBtn');

            filterVisible = !filterVisible;

            if (filterVisible) {
                panel.style.display = 'block';
                kosListCol.classList.remove('col');
                kosListCol.classList.add('col-md-9');
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                panel.style.display = 'none';
                kosListCol.classList.remove('col-md-9');
                kosListCol.classList.add('col');
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }
        }
    </script>

@endsection
