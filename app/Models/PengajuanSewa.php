<?php

namespace App\Models;

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
        'alasan'
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
    return $this->hasOne(Pembayaran::class,'pengajuan_sewa_id');
}
}
