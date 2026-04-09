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
                        {{-- NOTIF PEMBAYARAN --}}
                        @foreach ($notifPembayaran as $pb)
                            <a href="{{ url('/pemilik/verifikasi') }}" class="dropdown-item small py-2">
                                <strong>Pembayaran Baru</strong><br>
                                Penyewa <strong>{{ optional($pb->pengajuan->penyewa)->name }}</strong>
                                mengirim pembayaran kos <strong>{{ $pb->pengajuan->kos->nama_kos }}</strong>
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
            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Pengajuan Penyewa
                </h3>

                <small class="text-muted d-block mb-4">
                    Data Pengajuan / Data Sewa
                </small>
                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white d-flex align-items-center">
                        <i class="bi bi-receipt me-2"></i>
                        <span class="fw-semibold">Data Penyewa</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Penyewa</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pengajuan as $p)
                                    @php
                                        $statusSaatIni = $p->statusSaatIni();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->kos->nama_kos }}</td>
                                        <td>{{ $p->penyewa->name }}</td>
                                        <td>{{ $p->kamar->nama_kamar }}</td>
                                        <td>{{ $p->tanggal_mulai }}</td>
                                        <td>
                                            @if ($statusSaatIni == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif ($statusSaatIni == 'disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif ($statusSaatIni == 'aktif')
                                                <span class="badge bg-primary">Aktif</span>
                                            @elseif ($statusSaatIni == 'jatuh_tempo')
                                                <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                            @elseif ($statusSaatIni == 'selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                            @elseif ($statusSaatIni == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pemilik.pengajuan.show', $p->id) }}"
                                                class="btn btn-sm btn-warning text-white">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    @if ($pengajuan->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                            <span class="text-muted fw-semibold">
                                Belum ada pengajuan kos
                            </span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
