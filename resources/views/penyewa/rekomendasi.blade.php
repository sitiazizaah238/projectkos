@extends('layouts.app')

@section('content')
    <div class="d-flex">
        @include('components.sidebar-penyewa')

        <div class="flex-grow-1">
            <div class="topbar d-flex justify-content-end align-items-center px-4">
                <span class="text-white me-2">{{ Auth::user()->name }}</span>
                <i class="bi bi-person-circle fs-3 text-white"></i>
            </div>

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">
                <h3 class="fw-bold">Rekomendasi Untuk Anda</h3>
                <p class="text-muted">Berdasarkan preferensi pencarian dan riwayat Anda.</p>

                <div class="row g-4 mt-2">
                    @forelse($rekomendasi as $k)
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100" style="border-radius:16px; overflow:hidden;">
                                {{-- Label Persentase AI [cite: 80, 86] --}}
                                <div class="position-absolute p-2">
                                    <span
                                        class="badge {{ $k->similarity_score >= 80 ? 'bg-success' : 'bg-primary' }} shadow">
                                        {{ round($k->similarity_score) }}% - {{ $k->label }}
                                    </span>
                                </div>

                                <img src="{{ asset('storage/' . $k->foto) }}" class="card-img-top"
                                    style="height:200px; object-fit:cover;">

                                <div class="card-body">
                                    <h5 class="fw-bold mb-1">{{ $k->nama_kos }}</h5>
                                    <small class="text-muted d-block mb-2"><i class="bi bi-geo-alt"></i>
                                        {{ $k->lokasi }}</small>

                                    <div class="d-flex gap-1 mb-3">
                                        <span class="badge bg-light text-dark border">{{ ucfirst($k->tipe_kos) }}</span>
                                        @if ($k->fasilitas)
                                            <span class="badge bg-light text-dark border">{{ count($k->fasilitas) }}
                                                Fasilitas</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('penyewa.kos.detail', $k->id) }}" class="btn btn-primary w-100">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
                                <i class="bi bi-search fs-1 text-primary mb-3"></i>
                                <h5 class="fw-bold">Belum Ada Rekomendasi</h5>
                                <p class="text-muted">
                                    Kami belum mengenal seleramu. Silakan gunakan fitur
                                    <strong>"Cari Kos"</strong> dan gunakan filter untuk membantu AI kami bekerja.
                                </p>
                                <div class="mt-2">
                                    <a href="{{ route('penyewa.cari.kos') }}"
                                        class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-search me-1"></i> Mulai Mencari Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
