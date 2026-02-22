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

    $notifKos = Kos::where('user_id', $userId)
                ->where('status', 'disetujui')
                ->where('is_read', false)
                ->latest()
                ->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)
                        ->where('is_read', false)
                        ->latest()
                        ->get();

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
                    Edit Data Kos</h3>
                <small class="text-muted">Manajemen Kos/Data Kos</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-house-door-fill me-2"></i>
                        Form Edit Kos
                    </div>

                    <div class="card-body">

                        <form action="{{ route('pemilik.kos.update', $kos->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <div class="col-md-6">
                                    <label>Nama Kos</label>
                                    <input type="text" name="nama_kos" value="{{ $kos->nama_kos }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Foto Kos</label>
                                    <input type="file" name="foto" class="form-control">

                                    @if ($kos->foto)
                                        <img src="{{ asset('storage/' . $kos->foto) }}" width="120" class="mt-2"
                                            style="border-radius:8px">
                                    @endif
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Lokasi Kos</label>
                                    <input type="text" name="lokasi" value="{{ $kos->lokasi }}" class="form-control">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Tipe Kos</label>
                                    <select name="tipe_kos" class="form-control">
                                        <option value="Putra" {{ $kos->tipe_kos == 'Putra' ? 'selected' : '' }}>Putra</option>
                                        <option value="Putri" {{ $kos->tipe_kos == 'Putri' ? 'selected' : '' }}>Putri</option>
                                        <option value="Campur" {{ $kos->tipe_kos == 'Campur' ? 'selected' : '' }}>Campur</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control">{{ $kos->deskripsi }}</textarea>
                                </div>

                                {{-- FASILITAS --}}
                                <div class="col-md-12 mt-3">
                                    <label>Fasilitas</label>
                                    <div class="row mt-2">

                                        @php
                                            $fasilitas = $kos->fasilitas ?? [];
                                        @endphp

                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Parkir"
                                                {{ in_array('Parkir', $fasilitas) ? 'checked' : '' }}>
                                            Tempat Parkir
                                        </div>

                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="CCTV"
                                                {{ in_array('CCTV', $fasilitas) ? 'checked' : '' }}>
                                            CCTV
                                        </div>

                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Mushola"
                                                {{ in_array('Mushola', $fasilitas) ? 'checked' : '' }}>
                                            Mushola
                                        </div>

                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Dapur"
                                                {{ in_array('Dapur', $fasilitas) ? 'checked' : '' }}>
                                            Dapur Bersama
                                        </div>

                                        <div class="col-md-4">
                                            <input type="checkbox" name="fasilitas[]" value="Wifi"
                                                {{ in_array('Wifi', $fasilitas) ? 'checked' : '' }}>
                                            Wifi
                                        </div>

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
@endsection
