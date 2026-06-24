<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'penyewa_id',
        'pemilik_id',
        'kos_id',
    ];

    public function penyewa()
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * Hitung jumlah pesan yang belum dibaca oleh user tertentu.
     */
    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Jumlah CHAT (orang) yang punya pesan belum dibaca untuk user ini.
     * Misal: 1 orang kirim 10 pesan → hitung 1, bukan 10.
     */
    public static function totalUnreadFor(int $userId): int
    {
        return static::where(function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->orWhere('pemilik_id', $userId);
        })
        ->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', '!=', $userId)
              ->where('is_read', false);
        })
        ->count();
    }
}
