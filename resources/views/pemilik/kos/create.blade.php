@extends('layouts.app')

@section('content')
    <div class="d-flex">

        {{-- SIDEBAR --}}
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

            {{-- CONTENT --}}
            <div class="p-4">

                <h3 class="fw-bold">Tambah Kos</h3>
                <small class="text-muted"> Data Kos / Tambah</small>

                <div class="card shadow-sm mt-4">

                    <div class="card-header bg-dark text-white">
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
                                    <input type="file" name="foto" class="form-control">
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
@endsection
