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

    @include('components.sidebar-pemilik')

    <div class="flex-grow-1">

        {{-- TOPBAR --}}
       <div class="topbar d-flex justify-content-end align-items-center px-3 px-md-4 gap-2 flex-wrap">

    {{-- NOTIF + PROFILE SEBELAHAN --}}
    <div class="d-flex align-items-center gap-2">

        {{-- NOTIF --}}
        <div>
            @include('components.notif-pemilik')
        </div>

        {{-- PROFILE --}}
        <button type="button"
            class="btn text-white d-flex align-items-center gap-2 p-1"
            data-bs-toggle="modal"
            data-bs-target="#profileModal">

            <span class="fw-semibold text-white small d-none d-sm-inline">
                {{ Auth::user()->name }}
            </span>

            @if (Auth::user()->photo)
                <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                    style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
            @else
                <i class="bi bi-person-circle fs-3"></i>
            @endif

        </button>

    </div>

</div>
        <div class="p-3 p-md-4" style="background:#f5f7fb; min-height:100vh;">

            {{-- TITLE --}}
            <h3 class="fw-bold mb-1" style="font-size: 26px;">
                Metode Pembayaran
            </h3>
            <small class="text-muted d-block mb-3">
                Pembayaran / Metode Bayar
            </small>

            {{-- BUTTON --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('pemilik.pembayaran.create') }}"
                   class="btn btn-primary btn-sm px-3">
                    + Tambah Metode
                </a>
            </div>

            {{-- TABLE CARD --}}
            <div class="card shadow-sm rounded-4">

                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="bi bi-credit-card me-2"></i>
                    <span class="fw-semibold">Metode Pembayaran</span>
                </div>

                {{-- TABLE WRAPPER FIX --}}
                <div class="table-responsive w-100">

                    <table class="table table-bordered mb-0 text-nowrap align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Metode</th>
                                <th>Atas Nama</th>
                                <th>No Rekening</th>
                                <th>Gambar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($metode as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_metode }}</td>
                                    <td>{{ $item->atas_nama }}</td>
                                    <td>{{ $item->no_rekening ?? '-' }}</td>

                                    <td>
                                        @if ($item->gambar)
                                            <img src="{{ asset('storage/' . $item->gambar) }}"
                                                 style="width:40px;height:40px;border-radius:6px;object-fit:cover;">
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if ($item->status == 'aktif')
                                            <span class="text-success">Aktif</span>
                                        @else
                                            <span class="text-danger">Non-Aktif</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">

                                            <a href="{{ route('pemilik.pembayaran.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('pemilik.pembayaran.destroy', $item->id) }}"
                                                  method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                        class="btn btn-danger btn-sm btn-delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">
                                        Belum ada metode pembayaran
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

{{-- MODAL --}}
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

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            let form = this.closest('.form-delete');

            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

});
</script>

{{-- RESPONSIVE FIX ONLY --}}
<style>
@media (max-width: 768px) {

    .topbar{
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px;
    }

    h3{
        font-size: 20px !important;
    }

    .btn-primary{
        font-size: 12px;
        padding: 6px 10px;
    }

    .table-responsive{
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table{
        min-width: 700px;
        font-size: 12px;
    }

    th, td{
        padding: 6px !important;
        white-space: nowrap;
    }

    img{
        width: 35px !important;
        height: 35px !important;
    }

    .btn-sm{
        font-size: 11px;
        padding: 3px 6px;
    }
}
</style>

@endsection
