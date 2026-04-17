@php
    use App\Models\Kos;

    $notifKos = Kos::where(function ($query) {
        $query->where('status', 'menunggu')->orWhere('edit_request_status', 'menunggu');
    })
        ->with('user')
        ->latest()
        ->get();

    $readIds = session('admin_notif_read_ids', []);
    $notifTimeline = collect();

    foreach ($notifKos as $n) {
        $notifTimeline->push([
            'judul' => $n->edit_request_status === 'menunggu' ? 'Pengajuan Perubahan Data' : 'Pengajuan Kos Baru',
            'pesan' =>
                ($n->user->name ?? 'Pemilik') .
                ($n->edit_request_status === 'menunggu' ? ' mengajukan perubahan pada kos ' : ' mengajukan verifikasi kos ') .
                ($n->nama_kos ?? '-') .
                '.',
            'url' => route('admin.notif.read', $n->id),
            'id' => (string) $n->id,
            'is_unread' => ! array_key_exists((string) $n->id, $readIds),
            'created_at' => $n->updated_at ?? $n->created_at,
        ]);
    }

    $notifTimeline = $notifTimeline->sortByDesc('created_at')->values();

    $jumlahNotif = $notifTimeline->count();
    $jumlahNotifBaru = $notifTimeline->where('is_unread', true)->count();
@endphp

{{-- 🔔 NOTIF --}}
<div class="dropdown position-relative">

    <button class="btn text-white position-relative p-2" data-bs-toggle="dropdown">

        <i class="bi bi-bell fs-4"></i>

        @if ($jumlahNotifBaru > 0)
            <span class="notif-badge">
                {{ $jumlahNotifBaru }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-2 notif-dropdown-menu" style="max-height:300px; overflow-y:auto;">

        <h6 class="dropdown-header">Notifikasi Verifikasi Kos</h6>

        @forelse($notifTimeline as $notif)
            <a href="{{ data_get($notif, 'url') }}" class="dropdown-item small py-2 notif-item">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                    <strong class="notif-title">{{ data_get($notif, 'judul') }}</strong>
                    <span class="badge {{ data_get($notif, 'is_unread') ? 'bg-primary' : 'bg-secondary' }} text-white notif-read-badge">
                        {{ data_get($notif, 'is_unread') ? 'Baru' : 'Terbaca' }}
                    </span>
                </div>
                <div class="notif-message">{{ data_get($notif, 'pesan') }}</div>
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
