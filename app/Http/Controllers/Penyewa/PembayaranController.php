<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'metode_id' => 'required',
            'bukti' => 'required|image|max:2048'
        ]);

        $pengajuan = PengajuanSewa::findOrFail($id);

        $buktiPath = $request->file('bukti')
                    ->store('bukti','public');

        // 🔥 INSERT KE TABEL PEMBAYARANS
        Pembayaran::create([
            'pengajuan_sewa_id' => $pengajuan->id,
            'metode_id' => $request->metode_id,
            'bukti' => $buktiPath,
            'status' => 'menunggu'
        ]);

        return back()->with('success','Pembayaran berhasil dikirim!');
    }
}
