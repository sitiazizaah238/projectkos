<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;

class VerifikasiPembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with([
            'pengajuan.penyewa',
            'pengajuan.kamar',
            'pengajuan.kos',
            'metode'
        ])
        ->whereHas('pengajuan.kos', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->latest()
        ->get();

        return view('pemilik.verifikasi.index', compact('pembayaran'));
    }

  public function konfirmasi($id)
{
    $pembayaran = Pembayaran::with('pengajuan.kamar')
                    ->findOrFail($id);

    if ($pembayaran->status !== 'menunggu') {
        return back()->with('error','Pembayaran sudah diproses');
    }

    // 1️⃣ Update status pembayaran
    $pembayaran->update([
        'status' => 'dikonfirmasi'
    ]);

    // 2️⃣ Update status pengajuan
   $pembayaran->pengajuan->update([
    'status' => 'aktif',
    'status_pembayaran' => 'dikonfirmasi'
]);

    // 3️⃣ Update status kamar
    if ($pembayaran->pengajuan->kamar) {
        $pembayaran->pengajuan->kamar->update([
            'status' => 'terisi'
        ]);
    }

    return back()->with('success','Pembayaran dikonfirmasi & kamar terisi');
}
  public function tolak($id)
{
    $pembayaran = Pembayaran::with('pengajuan')
                    ->findOrFail($id);

    $pembayaran->update([
        'status' => 'ditolak'
    ]);

    $pembayaran->pengajuan->update([
        'status_pembayaran' => 'ditolak'
    ]);

    return back()->with('success','Pembayaran ditolak');
}
}
