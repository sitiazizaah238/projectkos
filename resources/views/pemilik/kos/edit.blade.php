@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\PengajuanSewa;
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::id();
    $kosIds = App\Models\Kos::where('user_id', $userId)->pluck('id');

    $notifKos = App\Models\Kos::where('user_id', $userId)
        ->where('is_read', false)
        ->where(function ($query) {
            $query
                ->whereIn('status', ['disetujui', 'nonaktif'])
                ->orWhere('edit_request_status', 'ditolak')
                ->orWhere('edit_request_status', 'disetujui');
        })
        ->latest()
        ->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();

    // Pastikan fasilitas jadi array
    $fasilitasKos = is_array($kos->fasilitas) ? $kos->fasilitas : json_decode($kos->fasilitas, true);
@endphp

@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">

    @include('components.notif-pemilik')

    <button type="button"
        class="btn text-white d-flex align-items-center gap-2"
        data-bs-toggle="modal"
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

            <div class="p-4">

                <h3 class="fw-bold" style="font-size:25px;">Edit Kos</h3>
                <small class="text-muted">Manajemen Kos / Data Kos</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-house-door-fill me-2"></i>
                        Form Edit Kos
                    </div>

                    <div class="card-body">

                        @if ($kos->status === 'disetujui')
                            <div class="alert alert-info">
                                Perubahan data kos yang sudah disetujui akan diajukan dulu ke admin untuk diverifikasi.
                                Data utama tidak langsung berubah sebelum admin menyetujui.
                            </div>
                        @endif

                        <form action="{{ route('pemilik.kos.update', $kos->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- NAMA --}}
                                <div class="col-md-6">
                                    <label>Nama Kos</label>
                                    <input type="text" name="nama_kos" value="{{ $kos->nama_kos }}" class="form-control">
                                </div>

                                {{-- FOTO --}}
                                <div class="col-md-6">
                                    <label>Foto Kos</label>
                                    <input type="file" name="foto[]" id="fotoInput" class="form-control" multiple
                                        accept="image/*">

                                    <small class="text-muted">Maksimal 3 foto</small>

                                    <div class="row mt-3" id="previewContainer"></div>

                                    @if ($kos->foto && count($kos->foto) > 0)
                                        <label class="mt-3 fw-semibold">Foto Saat Ini</label>

                                        <div class="row mt-2 g-3" id="oldPhotoContainer">
                                            @foreach ($kos->foto as $index => $f)
                                                <div class="col-4" id="old-photo-{{ $index }}">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $f) }}"
                                                            class="img-fluid rounded shadow-sm w-100"
                                                            style="height:120px; object-fit:cover;">

                                                        <button type="button"
                                                            class="btn btn-danger btn-sm position-absolute"
                                                            style="top:6px; right:6px;"
                                                            onclick="removeOldPhoto('{{ $f }}', {{ $index }})">
                                                            ✕
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <input type="hidden" name="deleted_photos" id="deletedPhotos">
                                </div>

                                {{-- LOKASI --}}
                                <div class="col-md-6 mt-3">
                                    <label>Lokasi Kos (Alamat Lengkap)</label>
                                    <input type="text" name="lokasi" value="{{ $kos->lokasi }}" class="form-control">
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Titik Lokasi (Maps)</label>
                                    <div id="map" style="height: 300px; width: 100%; border-radius: 8px; z-index: 1;"></div>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ $kos->latitude }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ $kos->longitude }}">
                                    <small class="text-muted">Klik pada peta untuk mengubah titik lokasi kos yang tepat.</small>
                                </div>

                                {{-- TIPE --}}
                                <div class="col-md-6 mt-3">
                                    <label>Tipe Kos</label>
                                    <select name="tipe_kos" class="form-control">
                                        <option value="Putra" {{ $kos->tipe_kos == 'Putra' ? 'selected' : '' }}>Putra
                                        </option>
                                        <option value="Putri" {{ $kos->tipe_kos == 'Putri' ? 'selected' : '' }}>Putri
                                        </option>
                                        <option value="Campur" {{ $kos->tipe_kos == 'Campur' ? 'selected' : '' }}>Campur
                                        </option>
                                    </select>
                                </div>

                                {{-- DESKRIPSI --}}
                                <div class="col-md-12 mt-3">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control">{{ $kos->deskripsi }}</textarea>
                                </div>

                                {{-- ✅ FASILITAS (INI YANG TADI KURANG) --}}
                                <div class="col-md-12 mt-3">
                                    <label>Fasilitas</label>
                                    <div class="row mt-2">

                                        @php
                                            $listFasilitas = ['Parkir', 'CCTV', 'Mushola', 'Dapur', 'Wifi'];
                                        @endphp

                                        @foreach ($listFasilitas as $f)
                                            <div class="col-md-4">
                                                <input type="checkbox" name="fasilitas[]" value="{{ $f }}"
                                                    {{ in_array($f, $fasilitasKos ?? []) ? 'checked' : '' }}>
                                                {{ $f }}
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary">Update</button>
                                <a href="{{ route('pemilik.kos.index') }}" class="btn btn-secondary">Kembali</a>
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
            var defaultLat = {{ $kos->latitude ?: -6.4005784 }};
            var defaultLng = {{ $kos->longitude ?: 108.2100865 }};
            var map = L.map('map').setView([defaultLat, defaultLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            document.getElementById('latitude').value = defaultLat;
            document.getElementById('longitude').value = defaultLng;

            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                if (position.lat < -6.45 || position.lat > -6.30 || position.lng < 108.15 || position.lng > 108.35) {
                    alert('Lokasi kos harus berada di wilayah Kecamatan Lohbener!');
                    marker.setLatLng([defaultLat, defaultLng]);
                    map.setView([defaultLat, defaultLng], 15);
                    document.getElementById('latitude').value = defaultLat;
                    document.getElementById('longitude').value = defaultLng;
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
        let deletedPhotos = [];

        const input = document.getElementById('fotoInput');
        const preview = document.getElementById('previewContainer');

        input.addEventListener('change', function(e) {

            const newFiles = Array.from(e.target.files);
            selectedFiles = [...selectedFiles, ...newFiles];

            if (selectedFiles.length > 3) {
                alert('Maksimal 3 Foto!');
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
                             class="img-fluid rounded shadow-sm"
                             style="height:120px; object-fit:cover;">
                        <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                onclick="removeImage(${index})">✕</button>
                    </div>
                </div>`;
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
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        function removeOldPhoto(path, index) {
            deletedPhotos.push(path);
            document.getElementById('deletedPhotos').value = deletedPhotos.join(',');
            document.getElementById('old-photo-' + index).remove();
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
