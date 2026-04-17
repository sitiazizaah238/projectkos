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

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();
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
                            style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>
            </div>

            <div class="container-fluid p-4">

                <h1 class="fw-bold mb-1" style="font-size:32px;">Detail Kamar</h1>
                <small class="text-muted">Manajemen Kamar / Detail Kamar</small>

                <div class="row mt-4">

                    {{-- ================= LEFT (FIXED) ================= --}}
                    <div class="col-md-7">

                        <h1 class="fw-bold mb-4">
                            Kamar {{ $kamar->nama_kamar }}
                        </h1>

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
                            <div class="card shadow-sm border-0 mb-4" style="border-radius:15px; overflow:hidden;">

                                <div id="carouselFotoKamar" class="carousel slide" data-bs-ride="carousel">

                                    <div class="carousel-indicators">
                                        @foreach ($fotos as $index => $foto)
                                            <button type="button" data-bs-target="#carouselFotoKamar"
                                                data-bs-slide-to="{{ $index }}"
                                                class="{{ $index == 0 ? 'active' : '' }}">
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="carousel-inner">
                                        @foreach ($fotos as $index => $foto)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $foto) }}" class="d-block w-100"
                                                    style="
    max-height:420px;
    object-fit:contain;
    background:#f8f9fa;
">
                                            </div>
                                        @endforeach
                                    </div>

                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotoKamar"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>

                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselFotoKamar"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>

                                </div>

                            </div>
                            {{-- BUTTON KEMBALI (DI BAWAH CARD FOTO) --}}
                            <div class="mb-4">
                                <a href="{{ route('pemilik.kamar.index') }}"
                                    class="btn btn-outline-primary px-4 rounded-pill">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        @else
                            <p class="text-muted">Foto tidak tersedia</p>
                        @endif

                    </div>

                    {{-- ================= RIGHT (TIDAK DIUBAH) ================= --}}
                    <div class="col-md-5">
                        <div style="height:38px;"></div>
                        <div class="card shadow-sm border-0 h-100" style="border-radius:15px;">
                            <div class="card-body">

                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle"></i> Informasi Kos
                                </h6>
                                <hr>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Nama Kos</div>
                                    <div class="col-6 fw-semibold">{{ $kamar->kos->nama_kos }}</div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Lokasi Kos</div>
                                    <div class="col-6">{{ $kamar->kos->lokasi ?? '-' }}</div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Tipe Kos</div>
                                    <div class="col-6">{{ ucfirst($kamar->kos->tipe_kos) }}</div>
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
                                            <span class="text-success fw-semibold"> Tersedia</span>
                                        @else
                                            <span class="text-danger fw-semibold"> Terisi</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="text-muted mb-1">Deskripsi Kamar</div>
                                    <div>{{ $kamar->deskripsi }}</div>
                                </div>
                                {{-- FASILITAS DI DALAM INFO CARD --}}
                                @php
                                    $fasilitas = [];

                                    if (is_array($kamar->fasilitas)) {
                                        $fasilitas = $kamar->fasilitas;
                                    } elseif (is_string($kamar->fasilitas) && !empty($kamar->fasilitas)) {
                                        $fasilitas = [$kamar->fasilitas];
                                    }
                                @endphp

                                @if (!empty($fasilitas))
                                    <hr class="my-3">

                                    <div>
                                        <div class="text-muted mb-2">Fasilitas Kamar</div>

                                        <div class="row g-2">
                                            @foreach ($fasilitas as $item)
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center gap-2 border rounded-pill px-3 py-2"
                                                        style="font-size:14px;background:#f8f9fa;">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                        {{ $item }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
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

