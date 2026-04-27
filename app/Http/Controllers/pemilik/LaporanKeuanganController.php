<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    private function formatPeriode(Request $request): array
    {
        $dari = $request->filled('dari') ? Carbon::parse($request->dari) : null;
        $sampai = $request->filled('sampai') ? Carbon::parse($request->sampai) : null;

        if ($dari && $sampai) {
            $label = $dari->translatedFormat('d M Y') . ' - ' . $sampai->translatedFormat('d M Y');
            $slug = $dari->format('Ymd') . '-' . $sampai->format('Ymd');
        } elseif ($dari) {
            $label = 'Sejak ' . $dari->translatedFormat('d M Y');
            $slug = $dari->format('Ymd') . '-sekarang';
        } elseif ($sampai) {
            $label = 'Sampai ' . $sampai->translatedFormat('d M Y');
            $slug = 'awal-' . $sampai->format('Ymd');
        } else {
            $label = 'Semua Periode';
            $slug = 'semua-periode';
        }

        return [
            'label' => $label,
            'slug' => $slug,
        ];
    }

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
            ->paginate(5)
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
        $periode = $this->formatPeriode($request);
        $tanggalCetak = now()->translatedFormat('d M Y H:i');
        $fileTanggal = now()->format('Ymd_His');

        $totalKeseluruhan = $laporan->sum(function ($item) {
            return $item->nominal_tagihan ?? 0;
        });

        $pdf = Pdf::loadView(
            'pemilik.laporan.pdf',
            compact('laporan', 'totalKeseluruhan', 'tanggalCetak', 'periode')
        )
            ->setPaper('a4', 'portrait');

        $filename = 'laporan-keuangan_' . $periode['slug'] . '_cetak-' . $fileTanggal . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel (ikut filter aktif)
     */
    public function excel(Request $request)
    {
        $laporan = $this->getQuery($request)->get();
        $periode = $this->formatPeriode($request);
        $tanggalCetak = now()->translatedFormat('d M Y H:i');
        $fileTanggal = now()->format('Ymd_His');

        $filename = 'laporan-keuangan_' . $periode['slug'] . '_cetak-' . $fileTanggal . '.xlsx';

        return Excel::download(
            new LaporanKeuanganExport($laporan, $periode['label'], $tanggalCetak),
            $filename
        );
    }
}
