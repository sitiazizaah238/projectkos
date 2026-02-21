<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanSewa;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
   public function index()
{
      $pengajuan = PengajuanSewa::with([
    'kos.user.metodePembayaran',
    'kamar'
])
->where('user_id', Auth::id())
->latest()
->get();
        
    return view('penyewa.pengajuan.index', compact('pengajuan'));
}   public function store(Request $request)
    {
        $request->validate([
            'kos_id' => 'required',
            'kamar_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'durasi' => 'required|integer'
        ]);

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $request->kos_id,
            'kamar_id' => $request->kamar_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'durasi' => $request->durasi,
            'status' => 'menunggu'
        ]);

        return back()->with('success','Pengajuan berhasil dikirim!');
    }
}
