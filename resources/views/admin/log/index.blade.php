@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row" style="min-height:100vh; overflow-x:hidden;">

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

                        <span class="fw-semibold small text-nowrap">
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

            <div class="p-3">
                {{-- TITLE --}}
                <div class="mb-3">
                    <h3 class="fw-bold" style="font-size: 30px;"> Log Aktivitas </h3>
                    <small class="text-muted">
                        Log Aktivitas / <span class="text-dark">Data Log</span>
                    </small>
                </div>

{{-- SEARCH MOBILE --}}
<div class="d-block d-md-none mb-3">
    <form method="GET" action="">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
        </div>
    </form>
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
                      <form method="GET" action="" class="d-none d-md-block">
                           <div class="d-flex flex-column flex-md-row gap-2">

                                {{-- FILTER WAKTU --}}
                                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">

                                    <option value="7" {{ request('filter') == '7' ? 'selected' : '' }}>7 Hari</option>
                                    <option value="30" {{ request('filter') == '30' ? 'selected' : '' }}>30 Hari
                                    </option>
                                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Semua</option>
                                </select>

                                {{-- SEARCH --}}
                                <div class="input-group input-group-sm" style="width:200px;">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control" placeholder="Cari..."
                                        value="{{ request('search') }}">
                                </div>

                            </div>
                        </form>

                    </div>


                    <div class="card-body p-0">
                        <div class="card-body p-0">

                            {{-- 🔵 INFO FILTER --}}
                            @if (request('filter') == '7')
                                <div class="p-2 px-3 bg-light border-bottom">
                                    <span class="badge bg-primary">
                                        Menampilkan log 7 hari terakhir
                                    </span>
                                </div>
                            @elseif(request('filter') == '30')
                                <div class="p-2 px-3 bg-light border-bottom">
                                    <span class="badge bg-primary">
                                        Menampilkan log 30 hari terakhir
                                    </span>
                                </div>
                            @elseif(request('filter') == 'all')
                                <div class="p-2 px-3 bg-light border-bottom">
                                    <span class="badge bg-secondary">
                                        Menampilkan semua log aktivitas
                                    </span>
                                </div>
                            @endif
                            <div class="table-responsive" style="overflow-x:auto; -webkit-overflow-scrolling:touch;">

                                    <table class="table table-bordered mb-0 align-middle w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">No</th>
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
                                                    <td class="text-nowrap">
                                                        {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}
                                                    </td>
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
                            @if ($logs->hasPages())
                                <div class="p-3 d-flex justify-content-end">
                                    {{ $logs->links() }}
                                </div>
                            @endif
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
