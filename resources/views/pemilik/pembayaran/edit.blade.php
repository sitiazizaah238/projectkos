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
    // 🔔 NOTIF SECTION
    // =====================

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
                                <strong>Pembaruan Verifikasi Kos</strong><br>
                                Terdapat pembaruan status untuk kos <strong>{{ $n->nama_kos }}</strong>.
                            </a>
                        @endforeach

                        {{-- NOTIF PENGAJUAN --}}
                        @foreach ($notifPengajuan as $p)
                            <a href="{{ url('/notif/pengajuan/' . $p->id) }}" class="dropdown-item small py-2">
                                <strong>Pengajuan Sewa Baru</strong><br>
                                {{ optional($p->penyewa)->name ?? 'Penyewa' }} mengajukan sewa untuk kos <strong>{{ optional($p->kos)->nama_kos ?? '-' }}</strong>.
                            </a>
                        @endforeach
                        {{-- NOTIF PEMBAYARAN --}}
                        @foreach ($notifPembayaran as $pb)
                            <a href="{{ url('/pemilik/verifikasi') }}" class="dropdown-item small py-2">
                                <strong>Pembayaran Baru</strong><br>
                                Penyewa <strong>{{ optional($pb->pengajuan->penyewa)->name }}</strong>
                                telah mengirim pembayaran untuk kos <strong>{{ $pb->pengajuan->kos->nama_kos }}</strong>.
                            </a>
                        @endforeach
                        @if ($jumlahNotif == 0)
                            <div class="text-center text-muted small p-3">
                                Belum ada notifikasi baru
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
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-4">Edit Metode</h3>

                <div class="card shadow-sm rounded-4 p-4">

                    <form action="{{ route('pemilik.pembayaran.update', $metode->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Nama Metode / Bank</label>
                            <input type="text" name="nama_metode" class="form-control"
                                value="{{ $metode->nama_metode }}">
                        </div>

                        <div class="mb-3">
                            <label>Atas Nama</label>
                            <input type="text" name="atas_nama" class="form-control" value="{{ $metode->atas_nama }}">
                        </div>

                        <div class="mb-3">
                            <label>No Rekening</label>
                            <input type="text" name="no_rekening" class="form-control"
                                value="{{ $metode->no_rekening }}">
                        </div>

                        <div class="mb-3">
                            <label>Gambar</label>
                            <input type="file" name="gambar" class="form-control">

                            @if ($metode->gambar)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metode->gambar) }}" width="100">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" {{ $metode->status == 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="nonaktif" {{ $metode->status == 'nonaktif' ? 'selected' : '' }}>
                                    Non-Aktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pemilik.pembayaran.index') }}" class="btn btn-danger">
                                Batal
                            </a>

                            <button class="btn btn-primary">
                                Simpan
                            </button>
                        </div>

                    </form>

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
