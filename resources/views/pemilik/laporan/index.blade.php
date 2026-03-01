@extends('layouts.app')

@php
    use App\Models\Kos;
    use App\Models\PengajuanSewa;
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::id();
    $kosIds = Kos::where('user_id', $userId)->pluck('id');

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

    @include('components.sidebar-pemilik')

    <div class="flex-grow-1">

        {{-- ================= TOPBAR ================= --}}
        <div class="topbar d-flex justify-content-end align-items-center px-4 gap-1">

            {{-- NOTIF --}}
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

                <div class="dropdown-menu dropdown-menu-end p-2"
                     style="width:320px; max-height:300px; overflow-y:auto;">

                    <h6 class="dropdown-header">Notifikasi</h6>

                    @foreach ($notifKos as $n)
                        <a href="{{ url('/notif/kos/' . $n->id) }}" class="dropdown-item small py-2">
                            <strong>Kos Disetujui</strong><br>
                            Kos <strong>{{ $n->nama_kos }}</strong> telah disetujui admin
                        </a>
                    @endforeach

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

        {{-- ================= CONTENT ================= --}}
        <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

            <h3 class="fw-bold mb-1" style="font-size:28px;">
                Laporan Keuangan
            </h3>

            <small class="text-muted d-block mb-4">
                Rekapitulasi Data Pembayaran Penyewa
            </small>

            <div class="d-flex justify-content-end gap-2 mb-3">
                <a href="{{ route('pemilik.laporan.print') }}"
                   class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Print PDF
                </a>

                <a href="{{ route('pemilik.laporan.excel') }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>

            <div class="card shadow-sm rounded-4">

                <div class="card-header bg-dark text-white">
                    <i class="bi bi-journal-text me-2"></i>
                    <span class="fw-semibold">Data Laporan Pembayaran</span>
                </div>

                <div class="table-responsive">

                    <table class="table table-bordered mb-0 text-center">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>Nama Kamar</th>
                                <th>Tanggal Bayar</th>
                                <th>Metode Pembayaran</th>
                                <th>Total Bayar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($laporan as $item)
                                <tr class="align-middle">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($item->pengajuan->penyewa)->name ?? '-' }}</td>
                                    <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                    <td>{{ $item->metode->nama_metode }}</td>
                                    <td class="fw-semibold text-success">
                                        Rp {{ number_format($item->pengajuan->total_bayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Belum ada data pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if($laporan->count() > 0)
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="5" class="text-end">Total Keseluruhan</td>
                                <td class="text-success">
                                    Rp {{ number_format($laporan->sum(fn($i) => $i->pengajuan->total_bayar), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif

                    </table>

                </div>

            </div>
        </div>

    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-konfirmasi').forEach(button => {
                button.addEventListener('click', function() {

                    let id = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Konfirmasi Pembayaran?',
                        text: "Pembayaran akan disetujui dan kamar otomatis terisi!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Konfirmasi!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-konfirmasi-' + id).submit();
                        }
                    });

                });
            });

            document.querySelectorAll('.btn-tolak').forEach(button => {
                button.addEventListener('click', function() {

                    let id = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Tolak Pembayaran',
                        input: 'textarea',
                        inputLabel: 'Masukkan alasan penolakan',
                        inputPlaceholder: 'Contoh: Nominal pembayaran kurang / Bukti transfer tidak jelas / Data tidak valid',
                        inputAttributes: {
                            'aria-label': 'Masukkan alasan'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Tolak Pembayaran',
                        cancelButtonText: 'Batal',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Alasan wajib diisi!'
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let form = document.getElementById('form-tolak-' + id);

                            // buat input hidden untuk alasan
                            let input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "alasan";
                            input.value = result.value;

                            form.appendChild(input);

                            form.submit();
                        }
                    });

                });
            });

        });
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
