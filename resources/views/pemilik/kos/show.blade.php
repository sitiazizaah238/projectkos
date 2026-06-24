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
                @include('components.chat-icon-pemilik')
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
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-header bg-white border-0 pt-4 px-4">
                                <h5 class="mb-0 fw-semibold">Foto Kos</h5>
                            </div>
                            <div class="card-body pt-3 px-4 pb-4">
                                @if ($kos->foto && count($kos->foto) > 0)
                                    <div id="carouselKos" class="carousel slide" data-bs-ride="carousel">

                                        <div class="carousel-inner rounded-4 overflow-hidden" style="height:280px;">

                                            @foreach ($kos->foto as $key => $f)
                                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $f) }}" class="d-block w-100"
                                                        style="height:280px; object-fit:cover;">
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

                                    <div class="row g-2 mt-2">
                                        @foreach ($kos->foto as $foto)
                                            <div class="col-4">
                                                <img src="{{ asset('storage/' . $foto) }}" class="w-100 rounded-3"
                                                    style="height:70px; object-fit:cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light border text-muted mb-0">Belum ada foto kos.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0">

                        {{-- INFORMASI --}}
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">

                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-info-circle me-2"></i> Informasi Kos
                                </h5>
                                <div class="mb-2"><span class="text-muted">Nama Kos:</span> {{ $kos->nama_kos }}</div>
                                <div class="mb-2"><span class="text-muted">Lokasi Kos:</span> {{ $kos->lokasi }}</div>
                                <div class="mb-2"><span class="text-muted">Tipe Kos:</span> {{ $kos->tipe_kos }}</div>
                                <div class="mb-2">
                                    <span class="text-muted">Status Verifikasi:</span>
                                    @if ($kos->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($kos->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </div>

                            </div>
                        </div>
                        {{-- TAMBAHAN BARU --}}

                        @if ($kos->status == 'ditolak' && $kos->alasan)
                            <div class="alert alert-danger mt-3 mb-4 shadow-sm rounded">
                                <div class="fw-semibold mb-1">
                                    Alasan Penolakan Admin :
                                </div>
                                {{ $kos->alasan }}
                            </div>
                        @endif
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-card-text me-2"></i> Deskripsi Kos
                                </h5>
                                <div class="text-muted" style="white-space: pre-line;">{{ $kos->deskripsi ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-7">
                        {{-- MAP --}}
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-geo-alt-fill me-2"></i> Peta Lokasi
                                </h5>
                                <div id="map" style="height: 220px; width: 100%; border-radius: 8px; z-index: 1;">
                                </div>
                                <a href="https://www.google.com/maps?q={{ $kos->latitude ?: -6.4005784 }},{{ $kos->longitude ?: 108.2100865 }}"
                                    target="_blank" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                    <i class="bi bi-geo-alt"></i> Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        {{-- FASILITAS --}}
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-stars me-2"></i> Fasilitas Kos
                                </h5>
                                @php
                                    $fasilitas = is_array($kos->fasilitas) ? $kos->fasilitas : [];

                                    $iconMap = [
                                        'parkir' => 'bi bi-p-circle',
                                        'wifi' => 'bi bi-wifi',
                                        'kamar mandi' => 'bi bi-droplet',
                                        'ac' => 'bi bi-snow',
                                        'kipas' => 'bi bi-wind',
                                        'dapur' => 'bi bi-cup-straw',
                                        'listrik' => 'bi bi-lightning',
                                        'kasur' => 'bi bi-bed',
                                    ];
                                @endphp

                                @if (count($fasilitas) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($fasilitas as $f)
                                            @php
                                                $key = strtolower($f);
                                                $icon = $iconMap[$key] ?? 'bi bi-check-circle';
                                            @endphp

                                            <span
                                                class="badge bg-light text-dark border d-flex align-items-center gap-1 px-3 py-2">
                                                <i class="{{ $icon }}"></i>
                                                {{ $f }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">Tidak ada fasilitas</div>
                                @endif
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
