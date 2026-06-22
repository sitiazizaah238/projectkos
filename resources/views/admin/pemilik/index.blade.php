@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row" style="min-height:100vh; overflow-x:hidden;">

        {{-- SIDEBAR --}}
        @include('components.sidebar-admin')

        <div class="flex-grow-1" style="min-width:0;">

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
            <div class="p-3">
                {{-- TITLE --}}
                <div class="mb-3 ms-2 ms-md-3">
                    <h3 class="fw-bold" style="font-size: 29px;"> Manajemen Akun</h3>
                    <small class="text-muted">
                        Manajemen Akun / <span class="text-dark">Daftar Akun</span>
                    </small>
                </div>

                {{-- SEARCH MOBILE --}}
                <div class="d-block d-md-none mb-3">
                    <form method="GET" action="{{ route('admin.pemilik.index') }}">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Cari akun..."
                                value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
               <div class="card shadow-sm ms-2 ms-md-2">
                    <div
                        class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">

                        {{-- KIRI: TITLE --}}
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill fs-5 me-2"></i>
                            <span class="fw-semibold">Akun Terdaftar</span>
                        </div>

                        {{-- KANAN: SEARCH --}}
                        <form method="GET" action="{{ route('admin.pemilik.index') }}" class="d-none d-md-block">
                            <div class="input-group input-group-sm" style="width:250px;">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                        </form>

                    </div>


                    <div class="card-body p-0 table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Pemilik Kos</th>
                                    <th>Jumlah Kos</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th width="120">Status</th>
                                    <th width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pemilik as $key => $p)
                                    <tr>
                                        <td>{{ $pemilik->firstItem() + $key }}</td>
                                        <td>{{ $p->name }}</td>
                                        <td>
                                            {{ $p->kos->count() }}
                                        </td>
                                        <td>{{ $p->email }}</td>
                                        <td>{{ $p->no_hp }}</td>
                                        <td>
                                            @if ($p->status == 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{-- DETAIL --}}
                                            <a href="{{ route('admin.pemilik.show', $p->id) }}"
                                                class="btn btn-sm btn-info text-white " data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            {{-- EDIT --}}
                                            <a href="{{ route('admin.pemilik.edit', $p->id) }}"
                                                class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            {{-- HAPUS --}}
                                            <form action="{{ route('admin.pemilik.destroy', $p->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $p->id }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>

                                                {{-- MODAL DELETE --}}
                                                <div class="modal fade" id="deleteModal{{ $p->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                                        <div class="modal-content text-center p-4"
                                                            style="border-radius:18px;">

                                                            {{-- tombol X kanan atas --}}
                                                            <button type="button"
                                                                class="btn-close position-absolute top-0 end-0 m-3"
                                                                data-bs-dismiss="modal"></button>

                                                            {{-- ICON X MERAH --}}
                                                            <div class="mb-3">
                                                                <div
                                                                    style="
                        width:70px;
                        height:70px;
                        border-radius:50%;
                        border:3px solid #ff5a5a;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:auto;">
                                                                    <span style="font-size:30px;color:#ff5a5a;">✕</span>
                                                                </div>
                                                            </div>

                                                            {{-- TEXT --}}
                                                            <h5 class="fw-bold mb-2">Hapus Akun Ini?</h5>
                                                            <p class="text-muted small mb-4">
                                                                Yakin ingin menghapus? data yang telah di hapus tidak dapat
                                                                di kembalikan.
                                                            </p>

                                                            {{-- BUTTON --}}
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-secondary w-100"
                                                                    data-bs-dismiss="modal">
                                                                    Cancel
                                                                </button>

                                                                <button type="submit" class="btn w-100 text-white"
                                                                    style="background:#ff5a5a;">
                                                                    Delete
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            @if (request('search'))
                                                <div class="text-muted">
                                                    Data tidak ditemukan untuk
                                                    "<strong>{{ request('search') }}</strong>"
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    Belum ada pemilik terdaftar
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $pemilik->links() }}
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
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
