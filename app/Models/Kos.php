<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    protected $table = 'kos';

    protected $fillable = [
        'user_id',
        'nama_kos',
        'lokasi',
        'tipe_kos',
        'deskripsi',
         'fasilitas',
        'foto',
        'status'
    ];
protected $casts = [
    'fasilitas' => 'array'
];

    // relasi ke pemilik
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
