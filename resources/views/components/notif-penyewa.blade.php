@php
    $userId = Auth::id();
    $notifReadKeys = session('penyewa_notif_read_keys', []);

    $notifPengajuan = \App\Models\PengajuanSewa::where('user_id', $userId)
        ->where('status', 'disetujui')
        ->latest()
        ->get();

    $notifPembayaran = \App\Models\Pembayaran::whereHas('pengajuan', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->whereIn('status', ['dikonfirmasi', 'ditolak'])
        ->latest()
        ->get();

    $notifSewaHabis = \App\Models\PengajuanSewa::with('kos')
        ->where('user_id', $userId)
        ->whereIn('status', ['aktif', 'jatuh_tempo', 'selesai'])
        ->latest()
        ->get()
        ->map(function (\App\Models\PengajuanSewa $item) {
            $sisaHari = $item->sisaHariSewa();

            return [
                'id' => $item->id,
                'nama_kos' => $item->kos->nama_kos ?? '-',
                'sisa_hari' => $sisaHari,
                'kategori' => $sisaHari < 0 ? 'lewat' : 'menjelang',
                'can_extend' => $item->bisaAjukanPerpanjangan(),
                'created_at' => $item->updated_at ?? $item->created_at,
            ];
        })
        ->filter(function (array $item) {
            // Tampilkan notifikasi jika sisa hari <= 5 (menjelang habis atau sudah lewat/jatuh tempo)
            return $item['sisa_hari'] <= 5 && $item['can_extend'];
        })
        ->values();

    $notifHarusBayar = \App\Models\PengajuanSewa::with(['kos', 'kamar', 'pembayarans'])
        ->where('user_id', $userId)
        ->whereIn('status', ['aktif', 'jatuh_tempo'])
        ->latest()
        ->get()
        ->filter(function (\App\Models\PengajuanSewa $item) {
            return $item->statusSaatIni() === 'jatuh_tempo' && ! $item->adaPembayaranMenunggu();
        })
        ->map(function (\App\Models\PengajuanSewa $item) {
            return [
                'id' => $item->id,
                'nama_kos' => $item->kos->nama_kos ?? '-',
                'nominal' => $item->nominalTagihanBerjalan(),
                'created_at' => $item->updated_at ?? $item->created_at,
            ];
        })
        ->values();

    $notifTimeline = collect();

    foreach ($notifPengajuan as $p) {
        $notifTimeline->push([
            'judul' => 'Pengajuan Sewa Disetujui',
            'pesan' => 'Pengajuan sewa untuk kos ' . ($p->nama_kos ?? '-') . ' telah disetujui pemilik.',
            'url' => route('penyewa.notif.pengajuan', $p->id),
            'is_unread' => (int) $p->status_notif === 0,
            'created_at' => $p->created_at,
        ]);
    }

    foreach ($notifPembayaran as $pb) {
        $notifTimeline->push([
            'judul' => 'Status Pembayaran',
            'pesan' =>
                'Pembayaran kos ' .
                ($pb->nama_kos ?? '-') .
                ' ' .
                ($pb->status === 'dikonfirmasi' ? 'telah dikonfirmasi.' : 'ditolak. Silakan periksa alasan penolakan.'),
            'url' => route('penyewa.notif.pembayaran', $pb->id),
            'is_unread' => (int) $pb->status_notif === 0,
            'created_at' => $pb->created_at,
        ]);
    }

    foreach ($notifHarusBayar as $tagihan) {
        $notifKey = 'tagihan:' . $tagihan['id'];

        $notifTimeline->push([
            'judul' => 'Tagihan Perlu Dibayar',
            'pesan' =>
                'Tagihan kos ' .
                $tagihan['nama_kos'] .
                ' belum dibayar. Nominal Rp ' .
                number_format($tagihan['nominal'], 0, ',', '.') .
                '.',
            'url' => route('penyewa.notif.read', [
                'key' => $notifKey,
                'target' => 'pengajuan',
                'focus_bayar' => $tagihan['id'],
            ]),
            'is_unread' => ! array_key_exists($notifKey, $notifReadKeys),
            'created_at' => $tagihan['created_at'] ?? now(),
        ]);
    }

    foreach ($notifSewaHabis as $sewa) {
        $notifKey = 'sewa_habis:' . $sewa['id'] . ':' . $sewa['kategori'] . ':' . $sewa['sisa_hari'];

        $notifTimeline->push([
            'judul' => $sewa['kategori'] === 'lewat' ? 'Masa Sewa Sudah Habis' : 'Masa Sewa Akan Berakhir',
            'pesan' =>
                $sewa['kategori'] === 'lewat'
                    ? 'Sewa kos ' .
                        $sewa['nama_kos'] .
                        ' sudah lewat ' .
                        abs($sewa['sisa_hari']) .
                        ' hari. Segera perpanjang/bayar agar tidak otomatis selesai.'
                    : 'Sewa kos ' .
                        $sewa['nama_kos'] .
                        ' tersisa ' .
                        $sewa['sisa_hari'] .
                        ' hari. Silakan ajukan perpanjangan sewa.',
            'url' => route('penyewa.notif.read', [
                'key' => $notifKey,
                'target' => 'pengajuan',
                'focus_perpanjang' => $sewa['id'],
            ]),
            'is_unread' => ! array_key_exists($notifKey, $notifReadKeys),
            'created_at' => $sewa['created_at'] ?? now(),
        ]);
    }

    $notifTimeline = $notifTimeline->sortByDesc('created_at')->values();
    $totalNotif = $notifTimeline->where('is_unread', true)->count();
@endphp

<div class="dropdown position-relative">
    <button class="btn text-white position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-4"></i>

        @if ($totalNotif > 0)
            <span class="position-absolute start-50 translate-middle badge rounded-pill bg-danger"
                style="top:10px; font-size:10px;">
                {{ $totalNotif }}
            </span>
        @endif
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow notif-dropdown-menu">
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
            <li class="dropdown-item text-muted small">Belum ada notifikasi baru</li>
        @endif
    </ul>
</div>
