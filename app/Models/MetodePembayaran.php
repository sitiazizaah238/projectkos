<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $fillable = [
        'user_id',
        'nama_metode',
        'atas_nama',
        'no_rekening',
        'gambar',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class,'metode_id');
    }
}
