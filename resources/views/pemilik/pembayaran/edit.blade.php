@extends('layouts.app')

@section('content')
<div class="d-flex">

@include('components.sidebar-pemilik')

<div class="flex-grow-1">

<div class="p-4" style="background:#f5f7fb; min-height:100vh;">

<h3 class="fw-bold mb-4">Edit Metode</h3>

<div class="card shadow-sm rounded-4 p-4">

<form action="{{ route('pemilik.pembayaran.update', $metode->id) }}"
      method="POST"
      enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="mb-3">
<label>Nama Metode / Bank</label>
<input type="text" name="nama_metode"
       class="form-control"
       value="{{ $metode->nama_metode }}">
</div>

<div class="mb-3">
<label>Atas Nama</label>
<input type="text" name="atas_nama"
       class="form-control"
       value="{{ $metode->atas_nama }}">
</div>

<div class="mb-3">
<label>No Rekening</label>
<input type="text" name="no_rekening"
       class="form-control"
       value="{{ $metode->no_rekening }}">
</div>

<div class="mb-3">
<label>Gambar</label>
<input type="file" name="gambar" class="form-control">

@if($metode->gambar)
    <div class="mt-2">
        <img src="{{ asset('storage/'.$metode->gambar) }}" width="100">
    </div>
@endif
</div>

<div class="mb-3">
<label>Status</label>
<select name="status" class="form-control">
    <option value="aktif"
        {{ $metode->status == 'aktif' ? 'selected' : '' }}>
        Aktif
    </option>
    <option value="nonaktif"
        {{ $metode->status == 'nonaktif' ? 'selected' : '' }}>
        Non-Aktif
    </option>
</select>
</div>

<div class="d-flex justify-content-end gap-2">
<a href="{{ route('pemilik.pembayaran.index') }}"
   class="btn btn-danger">
   Batal
</a>

<button class="btn btn-primary">
   Simpan
</button>
</div>

</form>

</div>
</div>
</div>
</div>
@endsection
