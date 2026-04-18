@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.notif-penyewa')
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

                <h3 class="fw-bold mb-1" style="font-size: 30px;">
                    Data Pembayaran
                </h3>

                <small class="text-muted d-block mb-4">
                    Verivikasi Kos / Data Pembayaran Kos
                </small>

                @php
                    $selectedPerPage = (int) request('per_page', 10);
                    if (!in_array($selectedPerPage, [5, 10], true)) {
                        $selectedPerPage = 10;
                    }
                @endphp

                <div class="d-flex justify-content-start mb-3 mt-2">
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <label for="per_page_pembayaran" class="small text-muted mb-0">Tampilkan</label>
                        <select id="per_page_pembayaran" name="per_page" class="form-select form-select-sm"
                            onchange="this.form.submit()" style="width:90px;">
                            <option value="5" {{ $selectedPerPage === 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $selectedPerPage === 10 ? 'selected' : '' }}>10</option>
                        </select>
                    </form>
                </div>

                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between">
                        <span>
                            <i class="bi bi-credit-card"></i> Data Pembayaran
                        </span>
                    </div>

                    @php
                        $kamarIdsPembayaran = $pembayaran->pluck('pengajuan.kamar_id')->filter()->unique();

                        $kamarTerisiMap = collect();

                        if ($kamarIdsPembayaran->isNotEmpty()) {
                            $kamarTerisiMap = \App\Models\PengajuanSewa::whereIn('kamar_id', $kamarIdsPembayaran)
                                ->whereIn('status', ['aktif', 'jatuh_tempo'])
                                ->pluck('kamar_id')
                                ->unique()
                                ->flip();
                        }
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kos</th>
                                    <th>Nama Kamar</th>
                                    <th>Total Bayar</th>
                                    <th>Durasi</th>
                                    <th>Status Pengajuan</th>
                                    <th>Status Kamar</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pembayaran as $item)
                                    @php
                                        $statusPengajuan = $item->pengajuan->statusSaatIni();
                                        $kamarSedangTerisi = isset($kamarTerisiMap[$item->pengajuan->kamar_id]);
                                    @endphp
                                    <tr>
                                        <td>{{ $pembayaran->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->pengajuan->kos->nama_kos }}</td>
                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                                        <td>Rp {{ number_format($item->nominal_tagihan ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $item->durasi_tagihan ?? 1 }} Bulan</td>

                                        <td>
                                            @if ($statusPengajuan == 'menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($statusPengajuan == 'disetujui')
                                                <span class="badge bg-primary">Disetujui</span>
                                            @elseif($statusPengajuan == 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($statusPengajuan == 'jatuh_tempo')
                                                <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                            @elseif($statusPengajuan == 'selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                            @elseif($statusPengajuan == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($statusPengajuan == 'aktif' || $statusPengajuan == 'jatuh_tempo' || $kamarSedangTerisi)
                                                <span class="badge bg-danger">Terisi</span>
                                            @elseif($statusPengajuan == 'selesai' || $statusPengajuan == 'disetujui')
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>

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
                                            @php
                                                $aksiModal = '#modalMenungguVerifikasi';

                                                if ($item->status == 'ditolak') {
                                                    $aksiModal = "#modalUlang{$item->id}";
                                                } elseif ($item->status == 'dikonfirmasi') {
                                                    $aksiModal = '#modalSudahDikonfirmasi';
                                                }
                                            @endphp

                                            <button
                                                class="btn btn-sm {{ $item->status == 'ditolak' ? 'btn-warning' : 'btn-secondary' }}"
                                                data-bs-toggle="modal" data-bs-target="{{ $aksiModal }}">
                                                {{ $item->status == 'ditolak' ? 'Ajukan Ulang' : 'Lihat Status' }}
                                            </button>
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
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            Tidak ada riwayat pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $pembayaran->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMenungguVerifikasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 p-4 text-center">
                <i class="bi bi-hourglass-split text-warning" style="font-size:60px;"></i>
                <h5 class="fw-bold text-warning mt-2">Menunggu Verifikasi</h5>
                <p class="mb-0">Pembayaran Anda sudah diterima sistem dan sedang diverifikasi pemilik kos.</p>
                <button class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSudahDikonfirmasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 p-4 text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size:60px;"></i>
                <h5 class="fw-bold text-success mt-2">Pembayaran Sudah Dikonfirmasi</h5>
                <p class="mb-0">Pembayaran ini telah dikonfirmasi. Anda tidak perlu mengirim ulang bukti pembayaran.</p>
                <button class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
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
