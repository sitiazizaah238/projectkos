<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanSewa;
use App\Models\Kamar;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanSewa::with([
            'kos.user.metodePembayaran',
            'kamar',
            'pembayarans'
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('penyewa.pengajuan.index', compact('pengajuan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'kos_id' => 'required',
            'kamar_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'durasi' => 'required|integer'
        ]);

        $cek = PengajuanSewa::where('user_id', Auth::id())
            ->where('kamar_id', $request->kamar_id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if ($cek) {
            return back()->with('error', 'Kamu sudah mengajukan kamar ini!');
        }

        $kamar = Kamar::findOrFail($request->kamar_id);

        $totalBayar = $kamar->harga * $request->durasi;

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $request->kos_id,
            'kamar_id' => $request->kamar_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'durasi' => $request->durasi,
            'total_bayar' => $totalBayar,
            'status' => 'menunggu'
        ]);

        return redirect()->route('penyewa.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim!');
    }
}
