<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanSewa;
use App\Models\Kamar;
use App\Services\RecommendationScoringService;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function __construct(private RecommendationScoringService $recommendationScoring) {}

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
            ->paginate(5)
            ->withQueryString();
        return view('penyewa.pengajuan.index', compact('pengajuan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'kos_id' => 'required',
            'kamar_id' => 'required',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'jenis_sewa' => 'required|in:bulanan,tahunan',
            'durasi' => 'required|integer|min:1'
        ]);

        $cek = PengajuanSewa::where('user_id', Auth::id())
            ->where('kamar_id', $request->kamar_id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if ($cek) {
            return back()->with('error', 'Anda sudah pernah mengajukan kamar ini sebelumnya.');
        }

        $kamar = Kamar::findOrFail($request->kamar_id);
        $durasi = (int) $request->durasi;

        // Hitung harga dasar per bulan
        if ($kamar->tipe_harga === 'tahunan') {
            // Jika tahunan, hitung harga per bulannya
            $hargaPerBulan = (int) $kamar->harga / 12;
            $totalBayar = (int) ($hargaPerBulan * $durasi);
        } else {
            // Jika bulanan, langsung dikalikan durasi
            $totalBayar = (int) $kamar->harga * $durasi;
        }

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $request->kos_id,
            'kamar_id' => $request->kamar_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'durasi' => $durasi,
            'jenis_pengajuan' => 'sewa_baru',
            'total_bayar' => $totalBayar,
            'status' => 'menunggu'
        ]);

        if ($kamar->relationLoaded('kos') === false) {
            $kamar->load('kos');
        }

        if ($kamar->kos) {
            $this->recommendationScoring->learnFromConfirmedAction(Auth::user(), $kamar->kos, $kamar);
        }

        return redirect()->route('penyewa.pengajuan.index')
            ->with('success', 'Pengajuan sewa berhasil dikirim. Silakan tunggu proses verifikasi pemilik.');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'durasi_tambahan' => 'required|integer|min:1|max:36',
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
        $tipeHargaKamar = optional($pengajuan->kamar)->tipe_harga;

        // Hitung biaya tambahan secara presisi
        if ($tipeHargaKamar === 'tahunan') {
            $biayaTambahan = (int) (($hargaKamar / 12) * $durasiTambahan);
        } else {
            $biayaTambahan = (int) ($hargaKamar * $durasiTambahan);
        }

        $pengajuan->update([
            'durasi' => (int) $pengajuan->durasi + $durasiTambahan,
            'jenis_pengajuan' => 'perpanjang',
            'total_bayar' => (int) $pengajuan->total_bayar + $biayaTambahan,
            'status' => 'disetujui',
        ]);

        $pengajuan->loadMissing(['kos', 'kamar']);

        if ($pengajuan->kos && $pengajuan->kamar) {
            $this->recommendationScoring->learnFromConfirmedAction(Auth::user(), $pengajuan->kos, $pengajuan->kamar);
        }

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

        $kamar = $pengajuanLama->kamar;
        $hargaKamar = (int) optional($kamar)->harga;
        $tipeHargaKamar = optional($kamar)->tipe_harga;
        $durasi = (int) $pengajuanLama->durasi;

        if ($tipeHargaKamar === 'tahunan') {
            $totalBayar = (int) (($hargaKamar / 12) * $durasi);
        } else {
            $totalBayar = (int) ($hargaKamar * $durasi);
        }

        PengajuanSewa::create([
            'user_id' => Auth::id(),
            'kos_id' => $pengajuanLama->kos_id,
            'kamar_id' => $pengajuanLama->kamar_id,
            'tanggal_mulai' => now()->toDateString(),
            'durasi' => $durasi,
            'jenis_pengajuan' => $pengajuanLama->jenis_pengajuan ?: 'sewa_baru',
            'total_bayar' => $totalBayar,
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
