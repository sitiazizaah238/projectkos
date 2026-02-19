@extends('layouts.app')
@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">

                <button type="button" class="btn text-white d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#profileModal">

                    <span class="me-2">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-3"></i>
                </button>

            </div>

            <div class="p-4 mt-4">

                <h3 class="fw-bold mb-4" style="font-size:25px;">
                    Tambah Kamar Terbaru
                </h3>
                @if ($kos->isEmpty())
                    <div class="alert alert-warning">
                        Kos Anda belum disetujui. Anda tidak dapat menambahkan kamar.
                    </div>
                @endif

                <div class="card shadow-lg border-0" style="border-radius:20px;">
                    <div class="card-body p-4">

                        <form action="{{ route('pemilik.kamar.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                {{-- KIRI --}}
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Kos</label>
                                        <select name="kos_id" class="form-select">
                                            <option value="">Pilih Nama Kos</option>
                                            @foreach ($kos as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama_kos }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Kamar</label>
                                        <input type="text" name="nama_kamar" class="form-control">
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
                                                    name="fasilitas[]" value="Kamar Mandi Dalam"> Kamar Mandi Dalam</div>
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
                                                    name="fasilitas[]" value="Meja"> Meja</div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                                    name="fasilitas[]" value="Tempat Tidur"> Tempat Tidur</div>
                                        </div>
                                    </div>

                                </div>

                                {{-- KANAN --}}
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="form-label">Foto Kamar Kos (Maksimal 3 Foto)</label>

                                        <input type="file" name="foto[]" id="fotoInput" class="form-control" multiple
                                            accept="image/*" required>

                                        <small class="text-muted">maksimal 3 foto</small>

                                        <div class="row mt-3" id="previewContainer"></div>
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label">Status Kamar</label>
                                        <select name="status" class="form-select">
                                            <option value="tersedia">Tersedia</option>
                                            <option value="terisi">Terisi</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga</label>
                                        <input type="text" name="harga" id="hargaInput" class="form-control"
                                            placeholder="Rp 0">
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label">Tipe Harga</label>
                                        <select name="tipe_harga" class="form-select">
                                            <option value="bulanan">Bulanan</option>
                                            <option value="tahunan">Tahunan</option>
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
                        ✕
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
