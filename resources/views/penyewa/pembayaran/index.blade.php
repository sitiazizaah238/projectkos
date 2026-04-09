@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @php
                    $userId = Auth::id();

                    $notifPengajuan = \App\Models\PengajuanSewa::where('user_id', $userId)
                        ->where('status', 'disetujui')
                        ->latest()
                        ->get();

                    $notifPengajuanUnread = \App\Models\PengajuanSewa::where('user_id', $userId)
                        ->where('status', 'disetujui')
                        ->where('status_notif', 0)
                        ->count();

                    $notifPembayaran = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                        ->whereIn('status', ['dikonfirmasi', 'ditolak'])
                        ->latest()
                        ->get();

                    $notifPembayaranUnread = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                        ->whereIn('status', ['dikonfirmasi', 'ditolak'])
                        ->where('status_notif', 0)
                        ->count();

                    $totalNotif = $notifPengajuanUnread + $notifPembayaranUnread;
                @endphp
                {{-- 🔔 NOTIFIKASI --}}
                <div class="dropdown position-relative">

                    <button class="btn text-white position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-4"></i>

                        @if ($totalNotif > 0)
                            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger" style="top:10px; font-size:10px;">
                                {{ $totalNotif }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow" style="width:300px">

                        <h6 class="dropdown-header">Notifikasi</h6>

                        {{-- NOTIF PENGAJUAN --}}
                        @foreach ($notifPengajuan as $p)
                            <a href="{{ route('penyewa.notif.pengajuan', $p->id) }}" class="dropdown-item small py-2">
                                <strong>Pengajuan Disetujui</strong><br>
                                Kos <strong>{{ $p->nama_kos }}</strong> telah disetujui Pemilik
                            </a>
                        @endforeach

                        {{-- NOTIF PEMBAYARAN --}}
                        @foreach ($notifPembayaran as $pb)
                            <a href="{{ route('penyewa.notif.pembayaran', $pb->id) }}" class="dropdown-item small py-2">
                                <strong>Status Pembayaran</strong><br>
                                Pembayaran kos <strong>{{ $pb->nama_kos }}</strong>
                                {{ $pb->status }}
                            </a>
                        @endforeach
                        {{-- TAMPILKAN JIKA SEMUA NOTIF KOSONG --}}
                        @if ($notifPengajuan->isEmpty() && $notifPembayaran->isEmpty())
                            <li class="dropdown-item text-muted small">
                                Tidak ada notifikasi
                            </li>
                        @endif

                    </ul>
                </div>
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

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1" style="font-size: 30px;">
                    Riwayat Pembayaran
                </h3>

                <small class="text-muted d-block mb-4">
                    Pembayaran Kos / Riwayat Kos
                </small>

                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between">
                        <span>
                            <i class="bi bi-credit-card"></i> Data Pembayaran
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Durasi</th>
                                    <th>Status Pengajuan</th>
                                    <th>Status Sewa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pembayaran as $item)
                                    @php
                                        $statusPengajuan = $item->pengajuan->statusSaatIni();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->pengajuan->kos->nama_kos }}</td>
                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                                        <td>Rp {{ number_format($item->pengajuan->total_bayar ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $item->pengajuan->durasi }} Bulan</td>

                                        <td>
                                            @if ($statusPengajuan == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($statusPengajuan == 'disetujui')
                                                <span class="badge bg-primary">Disetujui</span>
                                            @elseif($statusPengajuan == 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($statusPengajuan == 'jatuh_tempo')
                                                <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                            @elseif($statusPengajuan == 'selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                            @elseif($statusPengajuan == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($statusPengajuan == 'aktif' || $statusPengajuan == 'jatuh_tempo')
                                                <span class="badge bg-danger">Terisi</span>
                                            @elseif($statusPengajuan == 'selesai' || $statusPengajuan == 'disetujui')
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>

                                        {{-- STATUS --}}
                                        <td>
                                            @if ($item->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @elseif($item->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        <td>
                                            <button
                                                class="btn btn-sm
        {{ $item->status == 'ditolak' ? 'btn-warning' : 'btn-secondary' }}"
                                                @if ($item->status == 'ditolak') data-bs-toggle="modal"
            data-bs-target="#modalUlang{{ $item->id }}"
        @else
            disabled @endif>
                                                Ajukan Ulang
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- MODAL AJUKAN ULANG --}}
                                    @if ($item->status == 'ditolak')
                                        <div class="modal fade" id="modalUlang{{ $item->id }}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 p-4">

                                                    <h5 class="fw-bold mb-3">Ajukan Ulang Pembayaran</h5>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Alasan Penolakan</label>
                                                        <div class="alert alert-danger">
                                                            {{ $item->alasan }}
                                                        </div>
                                                    </div>

                                                    <form
                                                        action="{{ route('penyewa.pembayaran.ajukan-ulang', $item->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Upload Bukti Baru</label>
                                                            <input type="file" name="bukti" class="form-control"
                                                                required>
                                                        </div>

                                                        <div class="d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-danger"
                                                                data-bs-dismiss="modal">
                                                                Batal
                                                            </button>
                                                            <button class="btn btn-primary">
                                                                Kirim Ulang
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            Tidak ada riwayat pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
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
