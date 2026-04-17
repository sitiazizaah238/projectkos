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
        if ($this->status !== 'aktif') {
            return $this->status;
        }

        if ($this->isJatuhTempo()) {
            return 'jatuh_tempo';
        }

        if (
            now()->greaterThan($this->tanggalSelesai())
            && $this->jumlahPembayaranTerkonfirmasi() >= max((int) $this->durasi, 1)
        ) {
            return 'selesai';
        }

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
        if ($this->status !== 'aktif') {
            return false;
        }

        $targetKonfirmasi = $this->bulanTagihanSaatIni();
        $sudahKonfirmasi = $this->jumlahPembayaranTerkonfirmasi();

        return now()->greaterThanOrEqualTo($this->tanggalJatuhTempoSaatIni())
            && $sudahKonfirmasi < $targetKonfirmasi;
    }

    public static function syncExpiredRentals(): int
    {
        $updatedCount = 0;

        static::with('kamar')
            ->where('status', 'aktif')
            ->get()
            ->each(function (self $pengajuan) use (&$updatedCount) {
                if (! $pengajuan->sudahSelesai()) {
                    return;
                }

                $pengajuan->update(['status' => 'selesai']);
                $updatedCount++;

                if ($pengajuan->kamar && $pengajuan->kamar->status !== 'tersedia') {
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
        if (!in_array($this->statusSaatIni(), ['aktif', 'jatuh_tempo'], true)) {
            return false;
        }

        $sisaHari = $this->sisaHariSewa();

        return $sisaHari <= 5;
    }
}
