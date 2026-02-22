@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            {{-- ================= TOPBAR ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid white;">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>
            </div>

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1" style="font-size: 30px;">
                    Riwayat Pembayaran
                </h3>

                <small class="text-muted d-block mb-4">
                    Pembayaran Kos / Riwayat Kos
                </small>

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
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pembayaran as $item)
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

                                                    <form
                                                        action="{{ route('penyewa.pembayaran.ajukan-ulang', $item->id) }}"
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

                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada riwayat pembayaran
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

    {{-- ================= PROFILE MODAL ================= --}}
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">

                <div class="mb-3">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>

                <a href="{{ route('penyewa.profile') }}" class="btn btn-primary w-100 mb-2">
                    Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger w-100">
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection
