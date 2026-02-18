<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;
class KosController extends Controller
{
    // LIST SEMUA KOS DARI PEMILIK
   public function index(Request $request)
{
    $search = $request->search;

    $kos = Kos::with('user')
        ->when($search, function ($q) use ($search) {
            $q->where('nama_kos','like',"%$search%")
              ->orWhereHas('user', function($u) use ($search){
                  $u->where('name','like',"%$search%");
              });
        })
        ->latest()
        ->get();

    return view('admin.kos.index', compact('kos'));
}

    // SETUJUI
    public function approve($id)
    {
        $kos = Kos::findOrFail($id);
        $kos->update([
            'status' => 'disetujui',
            'alasan' => null
        ]);

        return back()->with('success','Kos disetujui');
    }

    // TOLAK
    public function reject($id)
    {
        $kos = Kos::findOrFail($id);
        $kos->update([
            'status' => 'ditolak',
            'alasan' => request('alasan')
        ]);

        return back()->with('success','Kos ditolak');
    }
}
