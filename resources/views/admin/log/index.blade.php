@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-admin')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                @include('components.notif-admin')
                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>
            </div>

            <div class="p-3">
                {{-- TITLE --}}
                <div class="mb-3">
                    <h3 class="fw-bold" style="font-size: 30px;"> Log Aktivitas </h3>
                    <small class="text-muted">
                        Log Aktivitas / <span class="text-dark">Data Log</span>
                    </small>
                </div>



                {{-- CARD --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                        {{-- KIRI: TITLE --}}
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clipboard-data-fill fs-5 me-2"></i>
                            <span class="fw-semibold">Log Aktivitas</span>
                        </div>

                        {{-- KANAN: SEARCH --}}
                        <form method="GET" action="">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                        </form>

                    </div>


                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nama Pemilik</th>
                                    <th>Nama Kos</th>
                                    <th>Aktivitas</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $log->user->name ?? '-' }}</td>
                                        <td>{{ $log->kos->nama_kos ?? '-' }}</td>
                                        <td>{{ $log->aktivitas }}</td>
                                        <td>{{ $log->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $log->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Belum ada aktivitas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
