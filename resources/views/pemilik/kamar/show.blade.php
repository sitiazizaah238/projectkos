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

            <div class="container-fluid p-4">

                <h1 class="fw-bold mb-1" style="font-size:32px;">
                    Detail Kamar
                </h1>
                <small class="text-muted">Manajemen Kamar / Detail Kamar</small>

                <div class="row mt-4">

                    {{-- LEFT --}}
                    <div class="col-md-7">

                        <h1 class="fw-bold mb-4">
                            Kamar {{ $kamar->nama_kamar }}
                        </h1>

                        {{-- FOTO KAMAR --}}
                        @php
                            $fotos = [];

                            if (!empty($kamar->foto)) {
                                if (is_array($kamar->foto)) {
                                    $fotos = $kamar->foto;
                                } elseif (is_string($kamar->foto)) {
                                    $decoded = json_decode($kamar->foto, true);
                                    $fotos = json_last_error() === JSON_ERROR_NONE ? $decoded : [$kamar->foto];
                                }
                            }
                        @endphp

                        @if (!empty($fotos))
                            <div class="row g-3 mb-4">
                                @foreach ($fotos as $foto)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="card border-0 shadow-sm h-100"
                                            style="border-radius:15px; overflow:hidden;">
                                            <img src="{{ asset('storage/' . $foto) }}" class="w-100"
                                                style="height:250px; object-fit:cover;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Foto tidak tersedia</p>
                        @endif

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-md-5">
                        {{-- Spacer biar sejajar dengan judul kiri --}}
                        <div style="height:38px;"></div>
                        <div class="card shadow-sm border-0" style="border-radius:15px;">
                            <div class="card-body">

                                <h6 class="fw-bold mb-3">ℹ️ Informasi Kos</h6>
                                <hr>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Nama Kos</div>
                                    <div class="col-6 fw-semibold">
                                        {{ $kamar->kos->nama_kos }}
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Lokasi Kos</div>
                                    <div class="col-6">
                                        {{ $kamar->kos->alamat }}
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Tipe Kos</div>
                                    <div class="col-6">
                                        {{ ucfirst($kamar->kos->tipe_kos) }}
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Harga</div>
                                    <div class="col-6 fw-bold">
                                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Tipe Harga</div>
                                    <div class="col-6">
                                        {{ $kamar->tipe_harga == 'bulanan' ? 'Per-Bulan' : 'Per-Tahun' }}
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Status Kamar</div>
                                    <div class="col-6">
                                        @if ($kamar->status === 'tersedia')
                                            <span class="text-success fw-semibold">● Tersedia</span>
                                        @else
                                            <span class="text-danger fw-semibold">● Terisi</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="text-muted mb-1">Deskripsi Kamar</div>
                                    <div>{{ $kamar->deskripsi }}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- FASILITAS SAFE VERSION --}}
                @php
                    $fasilitas = [];

                    if (is_array($kamar->fasilitas)) {
                        $fasilitas = $kamar->fasilitas;
                    } elseif (is_string($kamar->fasilitas) && !empty($kamar->fasilitas)) {
                        $fasilitas = [$kamar->fasilitas];
                    }
                @endphp

                @if (!empty($fasilitas))
                    <div class="card mt-4 shadow-sm border-0" style="border-radius:15px;">
                        <div class="card-body">

                            <h6 class="fw-bold mb-3">ℹ️ Fasilitas Kamar</h6>
                            <hr>

                            <div class="row">
                                @foreach ($fasilitas as $item)
                                    <div class="col-md-4 mb-2">
                                        ✔ {{ $item }}
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endif
                <div class="text-end mt-3">
                    <a href="{{ route('pemilik.kamar.index') }}" class="btn btn-primary">
                        ← Kembali
                    </a>
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
