@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold text-white small">
                        {{ Auth::user()->name }}
                    </span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif
                </button>
            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 30px;">
                    Pengajuan Penyewa
                </h3>

                <small class="text-muted d-block mb-4">
                    Data Pengajuan / Data Sewa
                </small>
                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white d-flex align-items-center">
                        <i class="bi bi-receipt me-2"></i>
                        <span class="fw-semibold">Data Penyewa</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Penyewa</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pengajuan as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->kos->nama_kos }}</td>
                                        <td>{{ $p->penyewa->name }}</td>
                                        <td>{{ $p->kamar->nama_kamar }}</td>
                                        <td>{{ $p->tanggal_mulai }}</td>
                                        <td>
                                            @if ($p->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pemilik.pengajuan.show', $p->id) }}"
                                                class="btn btn-sm btn-warning text-white">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    @if ($pengajuan->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                            <span class="text-muted fw-semibold">
                                Belum ada pengajuan kos
                            </span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold text-white small">
                        {{ Auth::user()->name }}
                    </span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif
                </button>
            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-4">Manajemen Pengajuan Sewa</h3>

                <div class="card shadow-sm rounded-4 overflow-hidden">

                    <div class="bg-dark text-white p-3">
                        Data Penyewa
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Penyewa</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pengajuan as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $p->kos->nama_kos }}</td>
                                        <td>{{ $p->penyewa->name }}</td>
                                        <td>{{ $p->kamar->nama_kamar }}</td>
                                        <td>{{ $p->tanggal_mulai }}</td>
                                        <td>
                                            @if ($p->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pemilik.pengajuan.show', $p->id) }}"
                                                class="btn btn-sm btn-warning text-white">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

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

@endsection
