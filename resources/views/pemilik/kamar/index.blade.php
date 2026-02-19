@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>

            </div>


            {{-- CONTENT --}}
            <div class="p-4">

                {{-- PAGE TITLE --}}
                <div class="mb-4">
                   <h3 class="fw-bold" style="font-size:25px;">
                    Manajemen Kamar</h3>
                    <small class="text-muted">Manajemen Kos / Data Kamar Kos</small>
                </div>

                {{-- TOMBOL TAMBAH --}}
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kamar
                    </a>
                </div>

                <div class="card shadow-sm">

                    {{-- HEADER --}}
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-door-open fs-5 me-2"></i>
                            <span class="fw-semibold">Manajemen Data Kamar</span>
                        </div>

                        <form method="GET" action="{{ route('pemilik.kamar.index') }}">
                            <div class="input-group" style="width:250px;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari nama kamar / kos...">

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                    </div>

                    {{-- BODY --}}
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Harga</th>
                                    <th>Tipe Harga</th>
                                    <th>Status Kamar</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($kamars as $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $k->kos->nama_kos }}</td>
                                        <td>{{ $k->nama_kamar }}</td>
                                        <td>
                                            Rp. {{ number_format($k->harga, 0, ',', '.') }}
                                        </td>

                                        <td>{{ ucfirst($k->tipe_harga) }}</td>

                                        <td>
                                            @if ($k->status == 'tersedia')
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Terisi</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('pemilik.kamar.show', $k->id) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a href="{{ route('pemilik.kamar.edit', $k->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <form action="{{ route('pemilik.kamar.destroy', $k->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            @if (request('search'))
                                                Data kamar atau nama kos tidak ditemukan
                                            @else
                                                Belum ada data kamar
                                            @endif
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

    {{-- SWEET ALERT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", function() {
                    let form = this.closest("form");

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data kamar akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
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
