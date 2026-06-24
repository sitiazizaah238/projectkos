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

    // =====================
    // NOTIF SECTION
    // =====================

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
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
                @include('components.chat-icon-pemilik')
                @include('components.notif-pemilik')
                <button type="button" class="btn text-white d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="fw-semibold text-white small">
                        {{ Auth::user()->name }}
                    </span>

                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/profile/' . Auth::user()->photo) }}"
                            style="
                    width:35px;
                    height:35px;
                    min-width:35px;
                    min-height:35px;
                    border-radius:50%;
                    object-fit:cover;
                ">
                    @else
                        <i class="bi bi-person-circle fs-3"></i>
                    @endif

                </button>
            </div>
            {{-- CONTENT --}}
            <div class="p-4">

                <h3 class="fw-bold" style="font-size:25px;">
                    Tambah Kos Baru</h3>
                <small class="text-muted">Manajemen Kos/Data Kos</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-house-door-fill me-2"></i>
                        Form Tambah Kos
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('pemilik.kos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <label>Nama Kos</label>
                                    <input type="text" name="nama_kos" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Foto Kos</label>
                                    <input type="file" name="foto[]" id="fotoInput" class="form-control" multiple
                                        accept="image/*">

                                    <small class="text-muted">Maksimal 3 foto</small>

                                    <div class="row mt-3" id="previewContainer"></div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Lokasi Kos (Alamat Lengkap)</label>
                                    <input type="text" name="lokasi" class="form-control">
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Titik Lokasi (Maps)</label>
                                    <div id="map" style="height: 300px; width: 100%; border-radius: 8px; z-index: 1;">
                                    </div>
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <small class="text-muted">Klik pada peta untuk menentukan titik lokasi kos yang
                                        tepat.</small>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Tipe Kos</label>
                                    <select name="tipe_kos" class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="Putra">Putra</option>
                                        <option value="Putri">Putri</option>
                                        <option value="Campur">Campur</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control"></textarea>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Fasilitas</label>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Parkir"> Tempat Parkir
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="CCTV"> CCTV
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Mushola"> Mushola
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Dapur"> Dapur Bersama
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Wifi"> Wifi
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>

                                <a href="{{ route('pemilik.kos.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var map = L.map('map').setView([-6.4005784, 108.2100865], 13); // Default Lohbener
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var marker = L.marker([-6.4005784, 108.2100865], {
                    draggable: true
                }).addTo(map);

                document.getElementById('latitude').value = -6.4005784;
                document.getElementById('longitude').value = 108.2100865;

                marker.on('dragend', function(e) {
                    var position = marker.getLatLng();
                    if (position.lat < -6.45 || position.lat > -6.30 || position.lng < 108.15 || position.lng >
                        108.35) {
                        alert('Lokasi kos harus berada di wilayah Kecamatan Lohbener!');
                        marker.setLatLng([-6.4005784, 108.2100865]);
                        map.setView([-6.4005784, 108.2100865], 13);
                        document.getElementById('latitude').value = -6.4005784;
                        document.getElementById('longitude').value = 108.2100865;
                        return;
                    }
                    document.getElementById('latitude').value = position.lat;
                    document.getElementById('longitude').value = position.lng;
                });

                map.on('click', function(e) {
                    var lat = e.latlng.lat;
                    var lng = e.latlng.lng;

                    if (lat < -6.45 || lat > -6.30 || lng < 108.15 || lng > 108.35) {
                        alert('Lokasi kos harus berada di wilayah Kecamatan Lohbener!');
                        return;
                    }

                    marker.setLatLng(e.latlng);

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                });
            });

            let selectedFiles = [];
            const input = document.getElementById('fotoInput');
            const preview = document.getElementById('previewContainer');

            input.addEventListener('change', function(e) {

                const newFiles = Array.from(e.target.files);
                selectedFiles = [...selectedFiles, ...newFiles];

                if (selectedFiles.length > 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maksimal 3 Foto!'
                    });
                    selectedFiles = selectedFiles.slice(0, 3);
                }

                renderPreview();
                updateFileInput();
            });

            function renderPreview() {
                preview.innerHTML = "";

                selectedFiles.forEach((file, index) => {

                    const reader = new FileReader();
                    reader.onload = function(e) {

                        preview.innerHTML += `
                <div class="col-4 mb-3">
                    <div class="position-relative">
                        <img src="${e.target.result}"
                            class="img-fluid rounded shadow"
                            style="height:120px; object-fit:cover;">
                        <button type="button"
                            class="btn btn-danger btn-sm position-absolute top-0 end-0"
                            onclick="removeImage(${index})">
                            x
                        </button>
                    </div>
                </div>
            `;
                    }
                    reader.readAsDataURL(file);
                });
            }

            function removeImage(index) {
                selectedFiles.splice(index, 1);
                renderPreview();
                updateFileInput();
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                input.files = dataTransfer.files;
            }
        </script>
    @endpush
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
