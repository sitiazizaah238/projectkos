<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'pengajuan_sewa_id',
        'metode_id',
        'bukti',
        'durasi_tagihan',
        'nominal_tagihan',
        'status',
        'status_notif',
        'is_read',
        'alasan'
    ];

    protected $casts = [
        'durasi_tagihan' => 'integer',
        'nominal_tagihan' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSewa::class, 'pengajuan_sewa_id');
    }

    public function metode()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_id');
    }
}
