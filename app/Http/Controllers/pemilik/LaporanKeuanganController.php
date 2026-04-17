<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;

class LaporanKeuanganController extends Controller
{
    /**
     * Query dasar + filter
     */
    private function getQuery(Request $request)
    {
        return Pembayaran::with([
            'pengajuan.penyewa',
            'pengajuan.kamar',
            'pengajuan.kos',
            'metode'
        ])
            ->where('status', 'dikonfirmasi')
            ->whereHas('pengajuan.kos', function ($query) {
                $query->where('user_id', Auth::id());
            })

            // 🔎 SEARCH
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('pengajuan', function ($q) use ($request) {
                    $q->whereHas('penyewa', function ($p) use ($request) {
                        $p->where('name', 'like', '%' . $request->search . '%');
                    })
                        ->orWhereHas('kamar', function ($k) use ($request) {
                            $k->where('nama_kamar', 'like', '%' . $request->search . '%');
                        });
                });
            })

            // 📅 FILTER DURASI
            ->when($request->durasi, function ($query) use ($request) {
                $query->where('durasi_tagihan', $request->durasi);
            })

            // 📆 FILTER TANGGAL
            ->when($request->dari, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->dari);
            })
            ->when($request->sampai, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->sampai);
            })

            ->latest();
    }

    /**
     * Halaman utama laporan (PAGINATION)
     */
    public function index(Request $request)
    {
        $laporan = $this->getQuery($request)
            ->paginate(10)
            ->withQueryString();

        $totalKeseluruhan = $laporan->sum(function ($item) {
            return $item->nominal_tagihan ?? 0;
        });

        return view(
            'pemilik.laporan.index',
            compact('laporan', 'totalKeseluruhan')
        );
    }

    /**
     * Export PDF (ikut filter aktif)
     */
    public function print(Request $request)
    {
        $laporan = $this->getQuery($request)->get();

        $totalKeseluruhan = $laporan->sum(function ($item) {
            return $item->nominal_tagihan ?? 0;
        });

        $pdf = Pdf::loadView(
            'pemilik.laporan.pdf',
            compact('laporan', 'totalKeseluruhan')
        )
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan.pdf');
    }

    /**
     * Export Excel (ikut filter aktif)
     */
    public function excel(Request $request)
    {
        $laporan = $this->getQuery($request)->get();

        return Excel::download(
            new LaporanKeuanganExport($laporan),
            'laporan-keuangan.xlsx'
        );
    }
}
