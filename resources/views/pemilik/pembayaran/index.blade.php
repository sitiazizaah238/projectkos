@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">
            {{-- TOPBAR --}}
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
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <!-- TITLE -->
                <h3 class="fw-bold mb-1" style="font-size: 30px;">
                    Metode Pembayaran
                </h3>
                <small class="text-muted">
                    Pembayaran / Metode Bayar
                </small>

                <!-- BUTTON RIGHT & TURUN SEDIKIT -->
               <div class="d-flex justify-content-end" style="margin-top: 10px;">
                    <a href="{{ route('pemilik.pembayaran.create') }}" class="btn btn-primary px-4">
                        + Tambah Metode Baru
                    </a>
                </div>

                <!-- CARD -->
                <div class="card shadow-sm rounded-4 mt-3">

                    <div class="card-header bg-dark text-white d-flex align-items-center">
                        <i class="bi bi-credit-card me-2"></i>
                        <span class="fw-semibold">Metode Pembayaran</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Metode</th>
                                    <th>Atas Nama</th>
                                    <th>No Rekening</th>
                                    <th>Gambar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($metode as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_metode }}</td>
                                        <td>{{ $item->atas_nama }}</td>
                                        <td>{{ $item->no_rekening ?? '-' }}</td>

                                        <td>
                                            @if ($item->gambar)
                                                <img src="{{ asset('storage/' . $item->gambar) }}" width="60">
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @if ($item->status == 'aktif')
                                                <span class="text-success">● Aktif</span>
                                            @else
                                                <span class="text-danger">● Non-Aktif</span>
                                            @endif
                                        </td>

                                        <td class="d-flex justify-content-center gap-2">

                                            <a href="{{ route('pemilik.pembayaran.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('pemilik.pembayaran.destroy', $item->id) }}"
                                                method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum ada metode pembayaran</td>
                                    </tr>
                                @endforelse

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {

                    let form = this.closest('.form-delete');

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data metode pembayaran akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
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
@endsection
