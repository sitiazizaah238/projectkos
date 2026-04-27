@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-penyewa')

                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold small">
                        {{ Auth::user()->name }}
                    </span>

                    <i class="bi bi-person-circle fs-3"></i>
                </button>
            </div>

            {{-- ================= CONTENT ================= --}}
            <div class="p-4">

                {{-- TITLE --}}
                <div class="mb-2">
                    <h3 class="fw-bold mb-1" style="font-size:25px;">
                        Riwayat Pembayaran
                    </h3>
                    <small class="text-muted">
                        Histori pembayaran kos penyewa
                    </small>
                </div>

                {{-- SEARCH --}}
                @php
                    $selectedPerPage = (int) request('per_page', 10);
                    if (!in_array($selectedPerPage, [5, 10], true)) {
                        $selectedPerPage = 10;
                    }
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                    <form method="GET" class="d-flex align-items-center gap-2">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                    </form>

                    <form method="GET">
                        <div class="input-group" style="width:300px;">
                            <input type="hidden" name="per_page" value="{{ $selectedPerPage }}">

                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control rounded-start-pill" placeholder="Cari nama kos...">

                            <button type="submit" class="btn btn-primary rounded-end-pill">
                                <i class="bi bi-search"></i>
                            </button>

                        </div>
                    </form>
                </div>

                <div class="card shadow-sm">
                    {{-- HEADER --}}
                    <div class="card-header bg-dark text-white">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history fs-5 me-2"></i>
                            <span class="fw-semibold">Data Riwayat Pembayaran</span>
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Kos</th>
                                    <th>Kamar</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Metode Bayar</th>
                                    <th>Status Pembayaran</th>
                                    <th>Total</th>

                                    {{-- 🔧 DIPERKECIL --}}
                                    <th width="110">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($riwayat as $item)
                                    @php
                                        $kos = $item->pengajuan->kos ?? null;
                                        $kamar = $item->pengajuan->kamar ?? null;
                                        $totalBayar = $item->nominal_tagihan;

                                        $metode = is_array($item->metode)
                                            ? $item->metode
                                            : json_decode($item->metode, true);
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $riwayat->firstItem() + $loop->index }}
                                        </td>

                                        <td>{{ $kos->nama_kos ?? '-' }}</td>
                                        <td>{{ $kamar->nama_kamar ?? '-' }}</td>
                                        <td>{{ $item->created_at?->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            @php
                                                $tanggalMulai = \Carbon\Carbon::parse(
                                                    $item->pengajuan->tanggal_mulai ?? now(),
                                                );
                                                $durasi = $item->pengajuan->durasi ?? 0;
                                                $tanggalSelesai = $tanggalMulai->copy()->addMonths($durasi);
                                            @endphp

                                            {{ $tanggalSelesai->format('d-m-Y') }}
                                        </td>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $metode['nama_metode'] ?? '-' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $metode['no_rekening'] ?? '' }}
                                            </small>
                                        </td>

                                        <td>
                                            @if ($item->status == 'dikonfirmasi')
                                                <span class="badge bg-success">Berhasil</span>
                                            @elseif($item->status == 'menunggu')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Gagal</span>
                                            @endif
                                        </td>

                                        <td class="fw-bold text-success">
                                            Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                        </td>

                                        {{-- 🔧 TOMBOL DIPERKECIL --}}
                                        <td>
                                            <a href="{{ route('penyewa.riwayat.detail', $item->id) }}"
                                                class="btn btn-sm btn-info text-white px-2 py-1 d-flex align-items-center justify-content-center gap-1">
                                                <i class="bi bi-eye-fill"></i>
                                                <small>Detail</small>
                                            </a>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            @if (request('search'))
                                                Data tidak ditemukan
                                            @else
                                                Belum ada riwayat pembayaran
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                        {{-- PAGINATION --}}
                        @if ($riwayat->hasPages())
                            <div class="p-3 d-flex justify-content-end">
                                {{ $riwayat->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= PROFILE MODAL (FIX + TAMBAH PROFIL TANPA HAPUS APA PUN) ================= --}}
    <div class="modal fade" id="profileModal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">

                <div class="fw-bold">{{ Auth::user()->name }}</div>
                <small class="text-muted">{{ Auth::user()->email }}</small>

                <hr>

                {{-- 🔥 TAMBAHAN (SEPERTI INDEX) --}}
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
