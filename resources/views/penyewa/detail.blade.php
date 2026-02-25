@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
   {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                @php
                    $userId = Auth::id();

                    $notifPengajuan = \App\Models\PengajuanSewa::where('user_id', $userId)
                        ->where('status', 'disetujui')
                        ->latest()
                        ->get();

                    $notifPengajuanUnread = \App\Models\PengajuanSewa::where('user_id', $userId)
                        ->where('status', 'disetujui')
                        ->where('status_notif', 0)
                        ->count();

                    $notifPembayaran = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                        ->whereIn('status', ['dikonfirmasi', 'ditolak'])
                        ->latest()
                        ->get();

                    $notifPembayaranUnread = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                        ->whereIn('status', ['dikonfirmasi', 'ditolak'])
                        ->where('status_notif', 0)
                        ->count();

                    $totalNotif = $notifPengajuanUnread + $notifPembayaranUnread;
                @endphp
                {{-- 🔔 NOTIFIKASI --}}
                <div class="dropdown me-3">

                    <button class="btn position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-4 text-white"></i>

                        @if ($totalNotif > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalNotif }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow" style="width:300px">

                        <h6 class="dropdown-header">Notifikasi</h6>

                        {{-- NOTIF PENGAJUAN --}}
                        @foreach ($notifPengajuan as $p)
                            <a href="{{ route('penyewa.notif.pengajuan', $p->id) }}" class="dropdown-item small py-2">
                                <strong>Pengajuan Disetujui</strong><br>
                                Kos <strong>{{ $p->nama_kos }}</strong> telah disetujui Pemilik
                            </a>
                        @endforeach

                        {{-- NOTIF PEMBAYARAN --}}
                        @foreach ($notifPembayaran as $pb)
                            <a href="{{ route('penyewa.notif.pembayaran', $pb->id) }}" class="dropdown-item small py-2">
                                <strong>Status Pembayaran</strong><br>
                                Pembayaran kos <strong>{{ $pb->nama_kos }}</strong>
                                {{ $pb->status }}
                            </a>
                        @endforeach
                        {{-- TAMPILKAN JIKA SEMUA NOTIF KOSONG --}}
                        @if ($notifPengajuan->isEmpty() && $notifPembayaran->isEmpty())
                            <li class="dropdown-item text-muted small">
                                Tidak ada notifikasi
                            </li>
                        @endif

                    </ul>
                </div>
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

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                {{-- ================= HEADER ================= --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold mb-0">{{ $kos->nama_kos }}</h2>

                    @php
                        $total = $kos->kamars()->count();
                        $tersedia = $kos->kamars()->where('status', 'tersedia')->count();
                        $terisi = $kos->kamars()->where('status', '!=', 'tersedia')->count();
                    @endphp

                    <span class="badge bg-secondary p-2">
                        Total Kamar : {{ $total }}
                        Terisi : {{ $terisi }}
                        Tersedia : {{ $tersedia }}
                    </span>
                </div>

                {{-- ================= FOTO + INFO ================= --}}
                <div class="row g-4">

                    {{-- FOTO --}}
                    <div class="col-md-7">
                        @if ($kos->foto)
                            <img src="{{ asset('storage/' . $kos->foto) }}" class="img-fluid rounded-4 shadow-sm"
                                style="height:280px; object-fit:cover; width:100%;">
                        @endif
                    </div>

                    {{-- INFORMASI KOS --}}
                    <div class="col-md-5">
                        <div class="card shadow-sm rounded-4 p-3 h-100">

                            <h6 class="fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle"></i> Informasi Kos
                            </h6>

                            <div class="row mb-2">
                                <div class="col-6 text-muted">Nama Kos</div>
                                <div class="col-6 fw-semibold text-end">
                                    {{ $kos->nama_kos }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Pemilik Kos</div>
                                <div class="col-6 fw-semibold text-end">
                                    {{ $kos->user->name ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Lokasi Kos</div>
                                <div class="col-6 fw-semibold text-end">
                                    {{ $kos->lokasi }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6 text-muted">Tipe Kos</div>
                                <div class="col-6 fw-semibold text-end">
                                    {{ $kos->tipe_kos }}
                                </div>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">Deskripsi</small>
                                <p class="mb-0">{{ $kos->deskripsi }}</p>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ================= FASILITAS KOS ================= --}}
                @if ($kos->fasilitas)
                    <div class="card mt-4 shadow-sm rounded-4 p-3">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-house-door"></i> Fasilitas Kos
                        </h6>

                        <div class="row">
                            @foreach ($kos->fasilitas as $f)
                                <div class="col-md-3 mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    {{ $f }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ================= DAFTAR KAMAR ================= --}}
                <div class="card mt-4 shadow-sm rounded-4 p-4 border-0">

                    <h5 class="fw-bold mb-2">
                        <i class="bi bi-door-open"></i> Daftar Kamar yang tersedia
                    </h5>

                    <hr class="my-2">
                    <div class="row g-4">
                        @php
                            $pengajuanUser = \App\Models\PengajuanSewa::where('user_id', Auth::id())->get();
                        @endphp
                        @forelse($kos->kamars as $kamar)
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden d-flex flex-column">

                                    {{-- FOTO --}}
                                    @php
                                        $fotoKamar = is_array($kamar->foto) ? $kamar->foto : [];
                                        $fotoUtama = count($fotoKamar) > 0 ? $fotoKamar[0] : null;
                                    @endphp

                                    @if ($fotoUtama)
                                        <img src="{{ asset('storage/' . $fotoUtama) }}"
                                            style="height:200px; object-fit:cover; width:100%;">
                                    @else
                                        <div class="bg-light d-flex justify-content-center align-items-center"
                                            style="height:200px;">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">

                                        <div class="mb-2">
                                            <h5 class="fw-bold mb-1">
                                                {{ $kamar->nama_kamar }}
                                            </h5>

                                            <div class="text-primary fw-bold">
                                                Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                                <span class="text-muted fw-normal">/ bulan</span>
                                            </div>
                                        </div>

                                        {{-- Fasilitas --}}
                                        <div class="small text-muted mb-3" style="min-height:48px;">
                                            @if ($kamar->fasilitas)
                                                {{ implode(', ', $kamar->fasilitas) }}
                                            @endif
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            @if ($kamar->status == 'tersedia')
                                                <span class="badge bg-success px-3 py-2">
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">
                                                    Terisi
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Tombol selalu di bawah --}}
                                        <div class="mt-auto">

                                            @php
                                                $sudahAjukan = $pengajuanUser
                                                    ->where('kamar_id', $kamar->id)
                                                    ->whereIn('status', ['menunggu', 'disetujui'])
                                                    ->first();
                                            @endphp

                                            @if ($kamar->status == 'tersedia' && !$sudahAjukan)
                                                <button class="btn btn-primary rounded-pill w-100 py-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#ajukanModal{{ $kamar->id }}">
                                                    Ajukan Sewa
                                                </button>
                                            @elseif($sudahAjukan)
                                                <button class="btn btn-warning rounded-pill w-100 py-2" disabled>
                                                    Sudah Diajukan
                                                </button>
                                            @else
                                                <button class="btn btn-secondary rounded-pill w-100 py-2" disabled>
                                                    Tidak Tersedia
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= MODAL DI DALAM LOOP ================= --}}
                            @if ($kamar->status == 'tersedia')
                                <div class="modal fade" id="ajukanModal{{ $kamar->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content rounded-4 p-4">

                                            <h4 class="fw-bold mb-3">Form Pengajuan Sewa</h4>

                                            <form action="{{ route('penyewa.pengajuan.store') }}" method="POST">
                                                @csrf

                                                <input type="hidden" name="kos_id" value="{{ $kos->id }}">
                                                <input type="hidden" name="kamar_id" value="{{ $kamar->id }}">

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="fw-semibold">Nama Penyewa</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ Auth::user()->name }}" readonly>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="fw-semibold">Email</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ Auth::user()->email }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="fw-semibold">Nama Kos</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $kos->nama_kos }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="fw-semibold">Nama Kamar</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $kamar->nama_kamar }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="fw-semibold">Tanggal Mulai</label>
                                                    <input type="date" name="tanggal_mulai" class="form-control"
                                                        required>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="fw-semibold">Durasi Sewa</label>
                                                    <select name="durasi" class="form-select" required>
                                                        <option value="1">1 Bulan</option>
                                                        <option value="2">2 Bulan</option>
                                                        <option value="3">3 Bulan</option>
                                                        <option value="6">6 Bulan</option>
                                                        <option value="12">12 Bulan</option>
                                                    </select>
                                                </div>

                                                <div class="text-end">
                                                    <form action="{{ route('penyewa.pengajuan.store') }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                            Ajukan Penyewaan
                                                        </button>
                                                    </form>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">
                                    Tidak ada kamar tersedia
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        </table>
                    </div>

                </div>

                {{-- ================= BUTTON KEMBALI ================= --}}
                <div class="text-end mt-4">
                    <a href="{{ route('penyewa.cari.kos') }}" class="btn btn-primary rounded-pill px-4">
                        ← Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>


@endsection
