<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Halaman daftar chat untuk penyewa.
     */
    public function indexPenyewa()
    {
        $userId = Auth::id();

        $chats = Chat::with(['pemilik', 'kos', 'latestMessage'])
            ->where('penyewa_id', $userId)
            ->get()
            ->sortByDesc(function ($chat) {
                return optional($chat->latestMessage)->created_at ?? $chat->created_at;
            })
            ->values();

        return view('penyewa.chat.index', compact('chats'));
    }

    /**
     * Halaman daftar chat untuk pemilik.
     */
    public function indexPemilik()
    {
        $userId = Auth::id();

        $chats = Chat::with(['penyewa', 'kos', 'latestMessage'])
            ->where('pemilik_id', $userId)
            ->get()
            ->sortByDesc(function ($chat) {
                return optional($chat->latestMessage)->created_at ?? $chat->created_at;
            })
            ->values();

        return view('pemilik.chat.index', compact('chats'));
    }

    /**
     * Tampilkan detail percakapan chat.
     */
    public function show($id)
    {
        $userId = Auth::id();

        $chat = Chat::with(['penyewa', 'pemilik', 'kos', 'messages.sender'])
            ->where(function ($q) use ($userId) {
                $q->where('penyewa_id', $userId)
                  ->orWhere('pemilik_id', $userId);
            })
            ->findOrFail($id);

        // Tandai semua pesan lawan sebagai terbaca
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Determine role for back button
        $userRole = Auth::user()->role;

        return view('chat.show', compact('chat', 'userRole'));
    }

    /**
     * Kirim pesan baru.
     */
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = Auth::id();

        $chat = Chat::where(function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->orWhere('pemilik_id', $userId);
        })->findOrFail($chatId);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', $chat->id);
    }

    /**
     * Mulai chat baru dari halaman detail kos (penyewa).
     */
    public function startChat(Request $request)
    {
        $request->validate([
            'kos_id' => 'required|exists:kos,id',
        ]);

        $userId = Auth::id();
        $kos = Kos::findOrFail($request->kos_id);
        $pemilikId = $kos->user_id;

        // Cek apakah chat sudah ada
        $chat = Chat::firstOrCreate([
            'penyewa_id' => $userId,
            'pemilik_id' => $pemilikId,
            'kos_id' => $kos->id,
        ]);

        return redirect()->route('chat.show', $chat->id);
    }

    /**
     * API: ambil pesan baru (untuk polling).
     */
    public function getMessages($chatId)
    {
        $userId = Auth::id();

        $chat = Chat::where(function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->orWhere('pemilik_id', $userId);
        })->findOrFail($chatId);

        // Tandai pesan lawan sebagai terbaca
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = ChatMessage::with('sender')
            ->where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($userId) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_name' => $msg->sender->name,
                    'is_mine' => $msg->sender_id === $userId,
                    'time' => $msg->created_at->format('H:i'),
                    'date' => $msg->created_at->format('d M Y'),
                    'is_read' => $msg->is_read,
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    /**
     * API: kirim pesan via AJAX.
     */
    public function sendMessageAjax(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = Auth::id();

        $chat = Chat::where(function ($q) use ($userId) {
            $q->where('penyewa_id', $userId)
              ->orWhere('pemilik_id', $userId);
        })->findOrFail($chatId);

        $msg = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_name' => Auth::user()->name,
                'is_mine' => true,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->format('d M Y'),
                'is_read' => false,
            ],
        ]);
    }

    /**
     * API: hitung total unread untuk badge.
     */
    public function unreadCount()
    {
        $count = Chat::totalUnreadFor(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * API: ambil daftar chat sebagai JSON (untuk polling realtime di list).
     */
    public function chatListJson()
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role;

        if ($userRole === 'penyewa') {
            $chats = Chat::with(['pemilik', 'kos', 'latestMessage'])
                ->where('penyewa_id', $userId)
                ->get();
        } else {
            $chats = Chat::with(['penyewa', 'kos', 'latestMessage'])
                ->where('pemilik_id', $userId)
                ->get();
        }

        $result = $chats->sortByDesc(function ($chat) {
            return optional($chat->latestMessage)->created_at ?? $chat->created_at;
        })->values()->map(function ($chat) use ($userId, $userRole) {
            $partner = $userRole === 'penyewa' ? $chat->pemilik : $chat->penyewa;
            $latest = $chat->latestMessage;
            $unread = $chat->unreadCountFor($userId);

            $data = [
                'id' => $chat->id,
                'partner_name' => $partner->name ?? '-',
                'partner_initial' => strtoupper(substr($partner->name ?? '-', 0, 1)),
                'kos_name' => $chat->kos->nama_kos ?? '-',
                'last_message' => $latest ? \Illuminate\Support\Str::limit($latest->message, 50) : null,
                'last_message_is_mine' => $latest ? $latest->sender_id === $userId : false,
                'last_message_time' => $latest
                    ? ($latest->created_at->isToday()
                        ? $latest->created_at->format('H:i')
                        : $latest->created_at->format('d/m'))
                    : null,
                'unread' => $unread,
                'booking_label' => null,
            ];

            // Untuk pemilik: cek apakah penyewa ini sudah booking di kos milik pemilik ini
            if ($userRole === 'pemilik') {
                $booking = \App\Models\PengajuanSewa::with('kamar')
                    ->where('user_id', $chat->penyewa_id)
                    ->whereHas('kos', function ($q) use ($userId) {
                        $q->where('user_id', $userId); // hanya kos milik pemilik ini
                    })
                    ->whereIn('status', ['disetujui', 'aktif'])
                    ->latest()
                    ->first();

                if ($booking && $booking->kamar) {
                    $data['booking_label'] = 'Penyewa di ' . ($booking->kos->nama_kos ?? '-') . ' - ' . $booking->kamar->nama_kamar;
                } else {
                    $data['booking_label'] = 'Tanya tentang ' . ($chat->kos->nama_kos ?? '-');
                }
            }

            return $data;
        });

        return response()->json(['chats' => $result]);
    }
}
