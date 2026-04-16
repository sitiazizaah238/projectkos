@extends('layouts.app')
@if (request('read'))
    @php
        DB::table('pengajuan_sewas')
            ->where('id', request('read'))
            ->update(['status_notif' => 1]);
    @endphp
@endif
@section('content')
    <style>
        .payment-method-option {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .payment-method-option:hover {
            border-color: #0d6efd;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.12);
        }

        .payment-method-radio {
            width: 18px;
            height: 18px;
            margin-top: 2px;
        }

        .payment-method-option:has(input:checked) {
            border-color: #0d6efd;
            background: #eef5ff;
        }

        .btn-alasan-detail {
            background: #ffe3e3;
            border: 1px solid #dc3545;
            color: #b02a37;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-alasan-detail:hover {
            background: #dc3545;
            border: 2px solid #b02a37;
            color: #fff;
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.28);
            transform: translateY(-1px);
        }
    </style>
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
                            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
                                style="top:10px; font-size:10px;">
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

                <h3 class="fw-bold mb-1" style="font-size: 30px;">Data Pengajuan Sewa</h3>
                <small class="text-muted d-block mb-4">
                    Pengajuan Kos/ Data Pengajuan
                </small>
                {{-- SEARCH --}}
                <div class="d-flex justify-content-end mb-3 mt-2">
                    <form method="GET">
                        <div class="input-group" style="width:300px;">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control rounded-start-pill" placeholder="Cari nama kos / kamar...">

                            <button type="submit" class="btn btn-primary rounded-end-pill">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-clipboard-data"></i> Data Pengajuan
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">

                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Durasi Sewa</th>
                                    <th>Status Pengajuan</th>
                                    <th>Status Kamar</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @if ($pengajuan->count() > 0)
                                    @foreach ($pengajuan as $item)
                                        @php
                                            $statusSaatIni = $item->statusSaatIni();
                                        @endphp
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $item->kos->nama_kos ?? '-' }}</td>

                                            <td>{{ $item->kamar->nama_kamar ?? '-' }}</td>

                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}</td>

                                            {{-- TAMBAHAN TANGGAL SELESAI --}}
                                            <td>
                                                @php
                                                    $tanggalMulai = \Carbon\Carbon::parse($item->tanggal_mulai);
                                                    $tanggalSelesai = $tanggalMulai->copy()->addMonths($item->durasi);
                                                @endphp

                                                {{ $tanggalSelesai->format('Y-m-d') }}
                                            </td>

                                            <td>{{ $item->durasi }} Bulan</td>

                                            {{-- STATUS --}}
                                            <td>
                                                @if ($statusSaatIni == 'menunggu')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @elseif($statusSaatIni == 'disetujui')
                                                    <span class="badge bg-primary">Disetujui</span>
                                                @elseif($statusSaatIni == 'aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif($statusSaatIni == 'jatuh_tempo')
                                                    <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                                @elseif($statusSaatIni == 'selesai')
                                                    <span class="badge bg-secondary">Selesai</span>
                                                @elseif($statusSaatIni == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            {{-- STATUS KAMAR --}}
                                            <td>
                                                @if ($statusSaatIni === 'aktif' || $statusSaatIni === 'jatuh_tempo')
                                                    <span class="badge bg-danger">Terisi</span>
                                                @elseif($statusSaatIni === 'selesai')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-success">Tersedia</span>
                                                @endif
                                            </td>

                                            {{-- ALASAN --}}
                                            <td>
                                                @if ($item->status == 'ditolak')
                                                    <button type="button" class="btn btn-sm btn-alasan-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalAlasanPenolakan"
                                                        data-alasan="{{ $item->alasan ?: '-' }}"
                                                        data-kos="{{ $item->kos->nama_kos ?? '-' }}"
                                                        data-kamar="{{ $item->kamar->nama_kamar ?? '-' }}">
                                                        Detail
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- AKSI --}}
                                            <td>
                                                @php
                                                    $pembayaranTerakhir = DB::table('pembayarans')
                                                        ->where('pengajuan_sewa_id', $item->id)
                                                        ->latest()
                                                        ->first();

                                                    $pembayaranMenunggu = DB::table('pembayarans')
                                                        ->where('pengajuan_sewa_id', $item->id)
                                                        ->where('status', 'menunggu')
                                                        ->exists();
                                                @endphp

                                                @if (
                                                    ($statusSaatIni == 'disetujui' && !$pembayaranTerakhir) ||
                                                        ($statusSaatIni == 'jatuh_tempo' && !$pembayaranMenunggu))
                                                    {{-- Bisa bayar --}}
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#modalBayar{{ $item->id }}">
                                                        Bayar Sekarang
                                                    </button>
                                                @elseif($statusSaatIni == 'jatuh_tempo' && $pembayaranMenunggu)
                                                    {{-- TAMBAHAN: tetap bisa diklik --}}
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#modalSudahBayar">
                                                        Menunggu Verifikasi
                                                    </button>
                                                @else
                                                    {{-- TAMBAHAN: tetap bisa diklik --}}
                                                    <button class="btn btn-sm btn-info text-white fw-semibold"
                                                        data-bs-toggle="modal" data-bs-target="#modalSudahBayar"
                                                        style="cursor:pointer;">
                                                        Bayar Sekarang
                                                    </button>
                                                @endif
                                            </td>

                                        </tr>
                                        {{-- ================= MODAL PEMBAYARAN ================= --}}
                                        <div class="modal fade" id="modalBayar{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 p-4">

                                                    <h5 class="fw-bold mb-3">Pembayaran Sewa Kamar</h5>

                                                    {{-- Informasi Sewa --}}
                                                    <div class="border rounded-4 p-3 mb-3">
                                                        <div class="fw-semibold mb-2">Informasi Sewa</div>

                                                        <div class="row text-center">
                                                            <div class="col">
                                                                <small>Nama Kos</small>
                                                                <div>{{ $item->kos->nama_kos }}</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Durasi Sewa</small>
                                                                <div>{{ $item->durasi }} Bulan</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Nama Kamar</small>
                                                                <div>{{ $item->kamar->nama_kamar }}</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Total Bayar</small>
                                                                <div class="fw-bold text-success">
                                                                    Rp
                                                                    {{ number_format($item->kamar->harga, 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if ($statusSaatIni == 'jatuh_tempo')
                                                            <div class="mt-2 alert alert-warning py-2 small mb-0">
                                                                Tagihan bulanan jatuh tempo. Silakan bayar untuk
                                                                mengaktifkan status sewa kembali.
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Form Pembayaran --}}
                                                    <form action="{{ route('penyewa.bayar', $item->id) }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf

                                                        {{-- Pilih Metode --}}
                                                        <div class="mb-3">
                                                            <label class="fw-semibold mb-2">Pilih Metode Pembayaran</label>

                                                            @forelse($item->kos->user->metodePembayaran ?? [] as $metode)
                                                                <label
                                                                    class="payment-method-option rounded-3 p-2 mb-2 d-flex justify-content-between align-items-center w-100">
                                                                    <div class="d-flex gap-2 align-items-start">
                                                                        <input class="form-check-input payment-method-radio"
                                                                            type="radio" name="metode_id"
                                                                            value="{{ $metode->id }}" required>
                                                                        <div>
                                                                            <div class="fw-semibold">
                                                                                {{ $metode->nama_metode }}
                                                                            </div>
                                                                            <small>{{ $metode->no_rekening }}</small>
                                                                        </div>
                                                                    </div>

                                                                    @if ($metode->gambar)
                                                                        <img src="{{ asset('storage/' . $metode->gambar) }}"
                                                                            width="70">
                                                                    @endif
                                                                </label>
                                                            @empty
                                                                <div class="text-muted small">
                                                                    Metode pembayaran belum tersedia
                                                                </div>
                                                            @endforelse
                                                            {{-- Upload Bukti --}}
                                                            <div class="mb-3">
                                                                <label class="fw-semibold">Upload Bukti Transfer</label>
                                                                <input type="file" name="bukti" class="form-control"
                                                                    required>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>

                                                                <button class="btn btn-primary">
                                                                    Bayar
                                                                </button>
                                                            </div>

                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center text-muted">
                                                <i class="bi bi-info-circle fs-1 mb-2"></i>
                                                <span class="fw-semibold">
                                                    Silakan mengajukan kos terlebih dahulu
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL ALASAN PENOLAKAN ================= --}}
    <div class="modal fade" id="modalAlasanPenolakan" tabindex="-1" aria-labelledby="modalAlasanPenolakanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAlasanPenolakanLabel">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="small text-muted mb-2" id="alasanMetaInfo"></div>
                    <div id="alasanPenolakanText">-</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alasanModal = document.getElementById('modalAlasanPenolakan');
            if (!alasanModal) return;
            let lastTrigger = null;

            alasanModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                lastTrigger = trigger || null;
                if (!trigger) return;

                const alasan = trigger.getAttribute('data-alasan') || '-';
                const kos = trigger.getAttribute('data-kos') || '-';
                const kamar = trigger.getAttribute('data-kamar') || '-';

                const alasanText = alasanModal.querySelector('#alasanPenolakanText');
                const metaInfo = alasanModal.querySelector('#alasanMetaInfo');

                if (alasanText) alasanText.textContent = alasan;
                if (metaInfo) metaInfo.textContent = `Kos: ${kos} | Kamar: ${kamar}`;
            });

            alasanModal.addEventListener('hide.bs.modal', function() {
                const activeElement = document.activeElement;

                // Pindahkan fokus keluar modal sebelum Bootstrap memberi aria-hidden=true.
                if (activeElement && alasanModal.contains(activeElement)) {
                    activeElement.blur();
                }
            });

            alasanModal.addEventListener('hidden.bs.modal', function() {
                if (lastTrigger && typeof lastTrigger.focus === 'function' && document.contains(
                        lastTrigger)) {
                    lastTrigger.focus();
                } else {
                    document.body.focus();
                }
            });
        });
    </script>

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
  <div class="modal fade" id="modalSudahBayar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 p-4 text-center shadow">

            {{-- ICON CEKLIS --}}
            <div class="mb-3">
                <i class="bi bi-check-circle-fill text-success"
                    style="font-size: 60px;"></i>
            </div>

            {{-- JUDUL --}}
            <h5 class="fw-bold text-success mb-2">
                Pembayaran Berhasil
            </h5>

            {{-- DESKRIPSI --}}
            <p class="mb-2">
                Anda sudah melakukan pembayaran untuk sewa ini.
            </p>

            <small class="text-muted">
                Silakan tunggu proses verifikasi dari pemilik kos.
            </small>

            {{-- BUTTON --}}
            <div class="mt-4 d-flex justify-content-center gap-2">

                {{-- KE RIWAYAT --}}
                <a href="{{ route('penyewa.riwayat.pembayaran') }}"
                    class="btn btn-success px-3">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>

                {{-- TUTUP --}}
                <button class="btn btn-outline-secondary px-3"
                    data-bs-dismiss="modal">
                    Tutup
                </button>

            </div>

        </div>
    </div>
</div>
@endsection
