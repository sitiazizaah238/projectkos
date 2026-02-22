@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            {{-- TOPBAR --}}
            <div class="topbar d-flex justify-content-end align-items-center px-4">
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
