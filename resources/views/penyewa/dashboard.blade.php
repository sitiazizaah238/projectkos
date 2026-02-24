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
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">

            {{-- ================= TOPBAR (SAMA KAYA PEMILIK) ================= --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

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
                    // Ambil sewa aktif terbaru
                    $sewaAktifData = PengajuanSewa::with(['kos', 'kamar'])
                        ->where('user_id', $userId)
                        ->where('status', 'aktif')
                        ->latest()
                        ->first();

                    // Ambil pembayaran terakhir dari sewa aktif
                    $pembayaran = null;
                    if ($sewaAktifData) {
                        $pembayaran = Pembayaran::where('pengajuan_sewa_id', $sewaAktifData->id)->latest()->first();
                    }
                    // Total Kos yang pernah diajukan / disewa
                    $totalKos = PengajuanSewa::where('user_id', $userId)->count();

                    // Sewa Aktif (status disetujui / aktif)
                    $sewaAktif = PengajuanSewa::where('user_id', $userId)->where('status', 'aktif')->count();

                    // Menunggu Persetujuan
                    $menunggu = PengajuanSewa::where('user_id', $userId)->where('status', 'menunggu')->count();

                    // Riwayat (selesai)
                    $riwayat = PengajuanSewa::where('user_id', $userId)->where('status', 'selesai')->count();

                @endphp
                {{-- ================= CARD STATISTIK ================= --}}
                <div class="row mt-4 g-3 align-items-stretch">

                    <div class="row">

                        {{-- TOTAL KOS --}}
                        <div class="col-md-3 mb-4">
                            <div class="card border-0 shadow rounded-4 h-100 text-white"
                                style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
                                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="opacity-75">Total Kos Disewa</small>
                                        <h3 class="fw-bold mt-2 mb-0">{{ $totalKos }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                                        style="width:55px;height:55px;">
                                        <i class="bi bi-house-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEWA AKTIF --}}
                        <div class="col-md-3 mb-4">
                            <div class="card border-0 shadow rounded-4 h-100 text-white"
                                style="background: linear-gradient(135deg,#16a34a,#22c55e);">
                                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="opacity-75">Sewa Aktif</small>
                                        <h3 class="fw-bold mt-2 mb-0">{{ $sewaAktif }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                                        style="width:55px;height:55px;">
                                        <i class="bi bi-check-circle-fill fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MENUNGGU PERSETUJUAN --}}
                        <div class="col-md-3 mb-4">
                            <div class="card border-0 shadow rounded-4 h-100 text-white"
                                style="background: linear-gradient(135deg,#2563eb,#3b82f6);">
                                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="opacity-75">Menunggu Persetujuan</small>
                                        <h3 class="fw-bold mt-2 mb-0">{{ $menunggu }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                                        style="width:55px;height:55px;">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIWAYAT --}}
                        <div class="col-md-3 mb-4">
                            <div class="card border-0 shadow rounded-4 h-100 text-white"
                                style="background: linear-gradient(135deg,#dc2626,#ef4444);">
                                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="opacity-75">Riwayat Sewa</small>
                                        <h3 class="fw-bold mt-2 mb-0">{{ $riwayat }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center"
                                        style="width:55px;height:55px;">
                                        <i class="bi bi-clock-history fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- ================= KOS SAAT INI + STATUS ================= --}}
                    <div class="row mt-4 g-3 align-items-stretch">

                        {{-- KOS SAAT INI --}}
                        <div class="col-md-6 d-flex">
                            <div class="card p-3 shadow-sm w-100 h-100">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-info-circle me-1"></i> Kos Saat Ini
                                </h6>

                                @if ($sewaAktifData)
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1"><b>{{ $sewaAktifData->kos->nama_kos }}</b></p>

                                            <small>Kamar : {{ $sewaAktifData->kamar->nama_kamar }}</small><br>

                                            @php
                                                $durasi = \App\Models\Pembayaran::whereHas('pengajuan', function (
                                                    $q,
                                                ) use ($sewaAktifData) {
                                                    $q->where('kamar_id', $sewaAktifData->kamar_id)->where(
                                                        'user_id',
                                                        auth()->id(),
                                                    );
                                                })
                                                    ->where('status', 'dikonfirmasi')
                                                    ->latest()
                                                    ->first();

                                                $jumlahBulan = $durasi ? $durasi->pengajuan->durasi : 1;
                                            @endphp

                                            <small>
                                                Durasi Sewa : {{ $jumlahBulan }} Bulan
                                            </small>
                                            <br>
                                            <small>
                                                Status :
                                                <span class="badge bg-success">Aktif</span>
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            @php
                                                $tanggalMulai = \Carbon\Carbon::parse($sewaAktifData->created_at);

                                                // Ambil dari pembayaran yang dikonfirmasi
                                                $pembayaran = \App\Models\Pembayaran::whereHas('pengajuan', function (
                                                    $q,
                                                ) use ($sewaAktifData) {
                                                    $q->where('kamar_id', $sewaAktifData->kamar_id)->where(
                                                        'user_id',
                                                        auth()->id(),
                                                    );
                                                })
                                                    ->where('status', 'dikonfirmasi')
                                                    ->latest()
                                                    ->first();

                                                $jumlahBulan = $pembayaran ? (int) $pembayaran->pengajuan->durasi : 1;

                                                $tanggalSelesai = $tanggalMulai->copy()->addMonths($jumlahBulan);
                                            @endphp

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

                        {{-- STATUS PEMBAYARAN (PINDAH KE SINI ✅) --}}
                        <div class="col-md-6 d-flex">
                            <div class="card p-3 shadow-sm w-100 h-100">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-info-circle me-1"></i> Status Pembayaran
                                </h6>

                                <table class="table table-borderless mb-0">
                                    @if ($pembayaran)
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

                    {{-- ================= REKOMENDASI KOS ================= --}}
                    <div class="card mt-4 p-3 shadow-sm">

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-star-fill"></i> Rekomendasi Kos Untuk Anda
                        </h6>

                        <div class="row g-4">

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
                                        @if ($k->foto)
                                            <img src="{{ asset('storage/' . $k->foto) }}" class="card-img-top"
                                                style="height:180px; object-fit:cover;">
                                        @else
                                            <div style="height:180px; background:#dee2e6;"
                                                class="d-flex align-items-center justify-content-center">
                                                <i class="bi bi-house fs-1 text-muted"></i>
                                            </div>
                                        @endif

                                        <div class="card-body d-flex flex-column">

                                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                                <h6 class="fw-bold mb-0" style="min-width:0;">{{ $k->nama_kos }}</h6>
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
                                                        <i class="bi bi-tag me-1 text-primary"></i>Harga mulai dari:
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
                                            <strong>"Cari Kos"</strong> dan gunakan filter untuk membantu AI kami bekerja.
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
