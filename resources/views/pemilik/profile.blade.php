@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">
           {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
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

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif

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
@endsection
