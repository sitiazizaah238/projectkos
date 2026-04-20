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
            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Pengajuan Penyewa
                </h3>

                <small class="text-muted d-block mb-4">
                    Data Pengajuan / Data Sewa
                </small>

                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-receipt me-2"></i>
                            <span class="fw-semibold">Data Penyewa</span>
                        </div>

                        <form method="GET">
                            <div class="input-group" style="width:250px;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari penyewa / kos / kamar...">

                                <button class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

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
                                    <th>Tanggal Selesai</th>
                                    <th>Status Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pengajuan as $p)
                                    @php
                                        $statusSaatIni = $p->statusSaatIni();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->kos->nama_kos }}</td>
                                        <td>{{ $p->penyewa->name }}</td>
                                        <td>{{ $p->kamar->nama_kamar }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d-m-Y') }}</td>
                                        <td>
                                            @php
                                                $tanggalMulai = \Carbon\Carbon::parse($p->tanggal_mulai);

                                                // misal durasi 1 bulan (bisa kamu ubah nanti)
                                                $tanggalSelesai = $tanggalMulai->copy()->addMonths(1);
                                            @endphp

                                            {{ $tanggalSelesai->format('d-m-Y') }}
                                        </td>
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
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            @if (request('search'))
                                                Data tidak ditemukan
                                            @else
                                                Belum ada pengajuan kos
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        @if ($pengajuan->hasPages())
                            <div class="p-3 d-flex justify-content-end">
                                {{ $pengajuan->appends(request()->query())->links() }}
                            </div>
                        @endif
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

