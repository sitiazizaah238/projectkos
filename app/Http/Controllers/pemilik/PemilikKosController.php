<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kos;
use Illuminate\Support\Facades\Auth;

class PemilikKosController extends Controller
{
    // ================= LIST DATA =================
    public function index()
    {
        $kos = Kos::where('user_id', Auth::id())->latest()->get();
        return view('pemilik.kos.index', compact('kos'));
    }

    // ================= FORM TAMBAH =================
    public function create()
    {
        return view('pemilik.kos.create');
    }

    // ================= SIMPAN =================
    public function store(Request $request)
    {
        $request->validate([
            'nama_kos' => 'required',
            'lokasi' => 'required',
            'tipe_kos' => 'required',
            'foto' => 'nullable|image'
        ]);

        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('kos','public');
        }

        Kos::create([
            'user_id' => auth()->id(),
            'nama_kos' => $request->nama_kos,
            'lokasi' => $request->lokasi,
            'tipe_kos' => $request->tipe_kos,
            'deskripsi' => $request->deskripsi,
            'fasilitas' => $request->fasilitas, // checkbox array
            'foto' => $foto,
            'status' => 'menunggu'
        ]);

        // ✅ KHUSUS TAMBAH
      return redirect()->route('pemilik.kos.index')
    ->with('success','Kos berhasil diajukan');
    }
// ================= SHOW (WAJIB ADA) =================
    public function show($id)
    {
        $kos = Kos::findOrFail($id);
        return view('pemilik.kos.show', compact('kos'));
        // kalau belum punya view show, boleh sementara:
        // return redirect()->route('pemilik.kos.edit', $id);
    }
    // ================= FORM EDIT =================
    public function edit($id)
    {
        $kos = Kos::findOrFail($id);
        return view('pemilik.kos.edit', compact('kos'));
    }

public function update(Request $request, $id)
{
    $kos = Kos::findOrFail($id);

    $data = $request->all();

    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('kos', 'public');
    }

    $data['fasilitas'] = $request->fasilitas ?? [];

    $kos->update($data);

    return redirect()
        ->route('pemilik.kos.index')
        ->with('success','Data kos berhasil diperbarui');
}

    // ================= DELETE =================
    public function destroy($id)
    {
        Kos::destroy($id);

       return back()->with('success','Data berhasil dihapus');
    }
}
