@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\PengajuanSewa;
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::id();
    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();
@endphp

@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">

                {{-- NOTIF --}}
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

                        @foreach ($notifKos as $n)
                            <a href="{{ url('/notif/kos/' . $n->id) }}" class="dropdown-item small py-2">
                                <strong>Kos Disetujui</strong><br>
                                Kos <strong>{{ $n->nama_kos }}</strong> telah disetujui
                            </a>
                        @endforeach

                        @foreach ($notifPengajuan as $p)
                            <a href="{{ url('/notif/pengajuan/' . $p->id) }}" class="dropdown-item small py-2">
                                <strong>{{ $p->nama_penyewa }}</strong><br>
                                Mengajukan kos <strong>{{ $p->nama_kos }}</strong>
                            </a>
                        @endforeach

                        @if ($jumlahNotif == 0)
                            <div class="text-center text-muted small p-3">
                                Tidak ada notifikasi
                            </div>
                        @endif
                    </div>
                </div>

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
                        <div class="col-md-4">
                            <form method="GET" action="">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-secondary rounded-start"
                                        placeholder="Cari penyewa / kamar..." value="{{ request('search') }}"
                                        onkeyup="if(event.keyCode === 13){ this.form.submit(); }">
                                    <button type="submit" class="btn btn-secondary rounded-end">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- FILTER TANGGAL DENGAN LABEL --}}
                        <div class="col-md-3">
                            <label for="dari" class="form-label small text-muted">Tanggal</label>
                            <input type="date" name="dari" id="dari" class="form-control"
                                value="{{ request('dari') }}" onchange="this.form.submit()">
                        </div>

                        {{-- EXPORT PDF & EXCEL --}}
                        <div class="col-md-5 d-flex justify-content-end gap-2">
                            <a href="{{ route('pemilik.laporan.print') }}" class="btn btn-danger" target="_blank"
                                data-no-loader>
                                <i class="bi bi-file-pdf me-1"></i> Export PDF
                            </a>

                            <a href="{{ route('pemilik.laporan.excel') }}" class="btn btn-success" target="_blank"
                                data-no-loader>
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

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center">

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
                                        <td>{{ $item->pengajuan->durasi }} Bulan</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>{{ $item->metode->nama_metode }}</td>
                                        <td class="fw-semibold text-success">
                                            Rp {{ number_format($item->pengajuan->total_bayar, 0, ',', '.') }}
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
                                            {{ number_format($laporan->sum(fn($i) => $i->pengajuan->total_bayar), 0, ',', '.') }}
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
@endsection
