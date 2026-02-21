<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = PengajuanSewa::whereHas('kos', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('penyewa', 'kos', 'kamar')->get();

        return view('pemilik.pengajuan.index', compact('pengajuan'));
    }
    public function show($id)
    {
        $pengajuan = PengajuanSewa::with('penyewa', 'kos', 'kamar')
            ->findOrFail($id);

        return view('pemilik.pengajuan.detail', compact('pengajuan'));
    }

   public function approve($id)
{
    $p = PengajuanSewa::with('kamar')->findOrFail($id);

    $total = $p->kamar->harga * $p->durasi;

    $p->status = 'aktif';
    $p->total_bayar = $total;
    $p->save();

    return back()->with('success','Pengajuan disetujui!');
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

    return back()->with('success','Pengajuan ditolak!');
}
}
