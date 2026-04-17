@php
    use App\Models\Kos;
    use App\Models\PengajuanSewa;
    use App\Models\Pembayaran;

    $userId = Auth::id();
    $kosIds = Kos::where('user_id', $userId)->pluck('id');

    $notifKos = Kos::where('user_id', $userId)
        ->where('status', 'disetujui')
        ->latest()
        ->get();

    $notifPengajuan = PengajuanSewa::whereIn('kos_id', $kosIds)
        ->latest()
        ->get();

    $notifPembayaran = Pembayaran::whereHas('pengajuan.kos', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->where('status', 'menunggu')
        ->latest()
        ->get();

    $notifTimeline = collect();

    foreach ($notifKos as $n) {
        $notifTimeline->push([
            'judul' => 'Pembaruan Verifikasi Kos',
            'pesan' => 'Terdapat pembaruan status untuk kos ' . ($n->nama_kos ?? '-') . '.',
            'url' => url('/notif/kos/' . $n->id),
            'is_unread' => (int) $n->is_read === 0,
            'created_at' => $n->updated_at ?? $n->created_at,
        ]);
    }

    foreach ($notifPengajuan as $p) {
        $notifTimeline->push([
            'judul' => 'Pengajuan Sewa Baru',
            'pesan' =>
                (optional($p->penyewa)->name ?? 'Penyewa') .
                ' mengajukan sewa untuk kos ' .
                (optional($p->kos)->nama_kos ?? '-') .
                '.',
            'url' => url('/notif/pengajuan/' . $p->id),
            'is_unread' => (int) $p->is_read === 0,
            'created_at' => $p->created_at,
        ]);
    }

    foreach ($notifPembayaran as $pb) {
        $notifTimeline->push([
            'judul' => 'Pembayaran Baru',
            'pesan' =>
                'Penyewa ' .
                (optional($pb->pengajuan->penyewa)->name ?? '-') .
                ' telah mengirim pembayaran untuk kos ' .
                (optional($pb->pengajuan->kos)->nama_kos ?? '-') .
                '.',
            'url' => route('pemilik.notif.pembayaran', $pb->id),
            'is_unread' => (int) $pb->is_read === 0,
            'created_at' => $pb->created_at,
        ]);
    }

    $notifTimeline = $notifTimeline->sortByDesc('created_at')->values();
    $jumlahNotif = $notifTimeline->where('is_unread', true)->count();
@endphp

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

    <div class="dropdown-menu dropdown-menu-end p-2 notif-dropdown-menu" style="max-height:300px; overflow-y:auto;">
        <h6 class="dropdown-header">Notifikasi</h6>

        @foreach ($notifTimeline as $notif)
            <a href="{{ data_get($notif, 'url') }}" class="dropdown-item small py-2 notif-item">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                    <strong class="notif-title">{{ data_get($notif, 'judul') }}</strong>
                    <span class="badge {{ data_get($notif, 'is_unread') ? 'bg-primary' : 'bg-secondary' }} text-white notif-read-badge">
                        {{ data_get($notif, 'is_unread') ? 'Baru' : 'Terbaca' }}
                    </span>
                </div>
                <div class="notif-message">{{ data_get($notif, 'pesan') }}</div>
            </a>
        @endforeach

        @if ($notifTimeline->isEmpty())
            <div class="text-center text-muted small p-3">Belum ada notifikasi baru</div>
        @endif
    </div>
</div>
