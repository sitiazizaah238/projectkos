@extends('layouts.app')
@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
    use App\Models\Pembayaran;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $userId = Auth::id();

    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $totalKos = Kos::where('user_id', $userId)->count();
    $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
    $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
    $totalPenyewa = PengajuanSewa::whereIn('kos_id', $kosIds)->where('status', 'aktif')->count();

    // =====================
    // NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();
    $notifPembayaran = Pembayaran::whereHas('pengajuan.kos', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->where('status', 'menunggu')
        ->latest()
        ->get();
    $jumlahNotif = $notifKos->count() + $notifPengajuan->count() + $notifPembayaran->count();
@endphp

@section('content')
    <div class="d-flex flex-column flex-md-row">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-3 px-md-4 gap-2">

                {{-- GROUP KANAN --}}
                <div class="d-flex align-items-center gap-2"></div>
                @include('components.notif-pemilik')
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold text-white small">
                        {{ Auth::user()->name }}
                    </span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="
                    width:35px;
                    height:35px;
                    min-width:35px;
                    min-height:35px;
                    border-radius:50%;
                    object-fit:cover;
                ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>
            </div>

            {{-- CONTENT --}}
            <div class="p-4">

                {{-- PAGE TITLE --}}
                <div class="mb-2">
                    <h3 class="fw-bold" style="font-size:25px;">
                        Kelola Data Kamar</h3>
                    <small class="text-muted">Manajemen Kos / Data Kamar Kos</small>
                </div>
                {{-- SEARCH + TAMBAH (MOBILE) --}}
                <div class="d-md-none mb-3">

                    {{-- SEARCH --}}
                    <form method="GET" action="{{ route('pemilik.kamar.index') }}" class="mb-2">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari nama kamar / kos...">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    {{-- TOMBOL TAMBAH --}}
                    <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-primary d-block w-100 py-2">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kamar
                    </a>

                </div>
                {{-- TOMBOL TAMBAH DESKTOP --}}
                <div class="d-none d-md-flex justify-content-end mb-2">
                    <a href="{{ route('pemilik.kamar.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kamar
                    </a>
                </div>

                <div class="card shadow-sm">

                    {{-- HEADER --}}
                    <div
                        class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-door-open fs-5 me-2"></i>
                            <span class="fw-semibold">Manajemen Data Kamar</span>
                        </div>

                        <form method="GET" action="{{ route('pemilik.kamar.index') }}" class="d-none d-md-block">
                            <div class="input-group w-100 w-md-auto" style="max-width:250px;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari nama kamar / kos...">

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                    </div>

                    {{-- BODY --}}
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Kos</th>
                                        <th>Nama Kamar</th>
                                        <th>Harga</th>
                                        <th>Tipe Harga</th>
                                        <th>Status Kamar</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($kamars as $k)
                                        <tr>
                                            <td>{{ ($kamars->currentPage() - 1) * $kamars->perPage() + $loop->iteration }}
                                            </td>
                                            <td>{{ $k->kos->nama_kos }}</td>
                                            <td>{{ $k->nama_kamar }}</td>
                                            <td>
                                                Rp. {{ number_format($k->harga, 0, ',', '.') }}
                                            </td>

                                            <td>{{ ucfirst($k->tipe_harga) }}</td>

                                            <td>
                                                @if ($k->status == 'tersedia')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Terisi</span>
                                                @endif
                                            </td>

                                            <td class="text-nowrap">
                                                <div class="d-flex gap-1 flex-nowrap">

                                                    {{-- LIHAT --}}
                                                    <a href="{{ route('pemilik.kamar.show', $k->id) }}"
                                                        class="btn btn-sm btn-info text-white">
                                                        Detail
                                                    </a>

                                                    {{-- EDIT --}}
                                                    <a href="{{ route('pemilik.kamar.edit', $k->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Edit
                                                    </a>

                                                    {{-- HAPUS --}}
                                                    <form action="{{ route('pemilik.kamar.destroy', $k->id) }}"
                                                        method="POST" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-status="{{ $k->status }}">
                                                            Hapus
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                @if (request('search'))
                                                    Data kamar atau nama kos tidak ditemukan
                                                @else
                                                    Belum ada data kamar
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>
                        </div>
                        @if ($kamars->hasPages())
                            <div class="p-3 d-flex justify-content-end">
                                {{ $kamars->links() }}
                            </div>
                        @endif
                        <style>
                            /* ============================= */
                            /* PAGINATION FIX TOTAL (ANTI PUTIH) */
                            /* ============================= */

                            .pagination {
                                gap: 4px;
                            }

                            /* semua item pagination */
                            .pagination li {
                                list-style: none;
                            }

                            /* SEMUA LINK / SPAN pagination */
                            .pagination .page-link,
                            .pagination .page-item span,
                            .pagination .page-item a {
                                color: #0d6efd !important;
                                background-color: #fff !important;
                                border: 1px solid #dee2e6 !important;
                                padding: 6px 12px !important;
                                display: block;
                            }

                            /* hover */
                            .pagination .page-link:hover,
                            .pagination .page-item a:hover {
                                background-color: #0b5ed7 !important;
                                color: #fff !important;
                            }

                            /* active */
                            .pagination .page-item.active .page-link {
                                background-color: #0d6efd !important;
                                border-color: #0d6efd !important;
                                color: #fff !important;
                            }

                            /* disabled (NEXT / PREV FIX UTAMA) */
                            .pagination .page-item.disabled span {
                                color: #6c757d !important;
                                background-color: #e9ecef !important;
                                border-color: #dee2e6 !important;
                                opacity: 1 !important;
                                cursor: not-allowed !important;
                            }

                            /* kalau Next/Prev masih <a> bukan span */
                            .pagination .page-item.disabled a {
                                color: #6c757d !important;
                                background-color: #e9ecef !important;
                                border-color: #dee2e6 !important;
                                pointer-events: none !important;
                                opacity: 1 !important;
                            }

                            /* fokus mobile */
                            .pagination .page-link:focus {
                                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25) !important;
                            }

                            .pagination * {
                                background: #fff !important;
                                color: #0d6efd !important;
                            }

                            /* ============================= */
                            /* RESPONSIVE MOBILE FIX */
                            /* ============================= */
                            @media (max-width: 768px) {

                                /* padding konten biar ga sempit */
                                .p-4 {
                                    padding: 15px !important;
                                }

                                /* topbar biar ga nabrak */
                                .topbar {
                                    flex-wrap: wrap;
                                    gap: 8px;
                                }

                                .topbar {
                                    overflow-x: auto;
                                }

                                /* header card (title + search) */
                                .card-header {
                                    flex-direction: column !important;
                                    align-items: stretch !important;
                                    gap: 10px;
                                }

                                /* search full width */
                                .card-header form {
                                    width: 100%;
                                }

                                .card-header .input-group {
                                    width: 100% !important;
                                    max-width: 100% !important;
                                }

                                /* tabel lebih kecil */
                                table {
                                    font-size: 13px;
                                }

                                td .d-flex {
                                    flex-wrap: nowrap;
                                }

                                td .btn {
                                    white-space: nowrap;
                                }
                            }

                            @media (max-width:768px) {
                                .btn-primary {
                                    width: auto !important;
                                }
                            }

                            @media (max-width:768px) {
                                .btn-primary {
                                    width: auto !important;
                                }
                            }

                            @media (max-width:768px) {
                                .topbar {
                                    padding-top: 10px;
                                    padding-bottom: 10px;
                                }
                            }

                            @media (max-width: 768px) {

                                /* bikin tinggi search & tombol sama */
                                .d-md-none .input-group .form-control {
                                    height: 45px;
                                }

                                .d-md-none .input-group .btn {
                                    height: 45px;
                                }

                                /* tombol tambah biar kotak & sejajar */
                                .d-md-none a.btn-primary {
                                    height: 45px;
                                    min-width: 50px;
                                    padding: 0;
                                }

                                .pagination .page-item.disabled .page-link {
                                    color: #333 !important;
                                    background-color: #e9ecef !important;
                                    opacity: 1 !important;
                                }
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SWEET ALERT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-delete").forEach(btn => {

                btn.addEventListener("click", function() {

                    let status = this.dataset.status;
                    let form = this.closest("form");

                    // Kalau kamar TERISI
                    if (status === 'terisi') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Bisa Dihapus!',
                            text: 'Kamar sedang terisi oleh penyewa.',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    // Kalau kamar TERSEDIA
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data kamar akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
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
@endsection
