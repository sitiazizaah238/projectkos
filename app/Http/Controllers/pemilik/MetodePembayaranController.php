<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MetodePembayaran;
use Illuminate\Support\Facades\Auth;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metode = MetodePembayaran::where('user_id', Auth::id())->get();
        return view('pemilik.pembayaran.index', compact('metode'));
    }

    public function create()
    {
        return view('pemilik.pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_metode' => 'required',
            'atas_nama' => 'required',
            'no_rekening' => 'nullable',
            'gambar' => 'nullable|image'
        ]);

        $gambarPath = null;

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('metode', 'public');
        }

        MetodePembayaran::create([
            'user_id' => Auth::id(),
            'nama_metode' => $request->nama_metode,
            'atas_nama' => $request->atas_nama,
            'no_rekening' => $request->no_rekening,
            'gambar' => $gambarPath,
            'status' => $request->status
        ]);

        return redirect()->route('pemilik.pembayaran.index')
            ->with('success','Metode berhasil ditambahkan');
    }

    public function destroy($id)
    {
        MetodePembayaran::findOrFail($id)->delete();
        return back();
    }
    public function edit($id)
{
    $metode = MetodePembayaran::where('user_id', auth()->id())
                ->findOrFail($id);

    return view('pemilik.pembayaran.edit', compact('metode'));
}


public function update(Request $request, $id)
{
    $metode = MetodePembayaran::where('user_id', auth()->id())
                ->findOrFail($id);

    $request->validate([
        'nama_metode' => 'required',
        'atas_nama' => 'required',
        'no_rekening' => 'nullable',
        'gambar' => 'nullable|image'
    ]);

    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('metode', 'public');
        $metode->gambar = $gambarPath;
    }

    $metode->update([
        'nama_metode' => $request->nama_metode,
        'atas_nama' => $request->atas_nama,
        'no_rekening' => $request->no_rekening,
        'status' => $request->status
    ]);

    return redirect()->route('pemilik.pembayaran.index')
        ->with('success','Metode berhasil diupdate');
}
}
