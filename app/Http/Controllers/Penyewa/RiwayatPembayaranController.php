<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class RiwayatPembayaranController extends Controller
{
    // ======================
    // LIST RIWAYAT
    // ======================
    public function index(Request $request)
    {
        $userId = Auth::id();


        $query = Pembayaran::with([
            'pengajuan.kos',
            'pengajuan.kamar',
            'pengajuan.penyewa' // ✅ TAMBAH INI (biar aman)
        ])
            ->whereHas('pengajuan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        // 🔍 SEARCH
        if ($request->search) {
            $query->whereHas('pengajuan.kos', function ($q) use ($request) {
                $q->where('nama_kos', 'like', '%' . $request->search . '%');
            });
        }

        // 📌 FILTER STATUS
        $query->whereIn('status', ['dikonfirmasi', 'menunggu', 'ditolak']);

        $riwayat = $query->latest()
            ->paginate(5)
            ->withQueryString();
 
        // 💰 TOTAL (Mengambil dari nominal_tagihan yang sudah tersimpan di DB)
        $total = (clone $query)->where('status', 'dikonfirmasi')->sum('nominal_tagihan');

        return view('penyewa.riwayat-pembayaran.index', compact('riwayat', 'total'));
    }

    // ======================
    // DETAIL RIWAYAT
    // ======================
    public function show($id)
    {
        $data = Pembayaran::with([
            'pengajuan.kos',
            'pengajuan.kamar',
            'pengajuan.penyewa' // ❗ INI YANG BENER
        ])->findOrFail($id);

        return view('penyewa.riwayat-pembayaran.detail', compact('data'));
    }
}
