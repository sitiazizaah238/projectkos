@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-penyewa')
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

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold" style="font-size:30px;">
                    Profile Penyewa
                </h3>
                <small class="text-muted">
                    Manajemen Akun / Profile Penyewa
                </small>

                {{-- CARD --}}
                <div class="card mt-4 shadow-sm border-0 rounded-3">
                    <div class="card-body">

                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2 text-primary"></i>
                            Edit Profile
                        </h6>
                        <hr class="mb-4">
                        <form action="{{ route('penyewa.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                {{-- FOTO --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Foto Profil</label>
                                    <input type="file" name="foto" class="form-control">
                                </div>

                                {{-- NAMA --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}"
                                        class="form-control">
                                </div>

                            </div>

                            {{-- NOMOR HP + EMAIL --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="text" name="no_hp" value="{{ auth()->user()->no_hp }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ auth()->user()->email }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- UBAH PASSWORD --}}
                            <div class="mt-4">

                                <h6 class="fw-bold mb-3">Ubah Password</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="password" name="password" placeholder="Password" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                            class="form-control">
                                    </div>
                                </div>

                                <small class="text-muted">
                                    Tidak perlu diisi jika tidak ingin mengubah password
                                </small>

                            </div>
                            {{-- BUTTON --}}
                            <div class="text-end mt-4">
                                <a href="{{ route('penyewa.dashboard') }}" class="btn btn-danger">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= PROFILE MODAL (SAMA KAYA DASHBOARD) ================= --}}
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
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#0d6efd'
        });
    </script>
@endsection
