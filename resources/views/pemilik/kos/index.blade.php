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
    // 🔔 NOTIF SECTION
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
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                <div class="dropdown position-relative">

                    <button class="btn text-white position-relative" data-bs-toggle="dropdown">

                        <i class="bi bi-bell fs-4"></i>

                        @if ($jumlahNotif > 0)
                            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
                                style="top:10px; font-size:10px;">
                                {{ $jumlahNotif }}
                            </span>
                        @endif

                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-2" style="width:320px; max-height:300px; overflow-y:auto;">

                        <h6 class="dropdown-header">Notifikasi</h6>

                        {{-- NOTIF KOS DISETUJUI --}}
                        @foreach ($notifKos as $n)
                            <a href="{{ url('/notif/kos/' . $n->id) }}" class="dropdown-item small py-2">
                                <strong>Kos Disetujui</strong><br>
                                Kos <strong>{{ $n->nama_kos }}</strong> telah disetujui admin
                            </a>
                        @endforeach

                        {{-- NOTIF PENGAJUAN --}}
                        @foreach ($notifPengajuan as $p)
                            <a href="{{ url('/notif/pengajuan/' . $p->id) }}" class="dropdown-item small py-2">
                                <strong>{{ $p->nama_penyewa }}</strong><br>
                                Mengajukan kos <strong>{{ $p->nama_kos }}</strong>
                            </a>
                        @endforeach
                        {{-- NOTIF PEMBAYARAN --}}
                        @foreach ($notifPembayaran as $pb)
                            <a href="{{ url('/pemilik/verifikasi') }}" class="dropdown-item small py-2">
                                <strong>Pembayaran Baru</strong><br>
                                Penyewa <strong>{{ optional($pb->pengajuan->penyewa)->name }}</strong>
                                mengirim pembayaran kos <strong>{{ $pb->pengajuan->kos->nama_kos }}</strong>
                            </a>
                        @endforeach
                        @if ($jumlahNotif == 0)
                            <div class="text-center text-muted small p-3">
                                Tidak ada notifikasi
                            </div>
                        @endif

                    </div>
                </div>
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
                        Manajemen Kos</h3>
                    <small class="text-muted">Manajemen Kos / Data Kos</small>
                </div>

                {{-- TOMBOL TAMBAH (KANAN + ICON) --}}
                <div class="d-flex justify-content-end mb-2">
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
                        <table class="table table-bordered mb-0">


                            {{-- HEAD --}}
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="100">Foto Kos</th>
                                    <th>Nama Kos</th>
                                    <th>Lokasi Kos</th>
                                    <th>Tipe Kos</th>
                                    <th width="120">Status</th>
                                    <th>Alasan</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>

                            {{-- BODY --}}
                            <tbody>
                                @forelse($kos as $k)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>



                                        {{-- FOTO --}}
                                        <td style="width:110px;"> {{-- kolom dikit dilebarkan --}}
                                            @if ($k->foto && count($k->foto) > 0)
                                                <img src="{{ asset('storage/' . $k->foto[0]) }}"
                                                    style="width:80px; height:60px; border-radius:8px; object-fit:cover;">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $k->nama_kos }}</td>
                                        <td>{{ $k->lokasi }}</td>
                                        <td>{{ $k->tipe_kos }}</td>

                                        {{-- STATUS (badge kaya admin) --}}
                                        <td>
                                            @if ($k->status == 'disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($k->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                            @endif
                                        </td>

                                        <td>{{ $k->alasan ?? '-' }}</td>

                                        {{-- AKSI --}}
                                        <td>

                                            <a href="{{ route('pemilik.kos.show', $k->id) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <a href="{{ route('pemilik.kos.edit', $k->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <form action="{{ route('pemilik.kos.destroy', $k->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>

                                        </td>

                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
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
