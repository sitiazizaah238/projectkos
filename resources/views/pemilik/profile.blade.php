@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
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

    $notifKos = Kos::where('user_id', $userId)
                ->where('status', 'disetujui')
                ->where('is_read', false)
                ->latest()
                ->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)
                        ->where('is_read', false)
                        ->latest()
                        ->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();
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

                        {{-- FOTO --}}
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="photo" class="form-control">
                        </div>

                        {{-- NAMA + EMAIL --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-control">
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
@endsection
