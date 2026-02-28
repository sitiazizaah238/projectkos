@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $userId = Auth::id();

    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $totalKos = Kos::where('user_id', $userId)->count();
    $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
    $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
    $totalPenyewa = PengajuanSewa::whereIn('kos_id', $kosIds)->where('status', 'aktif')->count();

    // =====================
    // 🔔 NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();
@endphp

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                <div class="dropdown position-relative">

                    <button class="btn text-white position-relative" data-bs-toggle="dropdown">

                        <i class="bi bi-bell fs-4"></i>

                        @if ($jumlahNotif > 0)
                            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
                                style="top:10px; font-size:10px;">
                                {{ $jumlahNotif }}
                            </span>
                        @endif

                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-2" style="width:320px; max-height:300px; overflow-y:auto;">

                        <h6 class="dropdown-header">Notifikasi</h6>

                        {{-- NOTIF KOS DISETUJUI --}}
                        @foreach ($notifKos as $n)
                            <a href="{{ url('/notif/kos/' . $n->id) }}" class="dropdown-item small py-2">
                                <strong>Kos Disetujui</strong><br>
                                Kos <strong>{{ $n->nama_kos }}</strong> telah disetujui admin
                            </a>
                        @endforeach

                        {{-- NOTIF PENGAJUAN --}}
                        @foreach ($notifPengajuan as $p)
                            <a href="{{ url('/notif/pengajuan/' . $p->id) }}" class="dropdown-item small py-2">
                                <strong>{{ $p->nama_penyewa }}</strong><br>
                                Mengajukan kos <strong>{{ $p->nama_kos }}</strong>
                            </a>
                        @endforeach

                        @if ($jumlahNotif == 0)
                            <div class="text-center text-muted small p-3">
                                Tidak ada notifikasi
                            </div>
                        @endif

                    </div>
                </div>
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
@if($kos->foto && count($kos->foto) > 0)

<div id="carouselKos" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-inner">

        @foreach($kos->foto as $key => $f)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/'.$f) }}"
                     class="d-block w-100 rounded-4 shadow"
                     style="height:400px; object-fit:cover;">
            </div>
        @endforeach

    </div>

    <button class="carousel-control-prev" type="button"
        data-bs-target="#carouselKos" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button"
        data-bs-target="#carouselKos" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>

@else
    <div class="bg-light text-center p-5 rounded">
        Tidak ada foto
    </div>
@endif

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
