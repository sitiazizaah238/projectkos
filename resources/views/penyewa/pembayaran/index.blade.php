@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-4">Riwayat Pembayaran</h3>

                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between">
                        <span>
                            <i class="bi bi-credit-card"></i> Data Pembayaran
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembayaran as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->pengajuan->kos->nama_kos }}</td>
                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                                        <td>Rp {{ number_format($item->pengajuan->total_bayar ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $item->pengajuan->durasi }} Bulan</td>

                                        {{-- STATUS --}}
                                        <td>
                                            @if ($item->status == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @elseif($item->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        <td>
                                            @if ($item->status == 'ditolak')
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#modalUlang{{ $item->id }}">
                                                    Ajukan Ulang
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- MODAL AJUKAN ULANG --}}
                                    @if ($item->status == 'ditolak')
                                        <div class="modal fade" id="modalUlang{{ $item->id }}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 p-4">

                                                    <h5 class="fw-bold mb-3">Ajukan Ulang Pembayaran</h5>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Alasan Penolakan</label>
                                                        <div class="alert alert-danger">
                                                            {{ $item->alasan }}
                                                        </div>
                                                    </div>

                                                    <form action="{{ route('penyewa.pembayaran.ajukan-ulang', $item->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Upload Bukti Baru</label>
                                                            <input type="file" name="bukti" class="form-control"
                                                                required>
                                                        </div>

                                                        <div class="d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-danger"
                                                                data-bs-dismiss="modal">
                                                                Batal
                                                            </button>
                                                            <button class="btn btn-primary">
                                                                Kirim Ulang
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
