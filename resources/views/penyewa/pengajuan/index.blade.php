@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            {{-- ================= TOPBAR (SAMA KAYA DASHBOARD) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                            style="
            width:40px;
            height:40px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid white;
         ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>

            </div>


            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1" style="font-size: 30px;">Data Pengajuan</h3>
                <small class="text-muted d-block mb-4">
                    Pengajuan Kos / Data Pengajuan
                </small>

                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-clipboard-data"></i> Data Pengajuan
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">

                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Durasi Sewa</th>
                                    <th>Status Pengajuan</th>
                                    <th>Status Kamar</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @if ($pengajuan->count() > 0)
                                    @foreach ($pengajuan as $item)
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $item->kos->nama_kos ?? '-' }}</td>

                                            <td>{{ $item->kamar->nama_kamar ?? '-' }}</td>

                                            <td>{{ $item->tanggal_mulai }}</td>

                                            <td>{{ $item->durasi }} Bulan</td>

                                            {{-- STATUS --}}
                                            <td>
                                                @if ($item->status == 'menunggu')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @elseif($item->status == 'disetujui')
                                                    <span class="badge bg-primary">Disetujui</span>
                                                @elseif($item->status == 'aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif($item->status == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            {{-- STATUS KAMAR --}}
                                            <td>
                                                @if ($item->kamar->status == 'tersedia')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @elseif($item->kamar->status == 'terisi')
                                                    <span class="badge bg-danger">Terisi</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>

                                            {{-- ALASAN --}}
                                            <td>
                                                @if ($item->status == 'ditolak')
                                                    {{ $item->alasan ?? '-' }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- AKSI --}}
                                            <td>
                                                @if ($item->status == 'disetujui')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#modalBayar{{ $item->id }}">
                                                        Bayar Sekarang
                                                    </button>
                                                @elseif($item->status == 'aktif')
                                                    <span class="badge bg-success">Sudah Dibayar</span>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                        </tr>
                                        {{-- ================= MODAL PEMBAYARAN ================= --}}
                                        <div class="modal fade" id="modalBayar{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 p-4">

                                                    <h5 class="fw-bold mb-3">Pembayaran Sewa Kamar</h5>

                                                    {{-- Informasi Sewa --}}
                                                    <div class="border rounded-4 p-3 mb-3">
                                                        <div class="fw-semibold mb-2">Informasi Sewa</div>

                                                        <div class="row text-center">
                                                            <div class="col">
                                                                <small>Nama Kos</small>
                                                                <div>{{ $item->kos->nama_kos }}</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Durasi Sewa</small>
                                                                <div>{{ $item->durasi }} Bulan</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Nama Kamar</small>
                                                                <div>{{ $item->kamar->nama_kamar }}</div>
                                                            </div>

                                                            <div class="col">
                                                                <small>Total Bayar</small>
                                                                <div class="fw-bold text-success">
                                                                    Rp
                                                                    {{ number_format($item->kamar->harga * $item->durasi, 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Form Pembayaran --}}
                                                    <form action="{{ route('penyewa.bayar', $item->id) }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf

                                                        {{-- Pilih Metode --}}
                                                        <div class="mb-3">
                                                            <label class="fw-semibold mb-2">Pilih Metode Pembayaran</label>

                                                            @forelse($item->kos->user->metodePembayaran ?? [] as $metode)
                                                                <div
                                                                    class="border rounded-3 p-2 mb-2 d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <input type="radio" name="metode_id"
                                                                            value="{{ $metode->id }}" required>
                                                                        {{ $metode->nama_metode }}
                                                                        <br>
                                                                        <small>{{ $metode->no_rekening }}</small>
                                                                    </div>

                                                                    @if ($metode->gambar)
                                                                        <img src="{{ asset('storage/' . $metode->gambar) }}"
                                                                            width="70">
                                                                    @endif
                                                                </div>
                                                            @empty
                                                                <div class="text-muted small">
                                                                    Metode pembayaran belum tersedia
                                                                </div>
                                                            @endforelse
                                                            {{-- Upload Bukti --}}
                                                            <div class="mb-3">
                                                                <label class="fw-semibold">Upload Bukti Transfer</label>
                                                                <input type="file" name="bukti" class="form-control"
                                                                    required>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>

                                                                <button class="btn btn-primary">
                                                                    Bayar
                                                                </button>
                                                            </div>

                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center text-muted">
                                                <i class="bi bi-info-circle fs-1 mb-2"></i>
                                                <span class="fw-semibold">
                                                    Silakan mengajukan kos terlebih dahulu
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

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
