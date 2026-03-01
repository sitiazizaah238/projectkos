<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PemilikKamarController extends Controller
{
   public function index(Request $request)
{
    $search = $request->search;

    $kamars = Kamar::whereHas('kos', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kamar', 'like', '%' . $search . '%')
                  ->orWhereHas('kos', function ($k) use ($search) {
                      $k->where('nama_kos', 'like', '%' . $search . '%');
                  });
            });
        })
        ->with('kos')
        ->latest()
        ->get();

    return view('pemilik.kamar.index', compact('kamars', 'search'));
}


    public function create()
    {
        $kos = Kos::where('user_id', Auth::id())
            ->where('status', 'disetujui')
            ->get();

        return view('pemilik.kamar.create', compact('kos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kos_id'     => 'required|exists:kos,id',
            'nama_kamar' => 'required|string|max:255',
            'harga'      => 'required',
            'foto'       => 'required|array|min:1|max:3',
            'foto.*'     => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $harga = str_replace(['Rp', '.', ' '], '', $request->harga);
        if (strlen($harga) <= 3) {
            $harga = $harga . "000";
        }

        $data = $request->except('foto');
        $data['harga'] = $harga;

        if ($request->hasFile('foto')) {
            $paths = [];
            foreach ($request->file('foto') as $file) {
                $paths[] = $file->store('kamar', 'public');
            }
            $data['foto'] = $paths;
        }

        Kamar::create($data);

        return redirect()->route('pemilik.kamar.index')
            ->with('success', 'Kamar berhasil ditambahkan');
    }


    public function edit(Kamar $kamar)
    {
        // Proteksi supaya tidak edit kamar orang lain
        if ($kamar->kos->user_id != Auth::id()) {
            abort(403);
        }

        $kos = Kos::where('user_id', Auth::id())->get();

        return view('pemilik.kamar.edit', compact('kamar', 'kos'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        if ($kamar->kos->user_id != Auth::id()) abort(403);

        $request->validate([
            'kos_id'     => 'required|exists:kos,id',
            'nama_kamar' => 'required|string|max:255',
            'harga'      => 'required',
            'foto.*'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $harga = str_replace(['Rp', '.', ' '], '', $request->harga);
        if (strlen($harga) <= 3) {
            $harga = $harga . "000";
        }

        $data = $request->except('foto');
        $data['harga'] = $harga;

        if ($request->hasFile('foto')) {
            if ($kamar->foto) {
                foreach ($kamar->foto as $old) {
                    Storage::disk('public')->delete($old);
                }
            }

            $paths = [];
            foreach ($request->file('foto') as $file) {
                $paths[] = $file->store('kamar', 'public');
            }
            $data['foto'] = $paths;
        }

        $kamar->update($data);

        return redirect()->route('pemilik.kamar.index')
            ->with('success', 'Kamar berhasil diupdate');
    }

    public function show($id)
    {
        $kamar = Kamar::with('kos')
            ->whereHas('kos', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        return view('pemilik.kamar.show', compact('kamar'));
    }

    public function destroy(Kamar $kamar)
    {
        if ($kamar->kos->user_id != Auth::id()) abort(403);
 // CEK JIKA KAMAR TERISI
    if ($kamar->status == 'terisi') {
        return back()->with('error', 'Kamar sedang terisi dan tidak bisa dihapus!');
    }

        if ($kamar->foto) {
            foreach ($kamar->foto as $old) {
                Storage::disk('public')->delete($old);
            }
        }

        $kamar->delete();

        return back()->with('success', 'Kamar berhasil dihapus');
    }
}
