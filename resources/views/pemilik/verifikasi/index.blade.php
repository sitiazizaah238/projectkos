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

                    <div class="dropdown-menu dropdown-menu-end p-2"
                        style="width:320px; max-height:300px; overflow-y:auto;">

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
                                {{ optional($p->penyewa)->name ?? 'Penyewa' }} mengajukan sewa untuk kos
                                <strong>{{ optional($p->kos)->nama_kos ?? '-' }}</strong>.
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

                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white d-flex align-items-center">
                        <i class="bi bi-receipt me-2"></i>
                        <span class="fw-semibold">Data Pembayaran</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penyewa</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pembayaran as $item)
                                    <tr class="align-middle">

                                        <td>{{ $loop->iteration }}</td>

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
                                                <span class="badge bg-warning">Menunggu Verifikasi</span>
                                            @elseif($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>

                                        <td>
                                            <form id="form-konfirmasi-{{ $item->id }}"
                                                action="{{ route('pemilik.verifikasi.konfirmasi', $item->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm {{ $item->status == 'menunggu' ? 'btn-primary' : 'btn-secondary' }} btn-konfirmasi"
                                                    data-id="{{ $item->id }}"
                                                    {{ $item->status != 'menunggu' ? 'disabled' : '' }}>
                                                    Konfirmasi
                                                </button>
                                            </form>

                                            <form id="form-tolak-{{ $item->id }}"
                                                action="{{ route('pemilik.verifikasi.tolak', $item->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm {{ $item->status == 'menunggu' ? 'btn-danger' : 'btn-secondary' }} btn-tolak"
                                                    data-id="{{ $item->id }}"
                                                    {{ $item->status != 'menunggu' ? 'disabled' : '' }}>
                                                    Tolak
                                                </button>
                                            </form>
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

                    Swal.fire({
                        title: 'Tolak Pembayaran',
                        input: 'textarea',
                        inputLabel: 'Masukkan alasan penolakan',
                        inputPlaceholder: 'Contoh: Nominal pembayaran kurang / Bukti transfer tidak jelas / Data tidak valid',
                        inputAttributes: {
                            'aria-label': 'Masukkan alasan'
                        },
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

                            // buat input hidden untuk alasan
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
