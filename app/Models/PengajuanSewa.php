<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PengajuanSewa extends Model
{
    protected $fillable = [
        'user_id',
        'kos_id',
        'kamar_id',
        'tanggal_mulai',
        'durasi',
        'status',
        'jenis_pengajuan',
        'total_bayar',
        'alasan',
        'is_read',
        'status_notif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];

    public function penyewa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pengajuan_sewa_id');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'pengajuan_sewa_id');
    }

    public function tanggalSelesai(): Carbon
    {
        return Carbon::parse($this->tanggal_mulai)->addMonths((int) $this->durasi)->endOfDay();
    }

    public function statusSaatIni(): string
    {
        // 1. Jika sudah melewati batas toleransi (grace period 2 hari), maka sewa SELESAI
        if ($this->isLewatBatasAkhirSewa(2)) {
            return 'selesai';
        }

        if ($this->status !== 'aktif' && $this->status !== 'jatuh_tempo') {
            return $this->status;
        }

        // 2. Jika hari ini sudah lewat tanggal selesai ATAU sisa hari sewa <= 5 hari, maka JATUH TEMPO
        // Ini berlaku sampai batas grace period di atas tercapai.
        if ($this->sisaHariSewa() <= 5) {
            return 'jatuh_tempo';
        }

        // Hapus logika isJatuhTempo bulanan agar tidak membingungkan user yang menyewa jangka panjang
        // (Misal sewa 1 tahun, tidak muncul jatuh tempo di bulan ke-1)

        return 'aktif';
    }

    public function sudahSelesai(): bool
    {
        return $this->statusSaatIni() === 'selesai';
    }

    public function bulanTagihanSaatIni(): int
    {
        $durasi = max((int) $this->durasi, 1);
        $tanggalMulai = Carbon::parse($this->tanggal_mulai)->startOfDay();
        $hariIni = now()->startOfDay();

        if ($hariIni->lt($tanggalMulai)) {
            return 1;
        }

        $bulanBerjalan = $tanggalMulai->diffInMonths($hariIni) + 1;

        return min($bulanBerjalan, $durasi);
    }

    public function tanggalJatuhTempoSaatIni(): Carbon
    {
        $bulanKe = $this->bulanTagihanSaatIni();

        return Carbon::parse($this->tanggal_mulai)
            ->addMonths($bulanKe - 1)
            ->startOfDay();
    }

    public function jumlahPembayaranTerkonfirmasi(): int
    {
        if ($this->relationLoaded('pembayarans')) {
            $durasi = $this->pembayarans
                ->where('status', 'dikonfirmasi')
                ->pluck('durasi_tagihan')
                ->map(function ($value) {
                    return (int) ($value ?: 1);
                })
                ->sum();

            return (int) $durasi;
        }

        return (int) $this->pembayarans()
            ->where('status', 'dikonfirmasi')
            ->sum('durasi_tagihan');
    }

    public function durasiBelumTerbayar(): int
    {
        return max((int) $this->durasi - $this->jumlahPembayaranTerkonfirmasi(), 0);
    }

    public function nominalTagihanBerjalan(): int
    {
        $hargaKamar = (int) optional($this->kamar)->harga;
        $durasiTagihan = max($this->durasiBelumTerbayar(), 1);

        if (optional($this->kamar)->tipe_harga === 'tahunan') {
            return (int) (($hargaKamar / 12) * $durasiTagihan);
        }

        return $hargaKamar * $durasiTagihan;
    }

    public function adaPembayaranMenunggu(): bool
    {
        if ($this->relationLoaded('pembayarans')) {
            return $this->pembayarans->where('status', 'menunggu')->isNotEmpty();
        }

        return $this->pembayarans()->where('status', 'menunggu')->exists();
    }

    public function isJatuhTempo(): bool
    {
        // Logika ini sekarang hanya sisa pendukung, tapi statusUtama sudah dihandle di statusSaatIni
        if (! in_array($this->status, ['aktif', 'jatuh_tempo'], true)) {
            return false;
        }

        return $this->sisaHariSewa() <= 5;
    }

    public function isLewatBatasAkhirSewa(int $batasHari = 2): bool
    {
        if (! in_array($this->status, ['aktif', 'jatuh_tempo'], true)) {
            return false;
        }

        // Grace period 2 hari setelah tanggal selesai sewa (misal selesai tgl 1, tgl 2 masih jatuh tempo, tgl 3 otomatis selesai)
        $batasAkhir = $this->tanggalSelesai()->copy()->startOfDay()->addDays($batasHari);

        return now()->startOfDay()->greaterThanOrEqualTo($batasAkhir);
    }

    public static function syncExpiredRentals(): int
    {
        $updatedCount = 0;

        static::with('kamar')
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->get()
            ->each(function (self $pengajuan) use (&$updatedCount) {
                if (! $pengajuan->sudahSelesai()) {
                    return;
                }

                $pengajuan->update(['status' => 'selesai']);
                $updatedCount++;

                if (
                    $pengajuan->kamar
                    && $pengajuan->kamar->status !== 'tersedia'
                    && ! $pengajuan->kamarMasihTerisiOlehSewaLain()
                ) {
                    $pengajuan->kamar->update(['status' => 'tersedia']);
                }
            });

        return $updatedCount;
    }

    public function sisaHariSewa(): int
    {
        return now()->startOfDay()->diffInDays($this->tanggalSelesai()->startOfDay(), false);
    }

    public function bisaAjukanPerpanjangan(): bool
    {
        $statusSaatIni = $this->statusSaatIni();

        if (in_array($statusSaatIni, ['aktif', 'jatuh_tempo'], true)) {
            return $this->sisaHariSewa() <= 5;
        }

        if ($statusSaatIni === 'selesai') {
            return ! $this->kamarMasihTerisiOlehSewaLain();
        }

        if (!in_array($statusSaatIni, ['aktif', 'jatuh_tempo', 'selesai'], true)) {
            return false;
        }

        return false;
    }

    public function kamarMasihTerisiOlehSewaLain(): bool
    {
        if (! $this->kamar_id) {
            return false;
        }

        return static::where('kamar_id', $this->kamar_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->exists();
    }

    public static function formatDurasiByTipe(int $durasi, ?string $tipeHarga): string
    {
        $durasi = max($durasi, 1);

        if ($tipeHarga === 'tahunan' && $durasi % 12 === 0) {
            return intdiv($durasi, 12) . ' Tahun';
        }

        return $durasi . ' Bulan';
    }
}
