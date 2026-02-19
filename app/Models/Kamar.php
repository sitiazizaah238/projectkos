<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
        'kos_id',
        'nama_kamar',
        'deskripsi',
        'harga',
        'tipe_harga',
        'status',
        'foto',
        'fasilitas'
    ];
protected $casts = [
    'fasilitas' => 'array',
     'foto' => 'array'
];
    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }
    public function kamars()
{
    return $this->hasMany(Kamar::class);
}
public function getHargaTermurahAttribute()
{
    return $this->kamars()->min('harga');
}

}

