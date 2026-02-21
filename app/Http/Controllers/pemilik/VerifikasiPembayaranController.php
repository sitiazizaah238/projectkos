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
    $data = \App\Models\PengajuanSewa::with('kamar')->findOrFail($id);

    if ($data->status_pembayaran !== 'menunggu') {
        return back()->with('error','Pembayaran sudah diproses');
    }

    $data->update([
        'status_pembayaran' => 'dikonfirmasi',
        'status' => 'disetujui'
    ]);

    if ($data->kamar && $data->kamar->status === 'tersedia') {
        $data->kamar->update([
            'status' => 'terisi'
        ]);
    }

    return back()->with('success','Pembayaran dikonfirmasi & kamar terisi');
}

    public function tolak($id)
    {
        $data = Pembayaran::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        return back()->with('success','Pembayaran ditolak');
    }
}
