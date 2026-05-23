@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-between align-items-center px-4 w-100">

                {{-- KIRI (BIAR STABIL, WALAU KOSONG) --}}
                <div></div>

                {{-- KANAN (PROFILE) --}}
                <div class="d-flex align-items-center">
                    @include('components.notif-admin')
                    <button type="button" class="btn text-white d-flex align-items-center gap-3" data-bs-toggle="modal"
                        data-bs-target="#profileModal">

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

                <h3 class="fw-semibold">Manajemen Akun</h3>
                <small class="text-primary">
                    Manajemen Akun / Edit Akun
                </small>

                <div class="card shadow-sm border-0 mt-4" style="border-radius:12px;">
                    <div class="card-body">

                        {{-- HEADER --}}
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-lines-fill fs-5 me-2"></i>
                            <h6 class="mb-0 fw-semibold">Edit Akun</h6>
                        </div>

                        <hr>

                        {{-- FORM --}}
                        <form action="{{ route('admin.pemilik.update', $pemilik->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- NAMA --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Pemilik Kos</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $pemilik->name) }}" required>
                                </div>

                                {{-- EMAIL --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $pemilik->email) }}"
                                        @if (Auth::user()->role !== 'pemilik') disabled @endif required>
                                </div>

                                {{-- NO HP --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Hp</label>
                                    <input type="text" name="no_hp" class="form-control"
                                        value="{{ old('no_hp', $pemilik->no_hp) }}"
                                        @if (Auth::user()->role !== 'pemilik') disabled @endif required>
                                </div>

                                {{-- STATUS SWITCH --}}
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        {{-- Hidden input supaya selalu ada nilai --}}
                                        <input type="hidden" name="status" value="nonaktif">

                                        <input class="form-check-input" type="checkbox" name="status" value="aktif"
                                            id="statusSwitch" {{ $pemilik->status == 'aktif' ? 'checked' : '' }}>

                                        <label class="form-check-label ms-2" for="statusSwitch">
                                            Aktif
                                        </label>
                                    </div>
                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <div class="text-end mt-3">
                                <a href="{{ route('admin.pemilik.index') }}" class="btn btn-danger me-2 px-4">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- MODAL PROFILE --}}
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">
                <div class="mb-3">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>

                <a href="{{ route('admin.profile') }}" class="btn btn-primary w-100 mb-2">
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
