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
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10], true)) {
            $perPage = 10;
        }

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

        // 📄 PAGINATION
        $riwayat = $query->latest()->paginate($perPage)->withQueryString();

        // 💰 TOTAL (AMAN, TIDAK ERROR)
        $total = (clone $query)->get()->sum(function ($item) {
            $harga = $item->pengajuan->kamar->harga ?? 0;
            $durasi = $item->pengajuan->durasi ?? 0;
            return $harga * $durasi;
        });

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
