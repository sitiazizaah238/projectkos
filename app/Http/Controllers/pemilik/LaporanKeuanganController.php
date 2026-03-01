<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;


class LaporanKeuanganController extends Controller
{
    /**
     * Ambil data laporan berdasarkan pemilik login
     */
    private function getData()
    {
        return Pembayaran::with([
                'pengajuan.penyewa',
                'pengajuan.kamar',
                'pengajuan.kos',
                'metode'
            ])
            ->where('status', 'dikonfirmasi') // hanya pembayaran yang sudah dikonfirmasi
            ->whereHas('pengajuan.kos', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->get();
    }

    /**
     * Halaman utama laporan
     */
    public function index()
    {
        $laporan = $this->getData();

        $totalKeseluruhan = $laporan->sum(function ($item) {
            return $item->pengajuan->total_bayar ?? 0;
        });

        return view('pemilik.laporan.index', compact('laporan', 'totalKeseluruhan'));
    }

    /**
     * Export PDF
     */
    public function print()
    {
        $laporan = $this->getData();

        $totalKeseluruhan = $laporan->sum(function ($item) {
            return $item->pengajuan->total_bayar ?? 0;
        });

        $pdf = Pdf::loadView('pemilik.laporan.pdf', compact('laporan', 'totalKeseluruhan'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan.pdf');
    }

    /**
     * Export Excel
     */
    public function excel()
{
    $laporan = $this->getData();

    return Excel::download(
        new LaporanKeuanganExport($laporan),
        'laporan-keuangan.xlsx'
    );
}
}
