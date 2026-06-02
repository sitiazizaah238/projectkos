<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kos;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class PemilikKosController extends Controller
{
    // ================= LIST DATA =================
    public function index(Request $request)
    {
        $query = Kos::where('user_id', Auth::id());

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kos', 'like', '%' . $request->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $request->search . '%')
                    ->orWhere('tipe_kos', 'like', '%' . $request->search . '%');
            });
        }

        $kos = $query->latest()->get();

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
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'tipe_kos' => 'required',
            'foto.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $fotoPaths = [];

        if ($request->hasFile('foto')) {

            if (count($request->file('foto')) > 3) {
                return back()->withErrors(['foto' => 'Maksimal 3 foto']);
            }

            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('kos', 'public');
            }
        }

        $kos = Kos::create([
            'user_id' => auth()->id(),
            'nama_kos' => $request->nama_kos,
            'lokasi' => $request->lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tipe_kos' => $request->tipe_kos,
            'deskripsi' => $request->deskripsi,
            'fasilitas' => $request->fasilitas,
            'foto' => $fotoPaths,
            'status' => 'menunggu'
        ]);

        // ✅ SIMPAN LOG TAMBAH
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'kos_id' => $kos->id,
            'aktivitas' => 'Menambahkan Kos',
            'keterangan' => $kos->nama_kos
        ]);

        return redirect()->route('pemilik.kos.index')
            ->with('success', 'Kos berhasil diajukan');
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
        $kos = Kos::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'nama_kos' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'tipe_kos' => 'required|string|max:255',
            'foto.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->latitude && $request->longitude) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;
            if ($lat < -6.45 || $lat > -6.30 || $lng < 108.15 || $lng > 108.35) {
                return back()->withErrors(['lokasi' => 'Titik peta lokasi harus berada di wilayah Kecamatan Lohbener.'])->withInput();
            }
        }

        $data = $request->only([
            'nama_kos',
            'lokasi',
            'latitude',
            'longitude',
            'tipe_kos',
            'deskripsi',
        ]);

        $data['fasilitas'] = $request->fasilitas ?? [];

        $existingPhotos = $kos->foto ?? [];

        if ($request->deleted_photos) {
            $deleted = array_filter(explode(',', $request->deleted_photos));

            foreach ($deleted as $photo) {
                Storage::disk('public')->delete($photo);
            }

            $existingPhotos = array_values(array_diff($existingPhotos, $deleted));
        }

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                if (count($existingPhotos) >= 3) {
                    break;
                }

                $existingPhotos[] = $file->store('kos', 'public');
            }
        }

        $data['foto'] = $existingPhotos;

        if ($kos->status === 'disetujui') {
            if ($kos->punyaPengajuanEditAktif()) {
                return redirect()
                    ->route('pemilik.kos.index')
                    ->with('error', 'Masih ada pengajuan perubahan data yang sedang menunggu persetujuan admin.');
            }

            $kos->update([
                'edit_request_status' => 'menunggu',
                'edit_request_data' => $data,
                'edit_request_alasan' => null,
                'edit_requested_at' => now(),
                'is_read' => false,
            ]);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'kos_id' => $kos->id,
                'aktivitas' => 'Ajukan Perubahan Data Kos',
                'keterangan' => $kos->nama_kos,
            ]);

            return redirect()
                ->route('pemilik.kos.index')
                ->with('success', 'Pengajuan perubahan data berhasil dikirim ke admin untuk diverifikasi.');
        }

        if ($kos->status === 'ditolak') {
            $data['status'] = 'menunggu';
            $data['alasan'] = null;
            $data['tanggal_verifikasi'] = null;
            $data['is_read'] = false;
        }

        $kos->update($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'kos_id' => $kos->id,
            'aktivitas' => 'Update Data Kos',
            'keterangan' => $kos->nama_kos,
        ]);

        return redirect()
            ->route('pemilik.kos.index')
            ->with('success', 'Data kos berhasil diperbarui.');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        Kos::destroy($id);

        return back()->with('success', 'Data berhasil dihapus');
    }
}
