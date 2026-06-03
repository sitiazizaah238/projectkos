@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
    use App\Models\Pembayaran;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $userId = Auth::id();

    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $totalKos = Kos::where('user_id', $userId)->count();
    $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
    $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
    $totalPenyewa = PengajuanSewa::whereIn('kos_id', $kosIds)->where('status', 'aktif')->count();

    // =====================
    // NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)
        ->where('is_read', false)
        ->where(function ($query) {
            $query
                ->whereIn('status', ['disetujui', 'nonaktif'])
                ->orWhere('edit_request_status', 'ditolak')
                ->orWhere('edit_request_status', 'disetujui');
        })
        ->latest()
        ->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $notifPembayaran = Pembayaran::whereHas('pengajuan.kos', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->where('status', 'menunggu')
        ->latest()
        ->get();
    $jumlahNotif = $notifKos->count() + $notifPengajuan->count() + $notifPembayaran->count();
@endphp

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-pemilik')
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
            <div class="px-4 pt-3 pb-4">

                {{-- BREADCRUMB --}}
                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Detail Kos
                </h3>

                <small class="text-muted d-block mb-4">
                    Detail Kos / Data kos
                </small>

                <h4 class="fw-semibold mt-3 mb-4">
                    {{ $kos->nama_kos }}
                </h4>

                <div class="row">

                    {{-- KIRI --}}
                    <div class="col-md-7">

                        {{-- FOTO --}}
                        @if ($kos->foto && count($kos->foto) > 0)
                            <div id="carouselKos" class="carousel slide" data-bs-ride="carousel">

                                <div class="carousel-inner">

                                    @foreach ($kos->foto as $key => $f)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $f) }}" class="d-block w-100 rounded-4 shadow"
                                                style="height:400px; object-fit:cover;">
                                        </div>
                                    @endforeach

                                </div>

                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselKos"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>

                                <button class="carousel-control-next" type="button" data-bs-target="#carouselKos"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>

                            </div>
                        @else
                            <div class="bg-light text-center p-5 rounded">
                                Tidak ada foto
                            </div>
                        @endif
                    </div>
                        <div class="col-md-5">

                            {{-- INFORMASI --}}
                            <div class="card shadow rounded-4 mb-3"
                                style="background:linear-gradient(135deg,#f8f9fa,#ffffff);">
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
                                        <div class="col-5 text-muted">Status Verifikasi</div>
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
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-7">
                            {{-- MAP --}}
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-body p-4">
                                    <h5 class="fw-semibold mb-3">Peta Lokasi</h5>
                                    <div id="map" style="height: 220px; width: 100%; border-radius: 8px; z-index: 1;"></div>
                                    <a href="https://www.google.com/maps?q={{ $kos->latitude ?: -6.4005784 }},{{ $kos->longitude ?: 108.2100865 }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                        <i class="bi bi-geo-alt"></i> Buka di Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 d-flex flex-column">
                            {{-- FASILITAS --}}
                            <div class="card shadow rounded-4 mb-3 flex-grow-1" style="background:#f8f9fc;">
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
          {{-- PROFILE MODAL --}}
        <div class="modal fade" id="profileModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content p-3 text-center" style="border-radius:20px;">
                    <div class="mb-3">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>

                    <a href="{{ route('pemilik.profile') }}" class="btn btn-primary w-100 mb-2">
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
    @endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var lat = {{ $kos->latitude ?: -6.4005784 }};
            var lng = {{ $kos->longitude ?: 108.2100865 }};
            
            var map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>" + {!! json_encode($kos->nama_kos) !!} + "</b><br>" + {!! json_encode($kos->lokasi) !!}).openPopup();
        });
    </script>
@endpush
