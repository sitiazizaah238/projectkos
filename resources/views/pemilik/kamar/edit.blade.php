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
                                        @foreach (['Kamar Mandi Dalam', 'AC', 'Kipas Angin', 'Cermin', 'Lemari Pakaian', 'Meja', 'Tempat Tidur'] as $f)
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
                                        <input type="file" name="foto[]" class="form-control" multiple accept="image/*">
                                        <small class="text-muted">
                                            Kosongkan jika tidak ingin mengganti foto
                                        </small>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-3">
                                        <label class="form-label">Status Kamar</label>
                                        <select name="status" class="form-select">
                                            <option value="tersedia" {{ $kamar->status == 'tersedia' ? 'selected' : '' }}>
                                                Tersedia
                                            </option>
                                            <option value="terisi" {{ $kamar->status == 'terisi' ? 'selected' : '' }}>
                                                Terisi
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Harga --}}
                                    <div class="mb-3">
                                        <label class="form-label">Harga</label>
                                        <input type="text" name="harga"
                                            value="Rp {{ number_format($kamar->harga, 0, ',', '.') }}"
                                            class="form-control">
                                    </div>

                                    {{-- Tipe Harga --}}
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Harga</label>
                                        <select name="tipe_harga" class="form-select">
                                            <option value="bulanan"
                                                {{ $kamar->tipe_harga == 'bulanan' ? 'selected' : '' }}>
                                                Bulanan
                                            </option>
                                            <option value="tahunan"
                                                {{ $kamar->tipe_harga == 'tahunan' ? 'selected' : '' }}>
                                                Tahunan
                                            </option>
                                        </select>
                                    </div>

                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('pemilik.kamar.index') }}" class="btn btn-danger me-2">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

