<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanSewa;
use App\Models\Kamar;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        // ================= QUERY DASAR =================
        $query = PengajuanSewa::with([
            'kos.user.metodePembayaran',
            'kamar',
            'pembayarans'
        ])->where('user_id', Auth::id());

        // ================= SEARCH =================
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('kos', function ($k) use ($search) {
                    $k->where('nama_kos', 'like', "%$search%");
                })
                    ->orWhereHas('kamar', function ($k) use ($search) {
                        $k->where('nama_kamar', 'like', "%$search%");
                    })
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        // ================= PAGINATION =================
        $pengajuan = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penyewa.pengajuan.index', compact('pengajuan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'kos_id' => 'required',
            'kamar_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'durasi' => 'required|integer'
        ]);

        $cek = PengajuanSewa::where('user_id', Auth::id())
            ->where('kamar_id', $request->kamar_id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if ($cek) {
            return back()->with('error', 'Anda sudah pernah mengajukan kamar ini sebelumnya.');
        }

        $kamar = Kamar::findOrFail($request->kamar_id);

        $totalBayar = $kamar->harga * $request->durasi;

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $request->kos_id,
            'kamar_id' => $request->kamar_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'durasi' => $request->durasi,
            'total_bayar' => $totalBayar,
            'status' => 'menunggu'
        ]);

        return redirect()->route('penyewa.pengajuan.index')
            ->with('success', 'Pengajuan sewa berhasil dikirim. Silakan tunggu proses verifikasi pemilik.');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'durasi_tambahan' => 'required|integer|min:1|max:12',
        ]);

        $pengajuan = PengajuanSewa::with('kamar')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if (!in_array($pengajuan->statusSaatIni(), ['aktif', 'jatuh_tempo'], true)) {
            return back()->with('error', 'Perpanjangan hanya tersedia untuk data sewa aktif atau yang sudah jatuh tempo.');
        }

        if ($pengajuan->sisaHariSewa() > 5) {
            return back()->with('error', 'Perpanjangan dapat diajukan saat masa sewa mendekati berakhir (H-5 atau H-3).');
        }

        $durasiTambahan = (int) $request->durasi_tambahan;
        $hargaKamar = (int) optional($pengajuan->kamar)->harga;

        $pengajuan->update([
            'durasi' => (int) $pengajuan->durasi + $durasiTambahan,
            'total_bayar' => (int) $pengajuan->total_bayar + ($hargaKamar * $durasiTambahan),
            'status' => 'jatuh_tempo',
        ]);

        return redirect()
            ->route('penyewa.pengajuan.index', ['focus_bayar' => $pengajuan->id])
            ->with('success', 'Perpanjangan sewa berhasil diajukan. Silakan lanjutkan ke pembayaran.');
    }
}
