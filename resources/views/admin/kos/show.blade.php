@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-admin')

        <div class="flex-grow-1">
            <div class="topbar d-flex justify-content-between align-items-center px-4 w-100">
                <div></div>
                <div class="d-flex align-items-center">
                    @include('components.notif-admin')
                    <button type="button" class="btn text-white d-flex align-items-center gap-3" data-bs-toggle="modal"
                        data-bs-target="#profileModal">

                        <span class="fw-semibold small">{{ Auth::user()->name }}</span>

                        @if (Auth::user()->photo)
                            <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                                style="width:35px; height:35px; border-radius:50%; object-fit:cover;">
                        @else
                            <i class="bi bi-person-circle fs-3"></i>
                        @endif
                    </button>
                </div>
            </div>

            <div class="p-4" style="background:#f8fafc; min-height:100vh;">
                <h3 class="fw-bold mb-1">Detail Verifikasi Kos</h3>
                <small class="text-muted d-block mb-4">Manajemen Kos / Detail Verifikasi</small>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-header bg-white border-0 pt-4 px-4">
                                <h5 class="mb-0 fw-semibold">Foto Kos</h5>
                            </div>
                            <div class="card-body pt-3 px-4 pb-4">
                                @if (is_array($kos->foto) && count($kos->foto) > 0)
                                    <div id="carouselAdminKos" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner rounded-4 overflow-hidden">
                                            @foreach ($kos->foto as $idx => $foto)
                                                <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $foto) }}" class="d-block w-100"
                                                        style="height:280px; object-fit:cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselAdminKos" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselAdminKos" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    </div>

                                    <div class="row g-2 mt-2">
                                        @foreach ($kos->foto as $foto)
                                            <div class="col-4">
                                                <img src="{{ asset('storage/' . $foto) }}" class="w-100 rounded-3"
                                                    style="height:70px; object-fit:cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light border text-muted mb-0">Belum ada foto kos.</div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-info-circle me-2"></i> Informasi Kos
                                </h5>
                                <div class="mb-2"><span class="text-muted">Pemilik Kos:</span> {{ $kos->user->name }}
                                </div>
                                <div class="mb-2"><span class="text-muted">Nama Kos:</span> {{ $kos->nama_kos }}</div>
                                <div class="mb-2"><span class="text-muted">Lokasi Kos:</span> {{ $kos->lokasi }}</div>
                                <div class="mb-2"><span class="text-muted">Tipe Kos:</span> {{ $kos->tipe_kos }}</div>
                                <div class="mb-2">
                                    <span class="text-muted">Status Kos:</span>
                                    @if ($kos->status === 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($kos->status === 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @elseif($kos->status === 'nonaktif')
                                        <span class="badge bg-dark">Nonaktif</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted">Tanggal Verifikasi:</span>
                                    {{ $kos->tanggal_verifikasi ? $kos->tanggal_verifikasi->format('d-m-Y H:i') : '-' }}
                                </div>

                                @if ($kos->alasan)
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <div class="fw-semibold mb-1">Catatan Admin : </div>
                                        {{ $kos->alasan }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-card-text me-2"></i> Deskripsi Kos
                                </h5>
                                <div class="text-muted text-break" style="white-space: pre-line;">
                                    {{ $kos->deskripsi ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-4 map-row-up">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                @php
                                    $displayLat = $kos->latitude;
                                    $displayLng = $kos->longitude;
                                    if ($kos->edit_request_status === 'menunggu' && is_array($kos->edit_request_data)) {
                                        $displayLat = $kos->edit_request_data['latitude'] ?? $displayLat;
                                        $displayLng = $kos->edit_request_data['longitude'] ?? $displayLng;
                                    }
                                @endphp
                               <h5 class="fw-semibold mb-3">
    <i class="bi bi-geo-alt-fill me-2"></i> Peta Lokasi
</h5>
                                <div id="map" style="height: 220px; width: 100%; border-radius: 8px; z-index: 1;">
                                </div>
                                <a href="https://www.google.com/maps?q={{ $displayLat ?: -6.4005784 }},{{ $displayLng ?: 108.2100865 }}"
                                    target="_blank" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                    <i class="bi bi-geo-alt"></i> Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">
                                   <i class="bi bi-grid-3x3-gap me-2"></i> Fasilitas Kos
                                </h5>
                                @php
                                    $fasilitas = is_array($kos->fasilitas) ? $kos->fasilitas : [];
                                @endphp
                                @if (count($fasilitas) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($fasilitas as $f)
                                            <span
                                                class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-check-circle"></i>
                                                {{ $f }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">Tidak ada fasilitas.</div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-semibold mb-3">Ringkasan Harga Kamar</h5>
                                @if ($kos->kamars->count() > 0)
                                    <div class="mb-2">Total kamar: <strong>{{ $kos->kamars->count() }}</strong></div>
                                    <div class="mb-2">Harga termurah:
                                        <strong>Rp {{ number_format($kos->kamars->min('harga'), 0, ',', '.') }}</strong>
                                    </div>
                                    <div>Harga tertinggi:
                                        <strong>Rp {{ number_format($kos->kamars->max('harga'), 0, ',', '.') }}</strong>
                                    </div>
                                @else
                                    <div class="text-muted">Belum ada data kamar.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">Detail Harga Kamar</h5>
                        @if ($kos->kamars->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Kamar</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kos->kamars as $kamar)
                                            <tr>
                                                <td>{{ $kamar->nama_kamar }}</td>
                                                <td>Rp {{ number_format((int) $kamar->harga, 0, ',', '.') }}</td>
                                                <td>
                                                    @if ($kamar->status === 'terisi')
                                                        <span class="badge bg-danger">Terisi</span>
                                                    @else
                                                        <span class="badge bg-success">Tersedia</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-muted">Belum ada data kamar untuk ditampilkan.</div>
                        @endif
                    </div>
                </div>

                @if ($kos->edit_request_status === 'menunggu' && is_array($kos->edit_request_data))
                    <div class="card shadow-sm rounded-4 mt-4 border border-warning">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold mb-3 text-warning">Pengajuan Perubahan Data Dari Pemilik</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted">Nama Kos Baru</div>
                                    <div>{{ $kos->edit_request_data['nama_kos'] ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Lokasi Baru</div>
                                    <div>{{ $kos->edit_request_data['lokasi'] ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Tipe Baru</div>
                                    <div>{{ $kos->edit_request_data['tipe_kos'] ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Tanggal Pengajuan</div>
                                    <div>{{ $kos->edit_requested_at ? $kos->edit_requested_at->format('d-m-Y H:i') : '-' }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-muted">Deskripsi Baru</div>
                                    <div style="white-space:pre-line;">{{ $kos->edit_request_data['deskripsi'] ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm border-0 rounded-4 mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">Aksi Verifikasi</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @if (in_array($kos->status, ['menunggu', 'ditolak'], true))
                                <form action="{{ route('admin.kos.approve', $kos->id) }}" method="POST"
                                    class="m-0 approve-form">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-approve">Setujui Verifikasi</button>
                                </form>
                            @endif

                            @if ($kos->status === 'menunggu')
                                <form action="{{ route('admin.kos.reject', $kos->id) }}" method="POST"
                                    class="m-0 reject-form">
                                    @csrf
                                    <input type="hidden" name="alasan">
                                    <button type="button" class="btn btn-danger btn-reject">Tolak Verifikasi</button>
                                </form>
                            @endif

                            @if ($kos->status === 'disetujui')
                                <form action="{{ route('admin.kos.deactivate', $kos->id) }}" method="POST"
                                    class="m-0 deactivate-form">
                                    @csrf
                                    <input type="hidden" name="alasan">
                                    <button type="button" class="btn btn-dark btn-deactivate">Nonaktifkan Kos</button>
                                </form>
                            @endif

                            @if ($kos->status === 'nonaktif')
                                <form action="{{ route('admin.kos.activate', $kos->id) }}" method="POST"
                                    class="m-0 activate-form">
                                    @csrf
                                    <button type="button" class="btn btn-outline-success btn-activate">Aktifkan
                                        Kembali</button>
                                </form>
                            @endif

                            @if ($kos->edit_request_status === 'menunggu')
                                <form action="{{ route('admin.kos.edit-request.approve', $kos->id) }}" method="POST"
                                    class="m-0 approve-edit-form">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-approve-edit">Setujui Perubahan
                                        Data</button>
                                </form>

                                <form action="{{ route('admin.kos.edit-request.reject', $kos->id) }}" method="POST"
                                    class="m-0 reject-edit-form">
                                    @csrf
                                    <input type="hidden" name="alasan">
                                    <button type="button" class="btn btn-outline-danger btn-reject-edit">Tolak Perubahan
                                        Data</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.kos.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const askReasonThenSubmit = (buttonSelector, title, confirmText) => {
                document.querySelectorAll(buttonSelector).forEach(btn => {
                    btn.addEventListener('click', function() {
                        let form = this.closest('form');
                        let input = form.querySelector('input[name="alasan"]');

                        Swal.fire({
                            title: title,
                            input: 'textarea',
                            inputLabel: 'Alasan Penolakan:',
                            inputPlaceholder: 'Masukkan Keterangan...',
                            showCancelButton: true,
                            confirmButtonText: confirmText,
                            cancelButtonText: 'Batal',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Alasan wajib diisi!';
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (input) {
                                    input.value = result.value;
                                }
                                form.submit();
                            }
                        });
                    });
                });
            };

            document.querySelectorAll('.btn-approve, .btn-approve-edit, .btn-activate').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin ingin memverifikasi data ini?',
                        text: 'Status akan diubah menjadi "Sudah Terverifikasi".',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Verifikasi',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            askReasonThenSubmit('.btn-reject', 'Tolak Verifikasi Kos', 'Tolak');
            askReasonThenSubmit('.btn-reject-edit', 'Tolak Pengajuan Perubahan Data', 'Tolak');
            askReasonThenSubmit('.btn-deactivate', 'Nonaktifkan Kos', 'Nonaktifkan');
        });
    </script>

    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content p-3 text-center" style="border-radius:20px;">
                <div class="mb-3">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>

                <a href="{{ route('admin.profile') }}" class="btn btn-primary w-100 mb-2">Profil</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* KHUSUS DESKTOP */
        @media (min-width: 992px) {
            .map-row-up {
                margin-top: -25px !important;
                /* sebelumnya 0, sekarang naik */
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var lat = {{ $displayLat ?: -6.4005784 }};
            var lng = {{ $displayLng ?: 108.2100865 }};

            var map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>" + {!! json_encode($kos->nama_kos) !!} + "</b><br>" + {!! json_encode($kos->lokasi) !!}).openPopup();
        });
    </script>
@endpush
