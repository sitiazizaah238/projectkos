@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\PengajuanSewa;
    use App\Models\Pembayaran;
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::id();
    $kosIds = Kos::where('user_id', $userId)->pluck('id');

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
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.chat-icon-pemilik')
                @include('components.notif-pemilik')

                {{-- PROFILE --}}
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold small">
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

            {{-- CONTENT --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-1" style="font-size:28px;">
                    Laporan Keuangan
                </h3>

                <small class="text-muted d-block mb-4">
                    Rekapitulasi Data Pembayaran Penyewa
                </small>

                {{-- FILTER & EXPORT SEJARAH --}}
                <form method="GET" action="{{ route('pemilik.laporan.index') }}" id="filterForm" class="mb-3">
              <div class="row g-2 align-items-end">
                        {{-- SEARCH DENGAN ICON --}}
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control border-secondary rounded-start"
                                    placeholder="Cari penyewa / kamar..." value="{{ request('search') }}"
                                    onkeyup="if(event.keyCode === 13){ this.form.submit(); }">
                                <button type="submit" class="btn btn-secondary rounded-end">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="dari" class="form-label small text-muted mb-0">Dari Tanggal</label>
                            <input type="date" name="dari" id="dari" class="form-control">
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="sampai" class="form-label small text-muted mb-0">Sampai Tanggal</label>
                            <input type="date" name="sampai" id="sampai" class="form-control">
                        </div>

                        {{-- EXPORT PDF & EXCEL --}}
                        <div class="col-12 col-md-4 d-flex flex-wrap justify-content-md-end gap-2">
                            <a href="{{ route('pemilik.laporan.print', request()->query()) }}"
                                class="btn btn-danger flex-fill flex-md-grow-0" target="_blank" data-no-loader>
                                <i class="bi bi-file-pdf me-1"></i> Export PDF
                            </a>

                            <a href="{{ route('pemilik.laporan.excel', request()->query()) }}"
                                class="btn btn-success flex-fill flex-md-grow-0" target="_blank" data-no-loader>
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </div>

                    </div>
                </form>


                {{-- TABLE --}}
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-dark text-white">
                        Data Laporan Pembayaran
                    </div>

                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table table-bordered mb-0 text-center align-middle" style="min-width:800px;">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penyewa</th>
                                    <th>Nama Kamar</th>
                                    <th>Durasi</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($laporan as $item)
                                    <tr>
                                        <td>{{ $laporan->firstItem() + $loop->index }}</td>
                                        <td>{{ optional($item->pengajuan->penyewa)->name }}</td>
                                        <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                                        <td>
                                            {{ PengajuanSewa::formatDurasiByTipe((int) ($item->durasi_tagihan ?? 1), optional($item->pengajuan->kamar)->tipe_harga) }}
                                        </td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>{{ $item->metode->nama_metode }}</td>
                                        <td class="fw-semibold text-success">
                                            Rp {{ number_format($item->nominal_tagihan ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum ada data pembayaran</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if ($laporan->count())
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="6" class="text-end">
                                            Total Halaman Ini
                                        </td>
                                        <td class="text-success">
                                            Rp
                                            {{ number_format($laporan->sum(fn($i) => $i->nominal_tagihan ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif

                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $laporan->links() }}
                </div>

            </div>
        </div>
    </div>
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
