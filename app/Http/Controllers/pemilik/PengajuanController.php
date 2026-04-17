<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
  public function index(Request $request)
{
    $search = $request->search;

    $pengajuan = PengajuanSewa::whereHas('kos', function ($q) {
        $q->where('user_id', Auth::id());
    })
    ->when($search, function ($q) use ($search) {
        $q->where(function ($sub) use ($search) {

            $sub->whereHas('penyewa', function ($p) use ($search) {
                    $p->where('name', 'like', "%$search%");
                })
                ->orWhereHas('kos', function ($k) use ($search) {
                    $k->where('nama_kos', 'like', "%$search%");
                })
                ->orWhereHas('kamar', function ($km) use ($search) {
                    $km->where('nama_kamar', 'like', "%$search%");
                });

        });
    })
    ->with('penyewa', 'kos', 'kamar', 'pembayarans')
    ->latest()
    ->paginate(10);

    return view('pemilik.pengajuan.index', compact('pengajuan'));
}
    public function show($id)
    {
        $pengajuan = PengajuanSewa::with('penyewa', 'kos', 'kamar', 'pembayarans')
            ->findOrFail($id);

        return view('pemilik.pengajuan.detail', compact('pengajuan'));
    }

    public function approve($id)
    {
        $p = PengajuanSewa::with('kamar')->findOrFail($id);

        $total = $p->kamar->harga * $p->durasi;

        $p->update([
            'status' => 'disetujui',
            'total_bayar' => $total
        ]);

        return back()->with('success', 'Pengajuan disetujui! Silakan tunggu pembayaran.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required'
        ]);

        $p = PengajuanSewa::findOrFail($id);
        $p->status = 'ditolak';
        $p->alasan = $request->alasan;
        $p->save();

        return back()->with('success', 'Pengajuan ditolak!');
    }
    public function konfirmasiPembayaran($id)
    {
        $p = PengajuanSewa::with('kamar')->findOrFail($id);

        // Pastikan sudah ada pembayaran yang tersimpan
        if (!$p->pembayaran || !$p->pembayaran->bukti) {
            return back()->with('error', 'Belum ada bukti pembayaran.');
        }

        // Ubah status pengajuan jadi aktif (jika belum)
        $p->status = 'aktif';
        $p->save();

        if ($p->kamar) {
            $p->kamar->status = 'terisi';
            $p->kamar->save();
        }

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi & kamar menjadi terisi.');
    }
}
