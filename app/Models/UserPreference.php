<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = ['user_id', 'pref_harga', 'pref_tipe_kos', 'pref_fasilitas', 'pref_tipe_harga'];

    protected $casts = [
        'pref_fasilitas' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
