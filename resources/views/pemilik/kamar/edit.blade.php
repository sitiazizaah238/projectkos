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
    // NOTIF SECTION
    // =====================

    $notifKos = Kos::where('user_id', $userId)->where('status', 'disetujui')->where('is_read', false)->latest()->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)->where('is_read', false)->latest()->get();

    $jumlahNotif = $notifKos->count() + $notifPengajuan->count();
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
                    Edit Data Kamar</h3>
                <small class="text-muted">Manajemen Kamar/Data Kamar</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-door-open-fill me-2"></i>
                        Edit Kamar
                    </div>

                    <div class="card shadow-lg border-0" style="border-radius:20px;">
                        <div class="card-body p-4">
                            <style>
                                .form-check-input {
                                    accent-color: #0d6efd;
                                    border: 1px solid #0d6efd;
                                    background-color: #fff;
                                }

                                .form-check-input:checked {
                                    background-color: #0d6efd;
                                    border-color: #0d6efd;
                                }
                            </style>
                            <form action="{{ route('pemilik.kamar.update', $kamar->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">

                                    {{-- KIRI --}}
                                    <div class="col-md-6">

                                        {{-- Nama Kos --}}
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kos</label>
                                            <select name="kos_id" class="form-select">
                                                @foreach ($kos as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ $kamar->kos_id == $k->id ? 'selected' : '' }}>
                                                        {{ $k->nama_kos }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Nama Kamar --}}
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kamar</label>
                                            <input type="text" name="nama_kamar" value="{{ $kamar->nama_kamar }}"
                                                class="form-control">
                                        </div>

                                        {{-- Deskripsi --}}
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi Kamar</label>
                                            <textarea name="deskripsi" class="form-control">{{ $kamar->deskripsi }}</textarea>
                                        </div>

                                        {{-- Fasilitas --}}
                                        @php
                                            $fasilitasKamar = $kamar->fasilitas ?? [];
                                        @endphp

                                        <label class="fw-semibold mb-2">Fasilitas Kamar</label>

                                        <div class="row">
                                            @foreach (['Kamar Mandi Dalam', 'AC', 'Kipas Angin', 'Cermin', 'Lemari Pakaian', 'Meja Bejalar/ Kerja', 'Kasur & Bantal'] as $f)
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                                            value="{{ $f }}"
                                                            {{ in_array($f, $fasilitasKamar) ? 'checked' : '' }}>
                                                        {{ $f }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>

                                    {{-- KANAN --}}
                                    <div class="col-md-6">

                                        {{-- Foto Lama --}}
                                        <div class="mb-3">
                                            <label class="form-label">Foto Lama</label>
                                            <div class="row">
                                                @if ($kamar->foto && count($kamar->foto) > 0)
                                                    @foreach ($kamar->foto as $foto)
                                                        <div class="col-4 mb-3">
                                                            <img src="{{ asset('storage/' . $foto) }}"
                                                                class="img-fluid rounded shadow-sm"
                                                                style="height:120px; object-fit:cover;">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Upload Foto Baru --}}
                                        <div class="mb-3">
                                            <label class="form-label">Upload Foto Baru (Opsional)</label>
                                            <input type="file" name="foto[]" class="form-control" multiple
                                                accept="image/*">
                                            <small class="text-muted">
                                                Kosongkan jika tidak ingin mengganti foto
                                            </small>
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label class="form-label">Status Kamar</label>

                                            <input type="hidden" name="status" value="{{ $kamar->status }}">

                                            <div class="form-control bg-light">
                                                {{ ucfirst($kamar->status) }}
                                            </div>
                                        </div>

                                        {{-- Harga --}}
                                        <div class="mb-3">
                                            <label class="form-label">Harga</label>
                                            <input type="text" name="harga"
                                                value="Rp {{ number_format($kamar->harga, 0, ',', '.') }}"
                                                class="form-control">
                                        </div>

                                        {{-- Tipe pembayaran --}}
                                        <div class="mb-3">
                                            <label class="form-label">Tipe Pembayaran</label>
                                            <select name="tipe_harga" class="form-select">
                                                <option value="bulanan"
                                                    {{ $kamar->tipe_harga == 'bulanan' ? 'selected' : '' }}>
                                                    Perbulan
                                                </option>
                                                <option value="tahunan"
                                                    {{ $kamar->tipe_harga == 'tahunan' ? 'selected' : '' }}>
                                                    Pertahun
                                                </option>
                                            </select>
                                        </div>

                                    </div>

                                </div>

                                {{-- BUTTON --}}
                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('pemilik.kamar.index') }}" class="btn btn-danger me-2">
                                        <i class="bi bi-x-circle me-1"></i> Batal
                                    </a>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i> Update
                                    </button>
                                </div>

                            </form>

                        </div>
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
