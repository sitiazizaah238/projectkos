@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        {{-- MAIN --}}
        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-between align-items-center px-4 w-100">

                {{-- KIRI (BIAR STABIL, WALAU KOSONG) --}}
                <div></div>

                {{-- KANAN (PROFILE) --}}
                <div class="d-flex align-items-center">
                    <button type="button" class="btn text-white d-flex align-items-center gap-3">

                        <span class="fw-semibold small">
                            {{ Auth::user()->name }}
                        </span>

                        @if (Auth::user()->photo)
                            <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                                style="
                        width:35px;
                        height:35px;
                        border-radius:50%;
                        object-fit:cover;
                    ">
                        @else
                            <i class="bi bi-person-circle fs-3"></i>
                        @endif

                    </button>
                </div>

            </div>
            {{-- CONTENT --}}
            <div class="p-4">

                <h3 class="fw-bold mb-1">Profile ADMIN</h3>
                <small class="text-muted">
                    Manajemen Akun / <span class="text-primary">Profile Admin</span>
                </small>

                <div class="card mt-4 p-4 shadow-sm profile-card">

                    <h6 class="fw-bold mb-4">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Edit Profile Admin
                    </h6>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- FOTO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Profil</label>
                            <input type="file" name="photo" class="form-control">
                        </div>

                        {{-- NAMA & EMAIL --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama</label>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                    class="form-control" required>
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="card p-3 bg-light border-0 mb-3 rounded-4">
                            <h6 class="fw-bold">Ubah Password</h6>

                            <div class="row mt-2">

                                {{-- PASSWORD --}}
                                <div class="col-md-6 mb-3">
                                    <label>Password</label>

                                    <div class="input-group">
                                        <input type="password" name="password" id="adminPassword" class="form-control">

                                        <span class="input-group-text" style="cursor:pointer"
                                            onclick="togglePassword('adminPassword', this)">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>

                                    <small class="text-muted">
                                        Tidak perlu diisi jika tidak ingin mengubah password.
                                    </small>
                                </div>

                                {{-- CONFIRM PASSWORD --}}
                                <div class="col-md-6 mb-3">
                                    <label>Confirm Password</label>

                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="adminConfirmPassword"
                                            class="form-control">

                                        <span class="input-group-text" style="cursor:pointer"
                                            onclick="togglePassword('adminConfirmPassword', this)">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>

                            </div>


                            {{-- BUTTON --}}
                            <div class="text-end">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger rounded-pill px-4">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </a>

                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <style>
        .profile-card {
            border-radius: 20px;
            background: #f9f9f9;
        }

        .topbar {
            background: linear-gradient(90deg, #0d6efd, #0b5ed7);
            height: 70px;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-danger {
            border: none;
        }
    </style>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <style>
/* ===== DESKTOP TETAP ===== */
.topbar {
    background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    height: 70px;
}

/* card tetap aman */
.profile-card {
    border-radius: 20px;
    background: #f9f9f9;
}

/* form tetap rapi */
.form-control {
    border-radius: 10px;
}

/* ===== RESPONSIVE FIX ===== */
@media (max-width: 768px) {

    /* TOPBAR biar gak numpuk */
    .topbar {
        flex-direction: column;
        height: auto;
        padding: 10px 15px;
        gap: 10px;
    }

    /* user profile di tengah */
    .topbar > div:last-child {
        width: 100%;
        display: flex;
        justify-content: flex-end;
    }

    /* judul + breadcrumb biar gak sempit */
    .p-4 h3 {
        font-size: 22px;
    }

    /* row jadi stack */
    .row {
        flex-direction: column;
    }

    /* tombol jangan numpuk */
    .text-end {
        display: flex;
        flex-direction: column;
        gap: 10px;
        text-align: stretch !important;
    }

    .text-end a,
    .text-end button {
        width: 100%;
    }

    /* password input biar gak kecil */
    .input-group {
        flex-wrap: nowrap;
    }
}
</style>
@endsection
