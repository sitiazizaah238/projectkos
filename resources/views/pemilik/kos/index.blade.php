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

    $notifKos = Kos::where('user_id', $userId)
        ->where('is_read', false)
        ->where(function ($query) {
            $query
                ->whereIn('status', ['disetujui', 'nonaktif'])
                ->orWhere('edit_request_status', 'ditolak')
                ->orWhere('edit_request_status', 'disetujui');
        })
        ->latest()
        ->get();

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
    <style>
        /* ================= RESPONSIVE MOBILE FIX (DATA KOS) ================= */
        @media (max-width: 768px) {

            /* ===== LAYOUT ===== */
            .d-flex {
                flex-wrap: wrap !important;
            }

            .sidebar {
                position: fixed !important;
                z-index: 1050;
            }

            .flex-grow-1 {
                width: 100% !important;
            }

            .p-4 {
                padding: 15px !important;
            }

            /* ===== TOPBAR ===== */
            .topbar {
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center;
                gap: 8px;
                flex-wrap: nowrap !important;
            }

            .topbar span {
                font-size: 12px;
                max-width: 90px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* ===== SEARCH ===== */
            .input-group {
                width: 100% !important;
                max-width: 100% !important;
            }

            /* ===== TABLE (WAJIB BANGET) ===== */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border-radius: 12px;
            }

            .table {
                min-width: 1000px;
                /* karena kolom banyak */
            }

            .table th,
            .table td {
                white-space: nowrap;
                font-size: 12px !important;
                padding: 8px !important;
                vertical-align: middle;
            }

            .table thead th {
                position: sticky;
                top: 0;
                background: #f8f9fa;
                z-index: 2;
            }

            /* ===== BUTTON ===== */
            .btn {
                font-size: 12px;
                padding: 5px 8px;
            }

            /* tombol aksi biar ga kepanjangan */
            .btn-sm {
                padding: 4px 6px;
            }

            /* ===== TITLE ===== */
            h3 {
                font-size: 20px !important;
            }

            /* ===== MODAL ===== */
            .modal-dialog {
                margin: 10px;
            }
        }

        .card-header {
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-header form {
            width: 100%;
        }

        @media (min-width: 769px) {
            .card-header form {
                width: auto;
            }
        }

        /* ===== SEARCH POSITION FIX ===== */
        .search-mobile {
            display: none;
        }

        @media (max-width: 768px) {

            /* tampilkan search luar */
            .search-mobile {
                display: block;
                width: 100%;
            }

            .search-mobile .input-group {
                width: 100%;
            }

            /* sembunyikan search di dalam card */
            .card-header form {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }
        }
    </style>
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
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
                        Kelola Data Kos</h3>
                    <small class="text-muted"> Manajemen Kos / Data Kos</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">

                    {{-- KIRI (SEARCH MOBILE) --}}
                    <form action="{{ route('pemilik.kos.index') }}" method="GET" class="search-mobile">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari kos...">
                            <button class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    {{-- KANAN (TOMBOL TAMBAH) --}}
                    <a href="{{ route('pemilik.kos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kos
                    </a>

                </div>


                {{-- CARD --}}
                <div class="card shadow-sm">

                    {{-- HEADER --}}
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">
                            <i class="bi bi-building fs-5 me-2"></i>
                            <span class="fw-semibold">Manajemen Data Kos</span>
                        </div>

                        {{-- SEARCH (CUMA UI, BELUM FUNCTION) --}}
                        <form action="{{ route('pemilik.kos.index') }}" method="GET">
                            <div class="input-group" style="width:250px;">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari nama/lokasi/tipe...">
                                <button class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- BODY --}}
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                {{-- HEAD --}}
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>

                                        <th>Nama Kos</th>
                                        <th>Lokasi Kos</th>
                                        <th>Tipe Kos</th>
                                        <th width="120">Status Verifikasi</th>
                                        <th width="150">Status Perubahan Data</th>
                                        <th>Alasan Penolakan</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                </thead>

                                {{-- BODY --}}
                                <tbody>
                                    @forelse($kos as $k)
                                        <tr>

                                            <td>{{ $loop->iteration }}</td>


                                            <td>{{ $k->nama_kos }}</td>
                                            <td>{{ $k->lokasi }}</td>
                                            <td>{{ $k->tipe_kos }}</td>

                                            {{-- STATUS (badge kaya admin) --}}
                                            <td>
                                                @if ($k->status == 'disetujui')
                                                    <span class="badge bg-success">Disetujui admin</span>
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
                                                    <span class="badge bg-warning text-dark">Menunggu Admin</span>
                                                @elseif($k->edit_request_status === 'disetujui')
                                                    <span class="badge bg-success">Disetujui </span>
                                                @elseif($k->edit_request_status === 'ditolak')
                                                    <span class="badge bg-danger">Ditolak Admin</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Ada Perubahan</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($k->status == 'ditolak' || $k->edit_request_status == 'ditolak')
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#modalAlasan{{ $k->id }}">
                                                        Lihat
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- AKSI --}}
                                            <td>

                                                {{-- LIHAT --}}
                                                <a href="{{ route('pemilik.kos.show', $k->id) }}"
                                                    class="btn btn-sm btn-info text-white">
                                                    Detail
                                                </a>

                                                {{-- EDIT --}}
                                                @if ($k->edit_request_status === 'menunggu')
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        title="Menunggu persetujuan admin">
                                                        Edit
                                                    </button>
                                                @else
                                                    <a href="{{ route('pemilik.kos.edit', $k->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Edit
                                                    </a>
                                                @endif

                                                {{-- HAPUS --}}
                                                <form action="{{ route('pemilik.kos.destroy', $k->id) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                        Hapus
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                        <!-- MODAL ALASAN (VERSI PROFESSIONAL) -->
                                        <div class="modal fade" id="modalAlasan{{ $k->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content shadow-lg border-0"
                                                    style="border-radius:18px; overflow:hidden;">

                                                    <!-- HEADER -->
                                                    <div class="modal-header border-0 text-white"
                                                        style="background: linear-gradient(135deg, #ef4444, #dc2626);">

                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                                            <h5 class="modal-title mb-0 fw-semibold">
                                                                Alasan Penolakan
                                                            </h5>
                                                        </div>

                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <!-- BODY -->
                                                    <div class="modal-body p-4">

                                                        <div class="p-3 rounded-3"
                                                            style="background:#f9fafb; border:1px solid #e5e7eb;">

                                                            @if ($k->status == 'ditolak' && $k->alasan)
                                                                <div class="text-dark" style="line-height:1.6;">
                                                                    {{ $k->alasan }}
                                                                </div>
                                                            @elseif($k->edit_request_status == 'ditolak' && $k->edit_request_alasan)
                                                                <div class="text-dark" style="line-height:1.6;">
                                                                    {{ $k->edit_request_alasan }}
                                                                </div>
                                                            @else
                                                                <div class="text-muted text-center">
                                                                    Tidak ada alasan diberikan
                                                                </div>
                                                            @endif

                                                        </div>

                                                    </div>

                                                    <!-- FOOTER -->
                                                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                        <button type="button" class="btn btn-secondary px-4"
                                                            data-bs-dismiss="modal">
                                                            Tutup
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    Belum ada data kos
                                                </div>
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


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".btn-delete").forEach(btn => {
                    btn.addEventListener("click", function() {
                        let form = this.closest("form");

                        Swal.fire({
                            title: 'Yakin hapus?',
                            text: "Data kos akan dihapus permanen!",
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
