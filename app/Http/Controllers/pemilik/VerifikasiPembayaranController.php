<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

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
            return back()->with('error', 'Pembayaran sudah diproses');
        }

        // Update status pembayaran
        $pembayaran->update([
            'status' => 'dikonfirmasi'
        ]);

        // Update status pengajuan
        $pembayaran->pengajuan->update([
            'status' => 'aktif',
        ]);

        // Update status kamar
        if ($pembayaran->pengajuan->kamar) {
            $pembayaran->pengajuan->kamar->update([
                'status' => 'terisi'
            ]);
        }

        return back()->with('success', 'Pembayaran dikonfirmasi & kamar terisi');
    }
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string'
        ]);

        $pembayaran = Pembayaran::with('pengajuan')
            ->findOrFail($id);

        $statusPengajuanSebelumTolak = optional($pembayaran->pengajuan)->status;

        $pembayaran->update([
            'status' => 'ditolak',
            'alasan' => $request->alasan
        ]);

        $pembayaran->pengajuan->update([
            'status' => $statusPengajuanSebelumTolak === 'aktif' ? 'aktif' : 'disetujui'
        ]);

        return back()->with('success', 'Pembayaran ditolak');
    }
}
