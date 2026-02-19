<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;
class KosController extends Controller
{
    public function index(Request $request)
{
    $search = $request->search;

    $kos = \App\Models\Kos::with('kamars')
        ->where('status', 'disetujui') // 🔥 FILTER WAJIB
        ->when($search, function ($query) use ($search) {
            $query->where('nama_kos', 'like', "%$search%")
                  ->orWhere('lokasi', 'like', "%$search%");
        })
        ->latest()
        ->get();

    return view('penyewa.cari', compact('kos', 'search'));
}
    public function show($id)
    {
        $kos = Kos::with(['kamars' => function ($query) {
            $query->where('status', 'tersedia');
        }])->findOrFail($id);

        return view('penyewa.detail', compact('kos'));
    }
}
