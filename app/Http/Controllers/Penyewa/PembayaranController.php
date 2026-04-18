<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10], true)) {
            $perPage = 10;
        }

        $pembayaran = Pembayaran::whereHas('pengajuan', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('pengajuan.kos', 'pengajuan.kamar', 'pengajuan.pembayarans')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('penyewa.pembayaran.index', compact('pembayaran'));
    }
    public function ajukanUlang(Request $request, $id)
    {
        $request->validate([
            'bukti' => 'required|image|max:2048'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        $path = $request->file('bukti')->store('bukti', 'public');

        $pembayaran->update([
            'bukti' => $path,
            'status' => 'menunggu',
            'alasan' => null
        ]);

        return back()->with('success', 'Pengajuan ulang pembayaran berhasil dikirim.');
    }
    public function store(Request $request, $id)
    {
        $request->validate([
            'metode_id' => 'required',
            'bukti' => 'required|image|max:2048'
        ]);

        $pengajuan = PengajuanSewa::with('pembayarans')->findOrFail($id);

        $statusSaatIni = $pengajuan->statusSaatIni();

        if (!in_array($statusSaatIni, ['disetujui', 'jatuh_tempo'], true)) {
            return back()->with('error', 'Tagihan belum tersedia untuk diproses pembayaran.');
        }

        if ($pengajuan->adaPembayaranMenunggu()) {
            return back()->with('error', 'Masih ada pembayaran yang sedang menunggu verifikasi pemilik.');
        }

        $durasiTagihan = $pengajuan->durasiBelumTerbayar();
        if ($durasiTagihan <= 0) {
            return redirect()
                ->route('penyewa.pengajuan.index')
                ->with('error', 'Tidak ada tagihan yang perlu dibayar saat ini.');
        }

        $nominalTagihan = (int) optional($pengajuan->kamar)->harga * $durasiTagihan;

        $buktiPath = $request->file('bukti')
            ->store('bukti', 'public');

        // INSERT KE TABEL PEMBAYARANS
        Pembayaran::create([
            'pengajuan_sewa_id' => $pengajuan->id,
            'metode_id' => $request->metode_id,
            'bukti' => $buktiPath,
            'durasi_tagihan' => $durasiTagihan,
            'nominal_tagihan' => $nominalTagihan,
            'status' => 'menunggu',
            'is_read' => false,
        ]);

        return redirect()
            ->route('penyewa.pengajuan.index')
            ->with('success', 'Pembayaran berhasil dikirim dan menunggu verifikasi pemilik.');
    }
}
