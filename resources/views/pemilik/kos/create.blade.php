@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\Kamar;
    use App\Models\PengajuanSewa;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $userId = Auth::id();

    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $totalKos = Kos::where('user_id', $userId)->count();
    $totalKamar = Kamar::whereIn('kos_id', $kosIds)->count();
    $kamarTersedia = Kamar::whereIn('kos_id', $kosIds)->where('status', 'tersedia')->count();
    $totalPenyewa = PengajuanSewa::whereIn('kos_id', $kosIds)->where('status', 'aktif')->count();

    // =====================
    // 🔔 NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();
@endphp

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
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

                        {{-- NOTIF KOS DISETUJUI --}}
                        @foreach ($notifKos as $n)
                            <a href="{{ url('/notif/kos/' . $n->id) }}" class="dropdown-item small py-2">
                                <strong>Kos Disetujui</strong><br>
                                Kos <strong>{{ $n->nama_kos }}</strong> telah disetujui admin
                            </a>
                        @endforeach

                        {{-- NOTIF PENGAJUAN --}}
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
                    Tambah Data Kos</h3>
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
                                    <label>Lokasi Kos</label>
                                    <input type="text" name="lokasi" class="form-control">
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

                            <div class="mt-4">
                                <button class="btn btn-primary">Simpan</button>
                                <a href="{{ route('pemilik.kos.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
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
                            ✕
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
@endsection
