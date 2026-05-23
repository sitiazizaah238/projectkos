@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR (SUDAH DISESUAIKAN INDEX STYLE) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-penyewa')

                {{-- 👤 PROFILE (TETAP, HANYA DIPERBAIKI MODALNYA) --}}
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

            {{-- ================= CONTENT (TIDAK DIHAPUS SAMA SEKALI) ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <div class="mb-4">
                    <h2 class="fw-bold mb-3" style="font-size:28px;">
                        Detail Riwayat Pembayaran
                    </h2>
                </div>

                @php
                    $pengajuan = $data->pengajuan ?? null;
                    $kos = $pengajuan->kos ?? null;
                    $kamar = $pengajuan->kamar ?? null;
                    $penyewa = $pengajuan->penyewa ?? null;

                    $total = $data->nominal_tagihan;
                    $durasi = $pengajuan->durasi ?? 0;
                    $tipeHarga = $kamar->tipe_harga ?? 'bulanan';

                    $metode = is_array($data->metode) ? $data->metode : json_decode($data->metode, true);
                @endphp

                {{-- CARD 1 --}}
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-dark text-white">
                        🧾 Informasi Transaksi & Kos
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6">
                                <p class="mb-2"><strong>ID:</strong> {{ $data->id }}</p>
                                <p class="mb-2"><strong>Tanggal Pembayaran:</strong>
                                    {{ $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-' }}
                                </p>
                                <p class="mb-2"><strong>Status Pembayaran:</strong>
                                    @if ($data->status == 'dikonfirmasi')
                                        <span class="badge bg-success">Berhasil</span>
                                    @elseif($data->status == 'menunggu')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @else
                                        <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nama Kos:</strong> {{ $kos->nama_kos ?? '-' }}</p>
                                <p class="mb-2"><strong>Alamat Kos:</strong> {{ $kos->lokasi ?? '-' }}</p>
                                <p class="mb-2"><strong>Nama Kamar:</strong> {{ $kamar->nama_kamar ?? '-' }}</p>
                                <p class="mb-2"><strong>Total Bayar:</strong> Rp {{ number_format($total, 0, ',', '.') }}
                                </p>
                                <p class="mb-2"><strong>Durasi Sewa:</strong>
                                    {{ \App\Models\PengajuanSewa::formatDurasiByTipe((int) $durasi, $tipeHarga) }}</p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CARD 2 --}}
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-info text-white">
                        👤 Penyewa & Rincian Pembayaran
                    </div>
                    <div class="card-body">
                        <p><strong>NamaPenyewa:</strong> {{ $penyewa->name ?? '-' }}</p>
                        <p><strong>Total Bayar:</strong> Rp {{ number_format($total, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- CARD 3 --}}
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-warning">
                        💳 Metode & Bukti Pembayaran
                    </div>

                    <div class="card-body">
                        <div class="row align-items-start">

                            <div class="col-md-6">
                                <p class="mb-2"><strong>Metode Pembayaran:</strong> {{ $metode['nama_metode'] ?? '-' }}
                                </p>
                                <p class="mb-2"><strong>No Rekening:</strong> {{ $metode['no_rekening'] ?? '-' }}</p>
                                <p class="mb-2"><strong>Atas Nama:</strong> {{ $metode['atas_nama'] ?? '-' }}</p>
                            </div>

                            <div class="col-md-6 d-flex flex-column align-items-center">
                                @if (!empty($data->bukti))
                                    <img src="{{ asset('storage/' . $data->bukti) }}" class="img-fluid rounded mb-3"
                                        style="max-height:220px; object-fit:cover;">

                                    <button type="button"
                                        class="btn btn-success btn-sm no-loading d-flex align-items-center gap-2"
                                        onclick="downloadBukti('{{ asset('storage/' . $data->bukti) }}')">

                                        <i class="bi bi-download"></i>
                                        Download Bukti
                                    </button>
                                @else
                                    <p class="text-muted">Tidak ada bukti pembayaran</p>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('penyewa.riwayat.pembayaran') }}" class="btn btn-secondary px-4">
                        ← Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= PROFILE MODAL (FIX TAMBAH PROFIL TANPA HAPUS APA PUN) ================= --}}
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">

                <div class="fw-bold">{{ Auth::user()->name }}</div>
                <small class="text-muted">{{ Auth::user()->email }}</small>

                <hr>

                {{-- 🔥 TAMBAHAN (SEBELUMNYA TIDAK ADA) --}}
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

    <script>
        function downloadBukti(url) {
            const fileExtension = url.split('.').pop().split(/\#|\?/)[0];
            const a = document.createElement('a');
            a.href = url;
            a.download = 'bukti-pembayaran.' + fileExtension;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
@endsection
