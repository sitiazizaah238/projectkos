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

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->latest()->get();
    $notifPembayaran = Pembayaran::whereHas('pengajuan.kos', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->where('status', 'menunggu')
        ->latest()
        ->get();

    $notifTimeline = collect();

    foreach ($notifKos as $n) {
        $notifTimeline->push([
            'judul' => 'Pembaruan Verifikasi Kos',
            'pesan' => 'Terdapat pembaruan status untuk kos ' . ($n->nama_kos ?? '-') . '.',
            'url' => url('/notif/kos/' . $n->id),
            'is_unread' => (int) $n->is_read === 0,
            'created_at' => $n->updated_at ?? $n->created_at,
        ]);
    }

    foreach ($notifPengajuan as $p) {
        $notifTimeline->push([
            'judul' => 'Pengajuan Sewa Baru',
            'pesan' =>
                (optional($p->penyewa)->name ?? 'Penyewa') .
                ' mengajukan sewa untuk kos ' .
                (optional($p->kos)->nama_kos ?? '-') .
                '.',
            'url' => url('/notif/pengajuan/' . $p->id),
            'is_unread' => (int) $p->is_read === 0,
            'created_at' => $p->created_at,
        ]);
    }

    foreach ($notifPembayaran as $pb) {
        $notifTimeline->push([
            'judul' => 'Pembayaran Baru',
            'pesan' =>
                'Penyewa ' .
                (optional($pb->pengajuan->penyewa)->name ?? '-') .
                ' telah mengirim pembayaran untuk kos ' .
                (optional($pb->pengajuan->kos)->nama_kos ?? '-') .
                '.',
            'url' => route('pemilik.notif.pembayaran', $pb->id),
            'is_unread' => (int) $pb->is_read === 0,
            'created_at' => $pb->created_at,
        ]);
    }

    $notifTimeline = $notifTimeline->sortByDesc('created_at')->values();
    $jumlahNotif = $notifTimeline->where('is_unread', true)->count();
@endphp
@section('content')
    <div class="d-flex flex-column flex-md-row">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

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
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">
                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '{{ session('success') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    </script>
                @endif
                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Pembayaran Penyewa
                </h3>

                <small class="text-muted d-block mb-4">
                    Verifikasi/Pembayaran Sewa
                </small>
                <div class="d-md-none mb-3">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari penyewa / kamar...">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-receipt me-2"></i>
                            <span class="fw-semibold">Data Pembayaran</span>
                        </div>

                        {{-- SEARCH (SAMA KAYA KAMAR) --}}
                        <form method="GET" action="" class="ms-2 flex-grow-1 flex-md-grow-0 d-none d-md-block">
                            <div class="input-group w-100" style="max-width:250px;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari penyewa / kamar...">

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table table-bordered mb-0 text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penyewa</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Bukti</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pembayaran as $item)
                                    <tr class="align-middle">

                                        <td>{{ ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + $loop->iteration }}
                                        </td>

                                        <td>{{ optional($item->pengajuan->penyewa)->name ?? '-' }}</td>

                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>

                                        <td>Rp {{ number_format($item->nominal_tagihan ?? 0, 0, ',', '.') }}</td>

                                        <td>{{ $item->metode->nama_metode }}</td>

                                        <td>
                                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank"
                                                class="btn btn-sm btn-warning">
                                                Lihat
                                            </a>
                                        </td>

                                        <td>
                                            @if ($item->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                            @elseif($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap flex-md-nowrap justify-content-center gap-1">

                                                <form id="form-konfirmasi-{{ $item->id }}"
                                                    action="{{ route('pemilik.verifikasi.konfirmasi', $item->id) }}"
                                                    method="POST" class="flex-fill flex-md-grow-0">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary btn-konfirmasi w-100 w-md-auto"
                                                        data-id="{{ $item->id }}" data-status="{{ $item->status }}">
                                                        Konfirmasi
                                                    </button>
                                                </form>

                                                <form id="form-tolak-{{ $item->id }}"
                                                    action="{{ route('pemilik.verifikasi.tolak', $item->id) }}"
                                                    method="POST" class="flex-fill flex-md-grow-0">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-tolak w-100 w-md-auto"
                                                        data-id="{{ $item->id }}" data-status="{{ $item->status }}">
                                                        Tolak
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            Belum ada pembayaran masuk
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        <div class="p-3">
                            {{ $pembayaran->links() }}
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-konfirmasi').forEach(button => {
                button.addEventListener('click', function() {

                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status');

                    // 🚫 Kalau SUDAH DIKONFIRMASI
                    if (status === 'dikonfirmasi') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sudah Dikonfirmasi',
                            text: 'Pembayaran ini sudah dikonfirmasi sebelumnya.'
                        });
                        return;
                    }

                    // 🚫 Kalau DITOLAK
                    if (status === 'ditolak') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Ditolak',
                            text: 'Pembayaran ini sudah ditolak.'
                        });
                        return;
                    }

                    // ✅ Kalau MENUNGGU
                    Swal.fire({
                        title: 'Konfirmasi Pembayaran?',
                        text: "Pembayaran akan disetujui dan kamar otomatis terisi!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Konfirmasi!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-konfirmasi-' + id).submit();
                        }
                    });

                });
            });
            document.querySelectorAll('.btn-tolak').forEach(button => {
                button.addEventListener('click', function() {

                    let id = this.getAttribute('data-id');
                    let status = this.getAttribute('data-status');

                    // 🚫 Kalau SUDAH DIKONFIRMASI
                    if (status === 'dikonfirmasi') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sudah Dikonfirmasi',
                            text: 'Tidak bisa menolak pembayaran yang sudah dikonfirmasi.'
                        });
                        return;
                    }

                    // 🚫 Kalau SUDAH DITOLAK
                    if (status === 'ditolak') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sudah Ditolak',
                            text: 'Pembayaran ini sudah ditolak sebelumnya.'
                        });
                        return;
                    }

                    // ✅ Kalau MENUNGGU
                    Swal.fire({
                        title: 'Tolak Pembayaran',
                        input: 'textarea',
                        inputLabel: 'Masukkan alasan penolakan',
                        inputPlaceholder: 'Contoh: Nominal kurang / Bukti tidak jelas',
                        showCancelButton: true,
                        confirmButtonText: 'Tolak Pembayaran',
                        cancelButtonText: 'Batal',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Alasan wajib diisi!'
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let form = document.getElementById('form-tolak-' + id);

                            let input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "alasan";
                            input.value = result.value;

                            form.appendChild(input);

                            form.submit();
                        }
                    });

                });
            });
        });
    </script>
@endsection
