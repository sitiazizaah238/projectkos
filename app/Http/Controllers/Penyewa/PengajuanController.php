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
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10], true)) {
            $perPage = 10;
        }

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
            ->paginate($perPage)
            ->withQueryString();

        return view('penyewa.pengajuan.index', compact('pengajuan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'kos_id' => 'required',
            'kamar_id' => 'required',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
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
            'jenis_pengajuan' => 'sewa_baru',
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

        $statusSaatIni = $pengajuan->statusSaatIni();
        $kamarTersedia = optional($pengajuan->kamar)->status === 'tersedia';

        if (!in_array($statusSaatIni, ['aktif', 'jatuh_tempo', 'selesai'], true)) {
            return back()->with('error', 'Perpanjangan hanya tersedia untuk sewa aktif, jatuh tempo, atau sewa selesai yang kamarnya masih tersedia.');
        }

        if ($statusSaatIni === 'selesai' && ! $kamarTersedia) {
            return back()->with('error', 'Perpanjangan tidak bisa diproses karena kamar sudah tidak tersedia.');
        }

        if (in_array($statusSaatIni, ['aktif', 'jatuh_tempo'], true) && $pengajuan->sisaHariSewa() > 5) {
            return back()->with('error', 'Perpanjangan dapat diajukan saat masa sewa mendekati berakhir (H-5 atau H-3).');
        }

        $durasiTambahan = (int) $request->durasi_tambahan;
        $hargaKamar = (int) optional($pengajuan->kamar)->harga;

        $pengajuan->update([
            'durasi' => (int) $pengajuan->durasi + $durasiTambahan,
            'jenis_pengajuan' => 'perpanjang',
            'total_bayar' => (int) $pengajuan->total_bayar + ($hargaKamar * $durasiTambahan),
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('penyewa.pengajuan.index', ['focus_bayar' => $pengajuan->id])
            ->with('success', 'Perpanjangan sewa berhasil diajukan. Silakan klik Bayar Sekarang untuk melanjutkan pembayaran.');
    }

    public function ajukanUlang($id)
    {
        $pengajuanLama = PengajuanSewa::with('kamar')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($pengajuanLama->status !== 'ditolak') {
            return back()->with('error', 'Pengajuan ini tidak dapat diajukan ulang.');
        }

        $cekAktif = PengajuanSewa::where('user_id', Auth::id())
            ->where('kamar_id', $pengajuanLama->kamar_id)
            ->whereIn('status', ['menunggu', 'disetujui', 'aktif', 'jatuh_tempo'])
            ->exists();

        if ($cekAktif) {
            return back()->with('error', 'Masih ada pengajuan aktif/menunggu untuk kamar ini.');
        }

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $pengajuanLama->kos_id,
            'kamar_id' => $pengajuanLama->kamar_id,
            'tanggal_mulai' => now()->toDateString(),
            'durasi' => $pengajuanLama->durasi,
            'jenis_pengajuan' => $pengajuanLama->jenis_pengajuan ?: 'sewa_baru',
            'total_bayar' => (int) optional($pengajuanLama->kamar)->harga * (int) $pengajuanLama->durasi,
            'status' => 'menunggu',
            'alasan' => null,
            'is_read' => false,
            'status_notif' => 0,
        ]);

        return redirect()
            ->route('penyewa.pengajuan.index')
            ->with('success', 'Pengajuan berhasil diajukan ulang dan menunggu verifikasi pemilik.');
    }
}
