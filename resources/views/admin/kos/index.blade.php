@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-admin')

        <div class="flex-grow-1">

            <div class="topbar d-flex justify-content-between align-items-center px-4 w-100">
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

            <div class="p-3">
                <div class="mb-3">
                    <h3 class="fw-bold" style="font-size: 30px;">Verifikasi Kos</h3>
                    <small class="text-muted">
                        Manajemen Kos / <span class="text-dark">Data Kos</span>
                    </small>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-house-fill fs-5 me-2"></i>
                            <span class="fw-semibold">Data Kos</span>
                        </div>

                        <form method="GET" action="{{ route('admin.kos.index') }}">
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama kos/pemilik..." value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Pemilik</th>
                                        <th>Nama Kos</th>
                                        <th>Lokasi</th>
                                        <th>Status Kos</th>
                                        <th>Status Ubah Data</th>
                                        <th>Tgl Verifikasi</th>
                                        <th width="360" class="text-center">Aksi</th>
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
                                                    <span class="badge bg-secondary">Tidak Ada</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $k->tanggal_verifikasi ? $k->tanggal_verifikasi->format('d-m-Y H:i') : '-' }}
                                            </td>

                                            <td>
                                                <div
                                                    class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                                    <a href="{{ route('admin.kos.show', $k->id) }}"
                                                        class="btn btn-sm btn-info text-white">
                                                        Detail
                                                    </a>

                                                    <form action="{{ route('admin.kos.approve', $k->id) }}" method="POST"
                                                        class="approve-form m-0">
                                                        @csrf
                                                        <button type="button"
                                                            class="btn btn-sm btn-success btn-approve">Setujui</button>
                                                    </form>

                                                    <form action="{{ route('admin.kos.reject', $k->id) }}" method="POST"
                                                        class="reject-form m-0">
                                                        @csrf
                                                        <input type="hidden" name="alasan">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-reject">Tolak</button>
                                                    </form>

                                                    <form action="{{ route('admin.kos.deactivate', $k->id) }}"
                                                        method="POST" class="deactivate-form m-0">
                                                        @csrf
                                                        <input type="hidden" name="alasan">
                                                        <button type="button"
                                                            class="btn btn-sm btn-dark btn-deactivate">Nonaktifkan</button>
                                                    </form>

                                                    @if ($k->edit_request_status === 'menunggu')
                                                        <form
                                                            action="{{ route('admin.kos.edit-request.approve', $k->id) }}"
                                                            method="POST" class="approve-edit-form m-0">
                                                            @csrf
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary btn-approve-edit">
                                                                Setujui Ubah
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('admin.kos.edit-request.reject', $k->id) }}"
                                                            method="POST" class="reject-edit-form m-0">
                                                            @csrf
                                                            <input type="hidden" name="alasan">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger btn-reject-edit">
                                                                Tolak Ubah
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const askReasonThenSubmit = (buttonSelector, title, confirmText) => {
                document.querySelectorAll(buttonSelector).forEach(btn => {
                    btn.addEventListener('click', function() {
                        let form = this.closest('form');
                        let input = form.querySelector('input[name="alasan"]');

                        Swal.fire({
                            title: title,
                            input: 'textarea',
                            inputLabel: 'Alasan',
                            inputPlaceholder: 'Masukkan alasan...',
                            showCancelButton: true,
                            confirmButtonText: confirmText,
                            cancelButtonText: 'Batal',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Alasan wajib diisi!';
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (input) {
                                    input.value = result.value;
                                }
                                form.submit();
                            }
                        });
                    });
                });
            };

            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Setujui Verifikasi Kos?',
                        text: 'Kos akan ditampilkan kepada penyewa selama akun pemilik berstatus aktif.',
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

            document.querySelectorAll('.btn-approve-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Setujui Perubahan Data?',
                        text: 'Data kos akan diperbarui sesuai pengajuan dari pemilik.',
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

            askReasonThenSubmit('.btn-reject', 'Tolak Verifikasi Kos', 'Tolak');
            askReasonThenSubmit('.btn-reject-edit', 'Tolak Pengajuan Perubahan Data', 'Tolak');
            askReasonThenSubmit('.btn-deactivate', 'Nonaktifkan Kos', 'Nonaktifkan');
        });
    </script>

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
