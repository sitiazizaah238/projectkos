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

                    <h3 class="fw-semibold mb-1">Detail Pemilik Kos</h3>
                    <small class="text-primary">
                        Verifikasi data kos yang diajukan pemilik
                    </small>

                    {{-- CARD --}}
                    <div class="mt-4">
                        <div class="card shadow-sm border-0">

                            <div class="card-body">

                                {{-- INFORMASI PEMILIK --}}
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-person-circle fs-3 text-primary me-2"></i>
                                    <h5 class="mb-0 fw-semibold">Informasi Pemilik</h5>
                                </div>

                                <hr>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nama :</strong><br>
                                        {{ $pemilik->name }}
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <strong>Email :</strong><br>
                                        {{ $pemilik->email }}
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <strong>No. HP :</strong><br>
                                        {{ $pemilik->no_hp }}
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <strong>Alamat :</strong><br>
                                        {{ $pemilik->alamat ?? '-' }}
                                    </div>
                                </div>

                                {{-- DAFTAR KOS --}}
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-building fs-5 me-2 text-primary"></i>
                                    <h6 class="mb-0 fw-semibold">Daftar Kos Dimiliki</h6>
                                </div>

                                <hr>

                                @if ($pemilik->kos && $pemilik->kos->count())
                                    <ul class="list-group list-group-flush">
                                        @foreach ($pemilik->kos as $kos)
                                            <li class="list-group-item">
                                                {{ $kos->nama_kos }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">
                                        Belum memiliki kos
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    {{-- BUTTON KEMBALI --}}
                    <div class="text-end mt-4">
                        <a href="{{ route('admin.pemilik.index') }}" class="btn btn-primary px-4">
                            Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- MODAL PROFILE --}}
        <div class="modal fade" id="profileModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content p-3 text-center border-0" style="border-radius:20px;">
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
