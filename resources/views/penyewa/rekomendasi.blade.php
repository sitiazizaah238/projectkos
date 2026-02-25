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


            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">
               <h3 class="fw-bold" style="font-size: 30px;">Rekomendasi Untuk Anda</h3>
                <p class="text-muted">Berdasarkan preferensi pencarian dan riwayat Anda.</p>

                <div class="row g-4 mt-2">

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
                                                <span class="text-muted">/{{ ucfirst($kamarTermurah->tipe_harga) }}</span>
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
                        <a href="{{ route('penyewa.profile') }}" class="btn btn-primary w-100 mb-2">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger w-100">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
