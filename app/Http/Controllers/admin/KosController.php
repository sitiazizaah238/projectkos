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

        $kos = Kos::with(['user', 'kamars'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama_kos', 'like', "%{$search}%")
                        ->orWhere('lokasi', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(5);

        return view('admin.kos.index', compact('kos'));
    }

    public function show($id)
    {
        $kos = Kos::with(['user', 'kamars'])->findOrFail($id);

        return view('admin.kos.show', compact('kos'));
    }

    // SETUJUI
    public function approve($id)
    {
        $kos = Kos::findOrFail($id);

        $kos->update([
            'status' => 'disetujui',
            'alasan' => null,
            'tanggal_verifikasi' => now(),
            'is_read' => false,
        ]);

        return back()->with('success', 'Verifikasi kos berhasil disetujui.');
    }

    // TOLAK
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        $kos = Kos::findOrFail($id);

        $kos->update([
            'status' => 'ditolak',
            'alasan' => $request->alasan,
            'tanggal_verifikasi' => now(),
            'is_read' => false,
        ]);

        return back()->with('success', 'Verifikasi kos berhasil ditolak.');
    }

    public function deactivate(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        $kos = Kos::findOrFail($id);

        $kos->update([
            'status' => 'nonaktif',
            'alasan' => $request->alasan,
            'is_read' => false,
        ]);

        return back()->with('success', 'Kos berhasil dinonaktifkan. Notifikasi telah dikirim ke pemilik.');
    }

    public function activate($id)
    {
        $kos = Kos::findOrFail($id);

        $kos->update([
            'status' => 'disetujui',
            'alasan' => null,
            'tanggal_verifikasi' => now(),
            'is_read' => false,
        ]);

        return back()->with('success', 'Kos berhasil diaktifkan kembali.');
    }

    public function approveEditRequest($id)
    {
        $kos = Kos::findOrFail($id);

        if ($kos->edit_request_status !== 'menunggu' || empty($kos->edit_request_data)) {
            return back()->with('error', 'Tidak ada pengajuan perubahan data yang sedang menunggu persetujuan.');
        }

        $payload = $kos->edit_request_data;

        $kos->update([
            'nama_kos' => $payload['nama_kos'] ?? $kos->nama_kos,
            'lokasi' => $payload['lokasi'] ?? $kos->lokasi,
            'latitude' => $payload['latitude'] ?? $kos->latitude,
            'longitude' => $payload['longitude'] ?? $kos->longitude,
            'tipe_kos' => $payload['tipe_kos'] ?? $kos->tipe_kos,
            'deskripsi' => $payload['deskripsi'] ?? $kos->deskripsi,
            'fasilitas' => $payload['fasilitas'] ?? $kos->fasilitas,
            'foto' => $payload['foto'] ?? $kos->foto,
            'status' => 'disetujui',
            'tanggal_verifikasi' => now(),
            'edit_request_status' => 'disetujui',
            'edit_request_alasan' => null,
            'alasan' => null,
            'is_read' => false,
        ]);

        return back()->with('success', 'Pengajuan perubahan data kos berhasil disetujui.');
    }

    public function rejectEditRequest(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        $kos = Kos::findOrFail($id);

        if ($kos->edit_request_status !== 'menunggu') {
            return back()->with('error', 'Tidak ada pengajuan perubahan data yang sedang menunggu persetujuan.');
        }

        $kos->update([
            'edit_request_status' => 'ditolak',
            'edit_request_alasan' => $request->alasan,
            'is_read' => false,
        ]);

        return back()->with('success', 'Pengajuan perubahan data kos berhasil ditolak.');
    }
}
