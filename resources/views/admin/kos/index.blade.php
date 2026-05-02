@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row" style="min-height:100vh; overflow-x:hidden;">

        @include('components.sidebar-admin')

        <div class="flex-grow-1">
            <div class="topbar d-flex flex-wrap justify-content-between align-items-center px-3 px-md-4 w-100">
                <div></div>

                <div class="d-flex align-items-center">
                    @include('components.notif-admin')
                    <button type="button" class="btn text-white d-flex align-items-center gap-3" data-bs-toggle="modal"
                        data-bs-target="#profileModal">

                        <span class="fw-semibold small">
                            {{ Auth::user()->name }}
                        </span>

                        @if (Auth::user()->photo)
                            <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                                style="width:35px; height:35px; border-radius:50%; object-fit:cover;">
                        @else
                            <i class="bi bi-person-circle fs-3"></i>
                        @endif

                    </button>
                </div>
            </div>

            <div class="p-4">
                <div class="mb-3">
                    <h3 class="fw-bold" style="font-size: 30px;">Verifikasi Data Kos</h3>
                    <small class="text-muted">
                        Manajemen Kos / <span class="text-dark">Data Kos</span>
                    </small>
                </div>
                {{-- SEARCH MOBILE --}}
                <div class="d-block d-md-none mb-3">
                    <form method="GET" action="{{ route('admin.kos.index') }}">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Cari kos / lokasi..."
                                value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
                <div class="card shadow-sm ms-1">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-house-fill fs-5 me-2"></i>
                            <span class="fw-semibold">Data Kos</span>
                        </div>

                        <form method="GET" action="{{ route('admin.kos.index') }}" class="d-none d-md-block">
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama kos/Lokasi..." value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">

                            <table class="table table-bordered mb-0 align-middle w-100 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>

                                        <th>Nama Kos</th>
                                        <th>Lokasi Kos</th>
                                        <th>Status Verfikasi</th>
                                        <th>Status Perubahan kos</th>
                                        <th>Tanggal Verifikasi</th>
                                        <th width="150" class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($kos as $k)
                                        <tr>
                                            <td>{{ ($kos->currentPage() - 1) * $kos->perPage() + $loop->iteration }}</td>

                                            <td>{{ $k->nama_kos }}</td>
                                            <td>{{ $k->lokasi }}</td>

                                            <td>
                                                @if ($k->status == 'disetujui')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif($k->status == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @elseif($k->status == 'nonaktif')
                                                    <span class="badge bg-dark">Nonaktif</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($k->edit_request_status === 'menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                                @elseif($k->edit_request_status === 'disetujui')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif($k->edit_request_status === 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Ada Perubahan</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $k->tanggal_verifikasi ? $k->tanggal_verifikasi->format('d-m-Y') : '-' }}
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <a href="{{ route('admin.kos.show', $k->id) }}"
                                                        class="btn btn-sm btn-info text-white">
                                                        Detail
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                @if (request('search'))
                                                    <div class="text-muted">
                                                        Data tidak ditemukan untuk
                                                        "<strong>{{ request('search') }}</strong>"
                                                    </div>
                                                @else
                                                    <div class="text-muted">
                                                        Belum ada data kos
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="p-3 d-flex justify-content-end">
                                {{ $kos->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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
    <style>
@media (min-width: 992px) {

    .card.shadow-sm {
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table.table {
        width: 100%;
    }

    table.table th,
    table.table td {
        white-space: nowrap;   /* tetap rapi horizontal */
        font-size: 14px;
        vertical-align: middle;
    }

    /* biar kolom panjang (nama/lokasi) tidak ganggu */
    table.table td:nth-child(2),
    table.table td:nth-child(3) {
        white-space: normal;   /* hanya ini yang boleh wrap */
        max-width: 200px;
    }

    /* kolom aksi tetap kecil */
    table.table th:last-child,
    table.table td:last-child {
        width: 120px;
        text-align: center;
    }

    /* badge jangan bikin tinggi aneh */
    .badge {
        font-size: 12px;
    }
}
</style>
@endsection
