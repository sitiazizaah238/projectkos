@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">
                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '{{ session('success') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    </script>
                @endif
                <h3 class="fw-bold mb-4">Verifikasi Pembayaran</h3>

                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white">
                        Data Pembayaran
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Penyewa</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pembayaran as $item)
                                    <tr class="align-middle">

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ optional($item->pengajuan->penyewa)->name ?? '-' }}</td>

                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>

                                        <td>Rp {{ number_format($item->pengajuan->total_bayar, 0, ',', '.') }}</td>

                                        <td>{{ $item->metode->nama_metode }}</td>

                                        <td>
                                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank"
                                                class="btn btn-sm btn-warning">
                                                Lihat
                                            </a>
                                        </td>

                                        <td>
                                            @if ($item->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu Verifikasi</span>
                                            @elseif($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($item->status == 'menunggu')
                                                <form id="form-konfirmasi-{{ $item->id }}"
                                                    action="{{ route('pemilik.verifikasi.konfirmasi', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-primary btn-konfirmasi"
                                                        data-id="{{ $item->id }}">
                                                        Konfirmasi
                                                    </button>
                                                </form>

                                                <form id="form-tolak-{{ $item->id }}"
                                                    action="{{ route('pemilik.verifikasi.tolak', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-danger btn-tolak"
                                                        data-id="{{ $item->id }}">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                -
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            Belum ada pembayaran masuk
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.btn-konfirmasi').forEach(button => {
        button.addEventListener('click', function() {

            let id = this.getAttribute('data-id');

            Swal.fire({
                title: 'Konfirmasi Pembayaran?',
                text: "Pembayaran akan disetujui dan kamar otomatis terisi!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Konfirmasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-konfirmasi-' + id).submit();
                }
            });

        });
    });

    document.querySelectorAll('.btn-tolak').forEach(button => {
        button.addEventListener('click', function() {

            let id = this.getAttribute('data-id');

            Swal.fire({
                title: 'Tolak Pembayaran?',
                text: "Pembayaran akan ditolak!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-tolak-' + id).submit();
                }
            });

        });
    });

});
</script>

@endsection
