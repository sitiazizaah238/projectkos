@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        {{-- MAIN --}}
        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                <div class="text-white">
                    {{ Auth::user()->email }}
                    <i class="bi bi-person-circle ms-2 fs-4"></i>
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

@endsection
