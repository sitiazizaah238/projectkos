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
            <div class="p-4 mt-5">
                {{-- TOMBOL TAMBAH (KANAN + ICON) --}}
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('pemilik.kos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kos
                    </a>
                </div>


                {{-- CARD --}}
                <div class="card shadow-sm">

                    {{-- HEADER --}}
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-building fs-5 me-2"></i>
                            <span class="fw-semibold">Manajemen Data Kos</span>
                        </div>

                        {{-- SEARCH (CUMA UI, BELUM FUNCTION) --}}
                        <div class="input-group" style="width:250px;">
                            <input type="text" class="form-control" placeholder="Cari data...">
                            <button class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>

                    </div>

                    {{-- BODY --}}
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">


                            {{-- HEAD --}}
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="100">Foto Kos</th>
                                    <th>Nama Kos</th>
                                    <th>Lokasi Kos</th>
                                    <th>Tipe Kos</th>
                                    <th width="120">Status</th>
                                    <th>Alasan</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>

                            {{-- BODY --}}
                            <tbody>
                                @forelse($kos as $k)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        {{-- FOTO --}}
                                        <td>
                                            @if ($k->foto)
                                                <img src="{{ asset('storage/' . $k->foto) }}" width="70"
                                                    style="border-radius:8px">
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>{{ $k->nama_kos }}</td>
                                        <td>{{ $k->lokasi }}</td>
                                        <td>{{ $k->tipe_kos }}</td>

                                        {{-- STATUS (badge kaya admin) --}}
                                        <td>
                                            @if ($k->status == 'disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($k->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                            @endif
                                        </td>

                                        <td>{{ $k->alasan ?? '-' }}</td>

                                        {{-- AKSI --}}
                                        <td>

                                            <a href="{{ route('pemilik.kos.show', $k->id) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a href="{{ route('pemilik.kos.edit', $k->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <form action="{{ route('pemilik.kos.destroy', $k->id) }}" method="POST"
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
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                Belum ada data kos
                                            </div>
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


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", function() {
                    let form = this.closest("form");

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data kos akan dihapus permanen!",
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
