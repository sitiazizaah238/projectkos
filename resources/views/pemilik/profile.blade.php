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
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">
            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.chat-icon-pemilik')
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
            <div class="p-4">

                <h3 class="fw-bold">Profile Pemilik</h3>
                <small class="text-muted">Manajemen Akun / Profile Pemilik</small>



                <div class="card mt-4 shadow-sm p-4" style="border-radius:12px">

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-person-badge"></i>
                        Edit Profile Pemilik
                    </h6>

                    <form action="{{ route('pemilik.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- FOTO + NO HP --}}
                        <div class="row align-items-start g-4">
                            {{-- FOTO --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Foto Profil
                                </label>

                                <div class="d-flex align-items-center gap-3 flex-wrap mb-3">

                                    @if (Auth::user()->photo)
                                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                                            class="foto-preview">

                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#hapusFotoModal">

                                            <i class="bi bi-trash"></i>
                                            Hapus Foto
                                        </button>
                                    @else
                                        <div class="text-muted small">
                                            Belum ada foto
                                        </div>
                                    @endif

                                </div>

                                {{-- INPUT FOTO --}}
                                <input type="file" name="photo" class="form-control" accept="image/*"
                                    onchange="previewPhoto(event)">

                            </div>

                            {{-- NOMOR HP --}}
                            <div class="col-md-6">

                                <div class="nohp-wrapper">

                                    <label class="form-label fw-semibold">
                                        Nomor HP
                                    </label>

                                    <input type="text" name="no_hp" value="{{ Auth::user()->no_hp }}"
                                        class="form-control" placeholder="08xxxxxxxxxx">

                                </div>

                            </div>
                            {{-- NAMA + EMAIL --}}
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label>Nama</label>
                                    <input type="text" name="name" value="{{ Auth::user()->name }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ Auth::user()->email }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- PASSWORD --}}
                            <div class="border rounded p-3 mt-3">
                                <h6 class="fw-bold">Ubah Password</h6>
                                <small class="text-muted">
                                    Tidak perlu diisi jika tidak ingin mengubah password
                                </small>

                                <div class="row mt-3">

                                    {{-- PASSWORD BARU --}}
                                    <div class="col-md-6">
                                        <label>Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" class="form-control">

                                            <span class="input-group-text" style="cursor:pointer"
                                                onclick="togglePassword('password', this)">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- CONFIRM PASSWORD --}}
                                    <div class="col-md-6">
                                        <label>Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="confirmPassword"
                                                class="form-control">

                                            <span class="input-group-text" style="cursor:pointer"
                                                onclick="togglePassword('confirmPassword', this)">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>

                                </div>


                                {{-- BUTTON --}}
                                <div class="mt-4 d-flex justify-content-end gap-2">
                                    <a href="{{ route('pemilik.dashboard') }}" class="btn btn-danger">
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
    <script>
        function togglePassword(id, el) {
            let input = document.getElementById(id);
            let icon = el.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("bi-eye-slash", "bi-eye");
            }
        }
    </script>
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
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#0d6efd'
        });
    </script>
    <style>
        .foto-preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0d6efd;
            flex-shrink: 0;
        }

        .card form label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .card form .form-control {
            height: 48px;
            border-radius: 10px;
        }

        .input-group .form-control {
            height: 48px;
        }

        .input-group-text {
            border-radius: 0 10px 10px 0;
        }

        .row.g-4 {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 1rem;
        }

        .nohp-wrapper {
            margin-top: 18px;
        }
    </style>
    {{-- MODAL HAPUS FOTO --}}
    <div class="modal fade" id="hapusFotoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Hapus Foto
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Yakin ingin menghapus foto profile?
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <form action="{{ route('pemilik.profile.deletePhoto') }}" method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            Ya, Hapus
                        </button>

                    </form>

                </div>

            </div>
        </div>
    </div>
    <script>
        function previewPhoto(event) {

            const image = document.querySelector('.foto-preview');

            if (image) {
                image.src = URL.createObjectURL(event.target.files[0]);
            }

        }
    </script>
@endsection
