@extends('layouts.app')
@php
    $fasilitasIcon = [
        'Parkir' => 'bi-p-circle',
        'Wifi' => 'bi-wifi',
        'CCTV' => 'bi-camera-video',
        'Dapur' => 'bi-cup-hot',
        'Musola' => 'bi-moon',
    ];
@endphp
@section('content')
    <style>
        .dashboard-card-modern {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .dashboard-card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .card-title-modern {
            font-weight: 600;
            font-size: 15px;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
        }

        .info-value {
            font-weight: 500;
            font-size: 14px;
            color: #2c3e50;
        }

        .badge-soft-success {
            background: #e6f4ea;
            color: #198754;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .table-modern td {
            padding: 6px 0;
            font-size: 14px;
        }

        .table-modern td:first-child {
            color: #6c757d;
            width: 40%;
        }
    </style>
    <div class="d-flex">

        {{-- SIDEBAR --}}
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

            {{-- ================= CONTENT ================= --}}
            <div class="p-4" style="background:#f5f7fb;min-height:100vh;">

                <h3 class="fw-bold" style="font-size: 30px;">Dashboard Penyewa</h3>
                <small class="text-muted">Halo! Selamat Datang {{ Auth::user()->name }}</small>
                @php
                    use App\Models\PengajuanSewa;
                    use App\Models\Pembayaran;

                    $userId = Auth::id();
                    // Ambil sewa terbaru yang masih aktif atau sudah selesai
                    $sewaAktifData = PengajuanSewa::with(['kos', 'kamar', 'pembayarans'])
                        ->where('user_id', $userId)
                        ->whereIn('status', ['aktif', 'selesai'])
                        ->latest()
                        ->first();

                    // Ambil pembayaran terakhir dari pengajuan yang tampil
                    $pembayaran = null;
                    if ($sewaAktifData) {
                        $pembayaran = Pembayaran::where('pengajuan_sewa_id', $sewaAktifData->id)
                            ->where('status', 'dikonfirmasi')
                            ->latest()
                            ->first();

                        $tanggalMulaiSewa = \Carbon\Carbon::parse($sewaAktifData->tanggal_mulai)->startOfDay();
                        $tanggalSelesaiSewa = $tanggalMulaiSewa
                            ->copy()
                            ->addMonths((int) $sewaAktifData->durasi)
                            ->endOfDay();

                        if (now()->startOfDay()->lt($tanggalMulaiSewa)) {
                            $bulanTagihanSaatIni = 1;
                        } else {
                            $bulanTagihanSaatIni = min(
                                $tanggalMulaiSewa->diffInMonths(now()->startOfDay()) + 1,
                                max((int) $sewaAktifData->durasi, 1),
                            );
                        }

                        $jumlahKonfirmasi = Pembayaran::where('pengajuan_sewa_id', $sewaAktifData->id)
                            ->where('status', 'dikonfirmasi')
                            ->count();

                        $statusTagihan = $sewaAktifData->statusSaatIni();
                        $jatuhTempoTagihan = $sewaAktifData->tanggalSelesai();
                    } else {
                        $statusTagihan = null;
                        $jatuhTempoTagihan = null;
                    }
                    // Total Kos yang pernah diajukan / disewa
                    $totalKos = PengajuanSewa::where('user_id', $userId)->count();

                    // Sewa Aktif (status disetujui / aktif)
                    $sewaAktif = PengajuanSewa::where('user_id', $userId)->where('status', 'aktif')->count();

                    // Menunggu Persetujuan
                    $menunggu = PengajuanSewa::where('user_id', $userId)->where('status', 'menunggu')->count();

                    $riwayat = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                        ->where('status', 'dikonfirmasi')
                        ->count();

                @endphp
                <style>
                    .stat-card {
                        transition: all 0.3s ease;
                    }

                    .stat-card:hover {
                        transform: translateY(-6px) scale(1.02);
                        filter: brightness(1.05);
                        box-shadow: 0 18px 35px rgba(0, 0, 0, 0.18) !important;
                    }
                </style>

                <div class="row mt-4 g-4">

                    {{-- TOTAL KOS --}}
                    <div class="col-md-3">
                        <a href="{{ route('penyewa.pengajuan.index') }}" class="text-decoration-none">
                            <div class="card stat-card border-0 rounded-4 text-white h-100"
                                style="background: linear-gradient(135deg, #3B82F6, #2563EB);
                       box-shadow: 0 10px 25px rgba(59,130,246,0.25);">
                                <div class="card-body px-4 py-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="fw-bold mb-1">{{ $totalKos }}</h2>
                                        <small class="fw-medium">Total Kos Disewa</small>
                                    </div>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:60px;height:60px;background:rgba(255,255,255,0.18);">
                                        <i class="bi bi-house-fill fs-4 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- SEWA AKTIF --}}
                    <div class="col-md-3">
                        <a href="{{ route('penyewa.pengajuan.index') }}" class="text-decoration-none">
                            <div class="card stat-card border-0 rounded-4 text-white h-100"
                                style="background: linear-gradient(135deg, #10B981, #059669);
                       box-shadow: 0 10px 25px rgba(16,185,129,0.25);">
                                <div class="card-body px-4 py-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="fw-bold mb-1">{{ $sewaAktif }}</h2>
                                        <small class="fw-medium">Sewa Aktif</small>
                                    </div>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:60px;height:60px;background:rgba(255,255,255,0.18);">
                                        <i class="bi bi-check-circle-fill fs-4 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- MENUNGGU --}}
                    <div class="col-md-3">
                        <a href="{{ route('penyewa.pengajuan.index') }}" class="text-decoration-none">
                            <div class="card stat-card border-0 rounded-4 text-white h-100"
                                style="background: linear-gradient(135deg, #F59E0B, #D97706);
                       box-shadow: 0 10px 25px rgba(245,158,11,0.25);">
                                <div class="card-body px-4 py-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="fw-bold mb-1">{{ $menunggu }}</h2>
                                        <small class="fw-medium">Menunggu Persetujuan</small>
                                    </div>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:60px;height:60px;background:rgba(255,255,255,0.18);">
                                        <i class="bi bi-hourglass-split fs-4 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- RIWAYAT --}}
                    <div class="col-md-3">
                        <a href="{{ route('penyewa.riwayat.pembayaran') }}" class="text-decoration-none">
                            <div class="card stat-card border-0 rounded-4 text-white h-100"
                                style="background: linear-gradient(135deg, #EF4444, #DC2626);
                       box-shadow: 0 10px 25px rgba(239,68,68,0.25);">
                                <div class="card-body px-4 py-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="fw-bold mb-1">{{ $riwayat }}</h2>
                                        <small class="fw-medium">Riwayat Pembayaran</small>
                                    </div>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:60px;height:60px;background:rgba(255,255,255,0.18);">
                                        <i class="bi bi-clock-history fs-4 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                {{-- ================= KOS SAAT INI + STATUS ================= --}}
                <div class="row mt-4 g-3 align-items-stretch">

                    {{-- KOS SAAT INI --}}
                    <div class="col-md-6 d-flex">
                        <div class="card dashboard-card-modern p-3 w-100 h-100">
                            <h6 class="card-title-modern">
                                <i class="bi bi-house-door-fill text-primary"></i>
                                Kos Saat Ini
                            </h6>
                            <hr class="my-2">
                            @if ($sewaAktifData)
                                @php
                                    $tanggalMulai = \Carbon\Carbon::parse($sewaAktifData->tanggal_mulai)->startOfDay();
                                    $jumlahBulan = (int) $sewaAktifData->durasi;

                                    $tanggalSelesai = $tanggalMulai->copy()->addMonths($jumlahBulan)->subDay(); // biar pas (misal 1 Jan - 1 Feb jadi 31 Jan)
                                    $statusSewaAktif = $statusTagihan;
                                @endphp
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><b>{{ $sewaAktifData->kos->nama_kos }}</b></p>

                                        <small>Kamar : {{ $sewaAktifData->kamar->nama_kamar }}</small><br>

                                        <small>
                                            Durasi Sewa :
                                            {{ \App\Models\PengajuanSewa::formatDurasiByTipe((int) $jumlahBulan, optional($sewaAktifData->kamar)->tipe_harga) }}
                                        </small>
                                        <br>
                                        <small>
                                            Status :
                                            @if ($statusSewaAktif === 'selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                            @elseif($statusSewaAktif === 'jatuh_tempo')
                                                <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                            @elseif($statusSewaAktif === 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    {{ ucfirst($statusSewaAktif) }}
                                                </span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small>Tanggal Sewa</small>
                                        <p>{{ $tanggalMulai->format('d-m-Y') }}</p>

                                        <small>Tanggal Selesai</small>
                                        <p>{{ $tanggalSelesai->format('d-m-Y') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    Belum ada kos aktif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STATUS PEMBAYARAN --}}
                    <div class="col-md-6 d-flex">
                        <div class="card dashboard-card-modern p-3 w-100 h-100">
                            <h6 class="card-title-modern">
                                <i class="bi bi-credit-card-2-front text-primary"></i>
                                Status Pembayaran
                            </h6>
                            <hr class="my-2">
                            <table class="table table-borderless mb-0">
                                @if ($statusTagihan === 'jatuh_tempo')
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Jatuh Tempo</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jatuh Tempo</td>
                                        <td>{{ $jatuhTempoTagihan?->format('d-m-Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tagihan</td>
                                        <td>Rp {{ number_format($sewaAktifData->kamar->harga ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @elseif ($pembayaran)
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <span class="badge bg-success">Lunas</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Metode</td>
                                        <td>
                                            @php
                                                $metode = is_string($pembayaran->metode)
                                                    ? json_decode($pembayaran->metode)
                                                    : $pembayaran->metode;
                                            @endphp

                                            {{ $metode->nama_metode ?? '-' }} - {{ $metode->no_rekening ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Terakhir Bayar</td>
                                        <td>{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d-m-Y') }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Belum ada pembayaran
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
@php
    $punyaRiwayat = \App\Models\PengajuanSewa::where('user_id', auth()->id())->exists();
@endphp
                {{-- ================= REKOMENDASI KOS ================= --}}
                <div class="card mt-4 p-3 shadow-sm">

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-star-fill"></i> Rekomendasi Kos Untuk Anda
                    </h6>

                    <div class="row g-4">
  {{-- ❗ KUNCI UTAMA: akun baru tidak tampilkan rekomendasi --}}
        @if(!$punyaRiwayat)

            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
                    <i class="bi bi-search fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Belum Ada Rekomendasi</h5>
                    <p class="text-muted">
                        Kami belum mengenal seleramu. Silakan gunakan fitur
                        <strong>"Cari Kos"</strong> untuk mulai mendapatkan rekomendasi.
                    </p>
                    <a href="{{ route('penyewa.cari.kos') }}"
                       class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-search me-1"></i> Mulai Mencari
                    </a>
                </div>
            </div>
                @else
                        {{-- Bagian Card Rekomendasi --}}
                        @forelse($rekomendasi as $k)
                            @php
                                $tipeIcon = match ($k->tipe_kos) {
                                    'putra' => 'bi-gender-male',
                                    'putri' => 'bi-gender-female',
                                    'campur' => 'bi-gender-ambiguous',
                                    default => 'bi-house',
                                };
                                $fasKos = is_array($k->fasilitas) ? $k->fasilitas : [];
                                $kamarTermurah = $k->kamars->sortBy('harga')->first();
                                $kamarTermahal = $k->kamars->sortBy('harga')->last();
                            @endphp
                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100" style="border-radius:16px; overflow:hidden;">

                                    {{-- Badge Skor AI --}}
                                    <div class="position-absolute p-2" style="z-index:1;">
                                        <span
                                            class="badge {{ $k->similarity_score >= 80 ? 'bg-success' : 'bg-primary' }} shadow">
                                            {{ round($k->similarity_score) }}% - {{ $k->label }}
                                        </span>
                                    </div>

                                    {{-- Foto --}}
                                    @php
                                        $fotos = is_array($k->foto) ? $k->foto : json_decode($k->foto, true);
                                    @endphp

                                    @if ($fotos && count($fotos) > 0)
                                        <div id="carouselKos{{ $k->id }}" class="carousel slide"
                                            data-bs-ride="carousel">
                                            <div class="carousel-inner">

                                                @foreach ($fotos as $index => $foto)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <img src="{{ asset('storage/' . $foto) }}" class="d-block w-100"
                                                            style="height:180px; object-fit:cover;">
                                                    </div>
                                                @endforeach

                                            </div>

                                            {{-- TOMBOL PREV --}}
                                            @if (count($fotos) > 1)
                                                <button class="carousel-control-prev" type="button"
                                                    data-bs-target="#carouselKos{{ $k->id }}"
                                                    data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </button>

                                                {{-- TOMBOL NEXT --}}
                                                <button class="carousel-control-next" type="button"
                                                    data-bs-target="#carouselKos{{ $k->id }}"
                                                    data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <div style="height:180px; background:#dee2e6;"
                                            class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-house fs-1 text-muted"></i>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">

                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                            <h6 class="fw-bold mb-0" style="min-width:0;">
                                                {{ $k->nama_kos }}</h6>
                                            <span class="badge bg-primary flex-shrink-0">
                                                <i class="bi {{ $tipeIcon }} me-1"></i>{{ ucfirst($k->tipe_kos) }}
                                            </span>
                                        </div>

                                        {{-- 2. Alamat --}}
                                        <small class="text-muted mb-2 d-block">
                                            <i class="bi bi-geo-alt"></i> {{ $k->lokasi }}
                                        </small>

                                        {{-- 3. Fasilitas Kos --}}
                                        @php
                                            $fasKos = is_array($k->fasilitas) ? $k->fasilitas : [];
                                        @endphp
                                        @if (count($fasKos) > 0)
                                            <div class="mb-2">
                                                <small class="text-muted fw-semibold">
                                                    <i class="bi bi-building me-1 text-primary"></i>Fasilitas:
                                                </small>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach ($fasKos as $fas)
                                                        <span class="badge text-dark d-flex align-items-center gap-1"
                                                            style="background: linear-gradient(135deg, #e8f4fd, #d1ecf1); border: 1px solid #bee5eb; font-weight:500;">
                                                            <i
                                                                class="bi {{ $fasilitasIcon[$fas] ?? 'bi-check-circle' }} text-primary"></i>
                                                            {{ $fas }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 4. Harga --}}
                                        @php
                                            $kamarTermurah = $k->kamars->sortBy('harga')->first();
                                            $kamarTermahal = $k->kamars->sortBy('harga')->last();
                                        @endphp
                                        <div class="mb-3">
                                            @if ($kamarTermurah)
                                                <small class="text-muted fw-semibold">
                                                    <i class="bi bi-tag me-1 text-primary"></i>Harga mulai
                                                    dari:
                                                </small>
                                                <div>
                                                    <span class="fw-bold text-dark">
                                                        Rp{{ number_format($kamarTermurah->harga, 0, ',', '.') }}
                                                    </span>
                                                    @if ($kamarTermahal && $kamarTermahal->harga != $kamarTermurah->harga)
                                                        <span class="text-muted"> – </span>
                                                        <span class="fw-bold text-dark">
                                                            Rp{{ number_format($kamarTermahal->harga, 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="text-muted">/{{ ucfirst($kamarTermurah->tipe_harga) }}</span>
                                                </div>
                                            @else
                                                <small class="text-muted">Belum ada kamar tersedia</small>
                                            @endif
                                        </div>
                                        {{-- 5. Tombol Detail --}}
                                        <div class="mt-auto">
                                            <a href="{{ route('penyewa.kos.detail', $k->id) }}"
                                                class="btn btn-primary w-100">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
                                    <i class="bi bi-search fs-1 text-primary mb-3"></i>
                                    <h5 class="fw-bold">Belum Ada Rekomendasi</h5>
                                    <p class="text-muted">
                                        Kami belum mengenal seleramu. Silakan gunakan fitur
                                        <strong>"Cari Kos"</strong> dan gunakan filter untuk membantu AI
                                        kami bekerja.
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('penyewa.cari.kos') }}"
                                            class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-search me-1"></i> Mulai Mencari Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
  @endif
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
