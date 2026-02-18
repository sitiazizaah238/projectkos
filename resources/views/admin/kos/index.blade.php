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

            <div class="p-4 mt-4">

                {{-- SEARCH --}}
                <div class="d-flex justify-content-end mb-3">
                    <form method="GET" action="{{ route('admin.kos.index') }}">
                        <div class="input-group" style="width:300px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari kos / pemilik..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- CARD --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex align-items-center">
                        <i class="bi bi-house-fill fs-5 me-2"></i>
                        <span class="fw-semibold">Data Kos</span>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Pemilik</th>
                                    <th>Nama Kos</th>
                                    <th>Lokasi</th>
                                    <th width="120">Status</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($kos as $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $k->user->name }}</td>
                                        <td>{{ $k->nama_kos }}</td>
                                        <td>{{ $k->lokasi }}</td>

                                        <td>
                                            @if ($k->status == 'disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($k->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($k->status == 'menunggu')
                                                {{-- SETUJU --}}
                                                <form action="{{ route('admin.kos.approve', $k->id) }}" method="POST"
                                                    class="d-inline approve-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success btn-approve">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>

                                                {{-- TOLAK --}}
                                                <form action="{{ route('admin.kos.reject', $k->id) }}" method="POST"
                                                    class="d-inline reject-form">
                                                    @csrf
                                                    <input type="hidden" name="alasan">
                                                    <button type="button" class="btn btn-sm btn-danger btn-reject">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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
                                                    Belum ada data kos
                                                </div>
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

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // SETUJU
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin setujui?',
                        text: "Kos ini akan ditampilkan ke penyewa",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, setujui',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // TOLAK
            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');
                    let input = form.querySelector('input[name="alasan"]');

                    Swal.fire({
                        title: 'Tolak Kos',
                        input: 'textarea',
                        inputLabel: 'Alasan penolakan',
                        inputPlaceholder: 'Masukkan alasan...',
                        showCancelButton: true,
                        confirmButtonText: 'Tolak',
                        cancelButtonText: 'Batal',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Alasan wajib diisi!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            input.value = result.value; // DIKIRIM KE LARAVEL
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>
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
