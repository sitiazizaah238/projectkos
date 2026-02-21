<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'alamat',
        'password',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }
    public function kos()
    {
        return $this->hasMany(\App\Models\Kos::class);
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }
    public function metodePembayaran()
{
    return $this->hasMany(MetodePembayaran::class);
}
}
