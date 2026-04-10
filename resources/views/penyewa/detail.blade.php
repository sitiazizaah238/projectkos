@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
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
                <div class="dropdown position-relative">

                    <button class="btn text-white position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-4"></i>

                        @if ($totalNotif > 0)
                            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
                                style="top:10px; font-size:10px;">
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
                        Total Kamar : {{ $total }} | Terisi : {{ $terisi }} | Tersedia : {{ $tersedia }}
                    </span>
                </div>

                {{-- ================= FOTO + INFO ================= --}}
                <div class="row g-4">
                    {{-- FOTO KOS SLIDER --}}
                    <div class="col-md-7">
                        @php
                            $fotoKos = [];

                            if (!empty($kos->foto)) {
                                if (is_array($kos->foto)) {
                                    $fotoKos = $kos->foto;
                                } elseif (is_string($kos->foto)) {
                                    $decoded = json_decode($kos->foto, true);
                                    $fotoKos = json_last_error() === JSON_ERROR_NONE ? $decoded : [$kos->foto];
                                }
                            }
                        @endphp

                        @if (!empty($fotoKos))
                            <div id="carouselKos" class="carousel slide" data-bs-ride="carousel">

                                {{-- INDICATOR --}}
                                <div class="carousel-indicators">
                                    @foreach ($fotoKos as $i => $foto)
                                        <button type="button" data-bs-target="#carouselKos"
                                            data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}">
                                        </button>
                                    @endforeach
                                </div>

                                {{-- ISI SLIDE --}}
                                <div class="carousel-inner rounded-4 overflow-hidden" style="height:420px;">
                                    @foreach ($fotoKos as $i => $foto)
                                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $foto) }}" class="d-block w-100"
                                                style="height:420px; object-fit:cover;">
                                        </div>
                                    @endforeach
                                </div>

                                {{-- BUTTON --}}
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselKos"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>

                                <button class="carousel-control-next" type="button" data-bs-target="#carouselKos"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>

                            </div>
                        @else
                            <div class="bg-light d-flex justify-content-center align-items-center rounded-4"
                                style="height:420px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    {{-- FOTO --}}
                    @php
                        $fotoKamar = [];

                        if (!empty($kamar->foto)) {
                            if (is_array($kamar->foto)) {
                                $fotoKamar = $kamar->foto;
                            } elseif (is_string($kamar->foto)) {
                                $decoded = json_decode($kamar->foto, true);
                                $fotoKamar = json_last_error() === JSON_ERROR_NONE ? $decoded : [$kamar->foto];
                            }
                        }
                    @endphp

                    {{-- INFORMASI KOS --}}
                    <div class="col-md-5 d-flex">
                        <div class="card shadow-sm rounded-4 p-4 w-100" style="min-height:430px;">

                            <h6 class="fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle"></i> Informasi Kos
                            </h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Nama Kos</span>
                                <span class="fw-semibold">{{ $kos->nama_kos }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pemilik Kos</span>
                                <span class="fw-semibold">{{ $kos->user->name ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">No HP Pemilik</span>
                                <span class="fw-semibold">{{ $kos->user->no_hp ?? '-' }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Lokasi Kos</span>
                                <span class="fw-semibold text-end">{{ $kos->lokasi }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tipe Kos</span>
                                <span class="fw-semibold">{{ $kos->tipe_kos }}</span>
                            </div>

                            <div>
                                <small class="text-muted">Deskripsi</small>
                                <p class="mb-0">{{ $kos->deskripsi }}</p>
                            </div>
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-stars me-2 text-primary"></i> Fasilitas Kos
                            </h6>

                            <div class="row">
                                @foreach ($kos->fasilitas as $fasilitas)
                                    <div class="col-6 mb-2">
                                        <i class="bi {{ $fasilitasIcon[$fasilitas] ?? 'bi-check-circle' }}"></i>
                                        {{ $fasilitas }}
                                    </div>
                                @endforeach
                            </div>

                            {{-- Tombol WhatsApp --}}
                            @if (!empty($kos->user->no_hp))
                                @php
                                    $waNumber = $kos->user->no_hp;
                                    // bersihkan karakter selain angka
                                    $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62' . substr($waNumber, 1);
                                    }

                                    $namaPemilik = $kos->user->name ?? 'Pemilik Kos';

                                    $waMessage =
                                        "Halo, bapak/ibu {$namaPemilik}. Saya tertarik untuk menyewa kamar kos nomor (isi nomor kamar) di kost *{$kos->nama_kos}*.\n\nBerikut data saya:\nNama: *" .
                                        Auth::user()->name .
                                        "*\nTanggal mulai sewa: (isi tanggal mulai)\nDurasi sewa : (isi durasi sewa)\n\nApakah saya bisa mendapatkan informasi lebih lanjut mengenai kamar tersebut?\n\nTerima kasih.";
                                    $waUrl = "https://wa.me/{$waNumber}?text=" . rawurlencode($waMessage);
                                @endphp
                                <div class="mt-auto pt-4">
                                    <a href="{{ $waUrl }}" target="_blank"
                                        class="btn w-100 rounded-pill py-2 text-white fw-bold shadow-sm d-flex align-items-center justify-content-center"
                                        style="background-color: #25D366; transition: all 0.3s ease;">
                                        <i class="bi bi-whatsapp fs-5 me-2"></i> Hubungi Pemilik via WA
                                    </a>
                                </div>
                            @endif

                        </div>

                    </div>


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
                                        @endphp

                                        @if (count($fotoKamar) > 0)
                                            <div id="carouselKamar{{ $kamar->id }}" class="carousel slide"
                                                data-bs-ride="carousel">

                                                <div class="carousel-inner">

                                                    @foreach ($fotoKamar as $index => $foto)
                                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                            <img src="{{ asset('storage/' . $foto) }}"
                                                                class="d-block w-100 rounded-top"
                                                                style="height:300px; object-fit:cover; object-position:center 35%;">
                                                        </div>
                                                    @endforeach

                                                </div>

                                                {{-- Tombol Prev --}}
                                                <button class="carousel-control-prev" type="button"
                                                    data-bs-target="#carouselKamar{{ $kamar->id }}"
                                                    data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </button>

                                                {{-- Tombol Next --}}
                                                <button class="carousel-control-next" type="button"
                                                    data-bs-target="#carouselKamar{{ $kamar->id }}"
                                                    data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </button>

                                            </div>
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
                                                            min="{{ date('Y-m-d') }}" required>
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
                                                        <form action="{{ route('penyewa.pengajuan.store') }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-primary rounded-pill px-4">
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
