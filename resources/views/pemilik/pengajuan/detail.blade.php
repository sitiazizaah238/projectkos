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

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-4">Detail Pengajuan Penyewa</h3>

                @php
                    $harga = $pengajuan->kamar->harga;
                    $durasi = $pengajuan->durasi;
                    $total = $harga * $durasi;
                @endphp

                {{-- ================= INFORMASI PENYEWA ================= --}}
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-person-circle"></i> Informasi Penyewa
                    </h6>

                    <div class="row">
                        <div class="col-md-4">
                            <small>Nama</small>
                            <div class="fw-semibold">{{ $pengajuan->penyewa->name }}</div>
                        </div>

                        <div class="col-md-4">
                            <small>Email</small>
                            <div class="fw-semibold">{{ $pengajuan->penyewa->email }}</div>
                        </div>

                        <div class="col-md-4">
                            <small>Status</small>
                            @php
                                $statusSaatIni = $pengajuan->statusSaatIni();
                                $statusClass = match ($statusSaatIni) {
                                    'aktif' => 'text-success',
                                    'jatuh_tempo' => 'text-warning',
                                    'selesai' => 'text-secondary',
                                    'ditolak' => 'text-danger',
                                    default => 'text-warning',
                                };
                                $statusLabel = ucwords(str_replace('_', ' ', $statusSaatIni));
                            @endphp
                            <div class="fw-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </div>
                        </div>
                    </div>

                    @if ($pengajuan->status == 'ditolak')
                        <div class="alert alert-danger mt-3 rounded-3">
                            <strong>Alasan Penolakan :</strong><br>
                            {{ $pengajuan->alasan }}
                        </div>
                    @endif
                </div>


                {{-- ================= INFORMASI KOS ================= --}}
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-house"></i> Informasi Kos
                    </h6>

                    <div class="row">
                        <div class="col-md-4">
                            <small>Nama Kos</small>
                            <div class="fw-semibold">{{ $pengajuan->kos->nama_kos }}</div>
                        </div>

                        <div class="col-md-4">
                            <small>Lokasi</small>
                            <div class="fw-semibold">{{ $pengajuan->kos->lokasi }}</div>
                        </div>

                        <div class="col-md-4">
                            <small>Tipe Kos</small>
                            <div class="fw-semibold">{{ $pengajuan->kos->tipe_kos }}</div>
                        </div>
                    </div>
                </div>


                {{-- ================= INFORMASI KAMAR ================= --}}
                <div class="card shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-door-open"></i> Informasi Kamar & Sewa
                    </h6>

                    <div class="row">
                        <div class="col-md-3">
                            <small>Nama Kamar</small>
                            <div class="fw-semibold">{{ $pengajuan->kamar->nama_kamar }}</div>
                        </div>

                        <div class="col-md-3">
                            <small>Harga / Bulan</small>
                            <div class="fw-semibold">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <small>Durasi</small>
                            <div class="fw-semibold">
                                {{ $durasi }} Bulan
                            </div>
                        </div>

                        <div class="col-md-3">
                            <small>Tanggal Mulai</small>
                            <div class="fw-semibold">
                              {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d-m-Y') }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <small>Tanggal Selesai</small>
                            <div class="fw-semibold">
                                {{ $pengajuan->tanggalSelesai()->format('d-m-Y') }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-end">
                        <small>Total Bayar</small>
                        <div class="fw-bold fs-5 text-primary">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>


                {{-- ================= BUTTON ================= --}}
                <div class="mt-4 d-flex justify-content-end gap-2">

                    <a href="{{ route('pemilik.pengajuan.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>

                    @if ($pengajuan->status == 'menunggu')
                        <button onclick="confirmReject()" class="btn btn-danger">
                            Tolak
                        </button>

                        <button onclick="confirmApprove()" class="btn btn-primary">
                            Setujui
                        </button>

                        <form id="approveForm" action="{{ route('pemilik.pengajuan.approve', $pengajuan->id) }}"
                            method="POST">
                            @csrf
                        </form>

                        <form id="rejectForm" action="{{ route('pemilik.pengajuan.reject', $pengajuan->id) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="alasan" id="alasanInput">
                        </form>
                    @endif
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
    {{-- ================= SWEET ALERT ================= --}}
    <script>
        function confirmApprove() {
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: "Total pembayaran akan otomatis dibuat.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approveForm').submit();
                }
            })
        }

        function confirmReject() {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Masukkan alasan...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan wajib diisi!'
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('alasanInput').value = result.value;
                    document.getElementById('rejectForm').submit();
                }
            })
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#0d6efd'
            });
        </script>
    @endif
@endsection

