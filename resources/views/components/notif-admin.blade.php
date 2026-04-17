@php
    use App\Models\Kos;

    $notifKos = Kos::where(function ($query) {
        $query->where('status', 'menunggu')->orWhere('edit_request_status', 'menunggu');
    })
        ->with('user')
        ->latest()
        ->get();

    $jumlahNotif = $notifKos->count();

    // Kalau ada notif baru → reset status dibaca
    if ($jumlahNotif > 0) {
        session()->forget('notif_dibaca');
    }
@endphp

{{-- 🔔 NOTIF --}}
<div class="dropdown position-relative">

    <button class="btn text-white position-relative p-2" data-bs-toggle="dropdown">

        <i class="bi bi-bell fs-4"></i>

        @if ($jumlahNotif > 0 && !session('notif_dibaca'))
            <span class="notif-badge">
                {{ $jumlahNotif }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-2" style="width:300px; max-height:300px; overflow-y:auto;">

        <h6 class="dropdown-header">Notifikasi Verifikasi Kos</h6>

        @forelse($notifKos as $n)
            <a href="{{ route('admin.kos.index') }}" class="dropdown-item small py-2">
                @if ($n->edit_request_status === 'menunggu')
                    <strong>Pengajuan Perubahan Data</strong><br>
                    {{ $n->user->name }} mengajukan perubahan pada kos <strong>{{ $n->nama_kos }}</strong>.
                @else
                    <strong>Pengajuan Kos Baru</strong><br>
                    {{ $n->user->name }} mengajukan verifikasi kos <strong>{{ $n->nama_kos }}</strong>.
                @endif
            </a>
        @empty
            <div class="text-center text-muted small p-3">
                Tidak ada notifikasi
            </div>
        @endforelse

    </div>
</div>

<style>
    .notif-badge {
        position: absolute;
        top: 4px;
        right: 6px;
        background: #dc3545;
        color: white;
        font-size: 10px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>
