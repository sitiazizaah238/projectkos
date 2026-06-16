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

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">
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

            <div class="p-4">

                <h3 class="fw-bold" style="font-size:25px;">
                    Tambah Data Kamar</h3>
                <small class="text-muted">Manajemen Kamar/Data Kamar</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-door-open-fill me-2"></i>
                        Tambah Kamar
                    </div>
                    @if ($kos->isEmpty())
                        <div class="alert alert-warning m-3">
                            Kos Anda belum disetujui. Anda tidak dapat menambahkan kamar.
                        </div>
                    @else
                        <div class="card shadow-lg border-0" style="border-radius:20px;">
                            <div class="card-body p-4">
                                {{-- ERROR VALIDASI --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                {{-- ERROR CUSTOM --}}
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <form action="{{ route('pemilik.kamar.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">

                                        {{-- KIRI --}}
                                        <div class="col-md-6">

                                            <div class="mb-3">
                                                <label class="form-label">Nama Kos</label>
                                                <select name="kos_id" class="form-select" required>
                                                    <option value="">Pilih Nama Kos</option>
                                                    @foreach ($kos as $k)
                                                        <option
                                                            value="{{ $k->id }}"{{ old('kos_id') == $k->id ? 'selected' : '' }}>
                                                            {{ $k->nama_kos }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Kamar</label>
                                                <input type="text" name="nama_kamar" class="form-control"
                                                    value="{{ old('nama_kamar') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi Kamar</label>
                                                <textarea name="deskripsi" class="form-control"></textarea>
                                            </div>

                                            {{-- FASILITAS --}}
                                            <label class="fw-semibold mb-2">Fasilitas Kamar</label>

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Kamar Mandi Dalam"> Kamar Mandi Dalam
                                                    </div>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="AC"> AC</div>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Kipas Angin"> Kipas Angin</div>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Cermin"> Cermin</div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Lemari Pakaian"> Lemari Pakaian</div>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Meja"> Meja Belajar/Kerja</div>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="fasilitas[]" value="Tempat Tidur"> Kasur & Bantal</div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- KANAN --}}
                                        <div class="col-md-6">

                                            <div class="mb-3">
                                                <label class="form-label">Foto Kamar Kos (Maksimal 3 Foto)</label>

                                                <input type="file" name="foto[]" id="fotoInput" class="form-control"
                                                    multiple accept="image/*" required>

                                                <small class="text-muted">maksimal 3 foto</small>

                                                <div class="row mt-3" id="previewContainer"></div>
                                            </div>


                                            <div class="mb-3">
                                                <label class="form-label">Status Kamar</label>
                                                <input type="hidden" name="status" value="tersedia">

                                                <div class="form-control bg-light">
                                                    Tersedia
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Harga</label>
                                                <input type="text" name="harga" id="hargaInput"
                                                    class="form-control" placeholder="Rp 0" value="{{ old('harga') }}">
                                            </div>


                                            <div class="mb-3">
                                                <label class="form-label">Tipe Harga</label>
                                                <select name="tipe_harga" class="form-select">
                                                    <option value="bulanan">Perbulan</option>
                                                    <option value="tahunan">Pertahun</option>
                                                </select>
                                            </div>

                                        </div>

                                    </div>

                                    {{-- BUTTON --}}
                                    <div class="d-flex justify-content-end mt-4">
                                        <a href="{{ route('pemilik.kamar.index') }}" class="btn btn-danger me-2">
                                            <i class="bi bi-x-circle"></i> Batal
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Simpan
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <script>
            let selectedFiles = [];

            const input = document.getElementById('fotoInput');
            const preview = document.getElementById('previewContainer');

            input.addEventListener('change', function(e) {

                const newFiles = Array.from(e.target.files);

                // Gabungkan file lama + baru
                selectedFiles = [...selectedFiles, ...newFiles];

                // Batasi maksimal 3
                if (selectedFiles.length > 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maksimal 3 Foto!',
                        text: 'Anda hanya bisa upload maksimal 3 foto.'
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

                        const col = document.createElement('div');
                        col.className = "col-4 mb-3";

                        col.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}"
                         class="img-fluid rounded shadow-sm"
                         style="height:120px; object-fit:cover;">

                    <button type="button"
                        class="btn btn-danger btn-sm position-absolute top-0 end-0"
                        onclick="removeImage(${index})">
                        x
                    </button>
                </div>
            `;

                        preview.appendChild(col);
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

