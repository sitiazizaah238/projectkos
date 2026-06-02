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
        'alasan',
        'status',
        'is_read',
        'tanggal_verifikasi',
        'edit_request_status',
        'edit_request_data',
        'edit_request_alasan',
        'edit_requested_at',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'foto' => 'array',
        'fasilitas' => 'array',
        'edit_request_data' => 'array',
        'tanggal_verifikasi' => 'datetime',
        'edit_requested_at' => 'datetime',
    ];

    // relasi ke pemilik
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class);
    }

    public function punyaPengajuanEditAktif(): bool
    {
        return $this->edit_request_status === 'menunggu';
    }
}
