@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
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

                        <div class="card shadow-sm border-0 rounded-4 mt-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">Peta Lokasi</h5>
                                <div id="map" style="height: 220px; width: 100%; border-radius: 8px; z-index: 1;"></div>
                                <a href="https://www.google.com/maps?q={{ $kos->latitude ?: -6.4005784 }},{{ $kos->longitude ?: 108.2100865 }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                    <i class="bi bi-geo-alt"></i> Buka di Google Maps
                                </a>
                            </div>
                        </div>
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
                    <div class="col-md-5">
                        <div class="card shadow-sm rounded-4 mb-3 w-100">
                            <div class="card-body p-4">

                                <h6 class="fw-bold border-bottom pb-2 mb-3">
                                    <i class="bi bi-info-circle"></i> Informasi Kos
                                </h6>

                                <div class="d-flex justify-content-between mb-2 fs-6">
                                    <span class="text-muted">Nama Kos</span>
                                    <span class="fw-semibold">{{ $kos->nama_kos }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 fs-6">
                                    <span class="text-muted">Pemilik Kos</span>
                                    <span class="fw-semibold">{{ $kos->user->name ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 fs-6">
                                    <span class="text-muted">No HP Pemilik</span>
                                    <span class="fw-semibold">{{ $kos->user->no_hp ?? '-' }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 fs-6">
                                    <span class="text-muted">Lokasi Kos</span>
                                    <span class="fw-semibold text-end">{{ $kos->lokasi }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3 fs-6">
                                    <span class="text-muted">Tipe Kos</span>
                                    <span class="fw-semibold">{{ $kos->tipe_kos }}</span>
                                </div>

                                <hr class="my-4">

                                <div>
                                    <div class="text-muted mb-2 fs-6">Deskripsi</div>
                                    <div class="fs-6" style="line-height:1.7;">
                                        {{ $kos->deskripsi }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm rounded-4 mb-3 w-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-stars me-2 text-primary"></i> Fasilitas Kos
                                </h6>

                                <div class="row fs-6">
                                    @foreach ($kos->fasilitas as $fasilitas)
                                        <div class="col-6 mb-2">
                                            <i class="bi {{ $fasilitasIcon[$fasilitas] ?? 'bi-check-circle' }}"></i>
                                            {{ $fasilitas }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
                                <div class="mt-1">
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
                                                    <span class="text-muted fw-normal">/ {{ $kamar->tipe_harga === 'tahunan' ? 'tahun' : 'bulan' }}</span>
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
                                                     <input type="date" name="tanggal_mulai" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Jenis Sewa</label>
                                                        <select name="jenis_sewa" class="form-select jenis-sewa-select"
                                                            data-target="#durasiContainer{{ $kamar->id }}"
                                                            data-select="#durasiSelect{{ $kamar->id }}" required>
                                                            <option value="" selected disabled>Pilih jenis sewa
                                                            </option>
                                                            <option value="bulanan">Bulanan</option>
                                                            <option value="tahunan">Tahunan</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-4 d-none" id="durasiContainer{{ $kamar->id }}">
                                                        <label class="fw-semibold">Durasi Sewa</label>
                                                        <select name="durasi" class="form-select"
                                                            id="durasiSelect{{ $kamar->id }}" required disabled>
                                                            <option value="" selected disabled>Pilih durasi sewa
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="text-end">
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                            Ajukan Penyewaan
                                                        </button>
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
        <script>
            document.addEventListener('change', function(event) {
                if (!event.target.classList.contains('jenis-sewa-select')) {
                    return;
                }

                const jenisSewa = event.target.value;
                const targetSelector = event.target.getAttribute('data-target');
                const selectSelector = event.target.getAttribute('data-select');

                const durasiContainer = document.querySelector(targetSelector);
                const durasiSelect = document.querySelector(selectSelector);

                if (!durasiContainer || !durasiSelect) {
                    return;
                }

                const opsiBulanan = [{
                        value: '1',
                        label: '1 Bulan'
                    },
                    {
                        value: '2',
                        label: '2 Bulan'
                    },
                    {
                        value: '3',
                        label: '3 Bulan'
                    },
                    {
                        value: '6',
                        label: '6 Bulan'
                    },
                    {
                        value: '12',
                        label: '12 Bulan'
                    }
                ];

                const opsiTahunan = [{
                        value: '12',
                        label: '1 Tahun'
                    },
                    {
                        value: '24',
                        label: '2 Tahun'
                    },
                    {
                        value: '36',
                        label: '3 Tahun'
                    }
                ];

                const opsi = jenisSewa === 'tahunan' ? opsiTahunan : opsiBulanan;

                durasiSelect.innerHTML = '<option value="" selected disabled>Pilih durasi sewa</option>';
                opsi.forEach(function(item) {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.label;
                    durasiSelect.appendChild(opt);
                });

                durasiContainer.classList.remove('d-none');
                durasiSelect.disabled = false;
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

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var lat = {{ $kos->latitude ?: -6.383568 }};
            var lng = {{ $kos->longitude ?: 108.281052 }};
            
            var map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            @if($kos->latitude && $kos->longitude)
                L.marker([lat, lng]).addTo(map)
                    .bindPopup("<b>" + {!! json_encode($kos->nama_kos) !!} + "</b><br>" + {!! json_encode($kos->lokasi) !!}).openPopup();
            @endif
        });
    </script>
@endpush
