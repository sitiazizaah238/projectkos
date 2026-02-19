<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kos;

class KosController extends Controller
{
    public function show($id)
    {
        $kos = Kos::with(['kamars' => function ($query) {
            $query->where('status', 'tersedia');
        }])->findOrFail($id);

        return view('penyewa.detail', compact('kos'));
    }
}
