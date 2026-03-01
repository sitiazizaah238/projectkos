<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class LaporanKeuanganExport implements FromArray
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function array(): array
    {
        $data = [];

        // 🔥 Judul
        $data[] = ['LAPORAN KEUANGAN'];
        $data[] = []; // spasi

        // 🔥 Header (sama seperti PDF)
        $data[] = [
            'No',
            'Nama Penyewa',
            'Nama Kamar',
            'Tanggal',
            'Metode',
            'Total'
        ];

        // 🔥 Isi data
        foreach ($this->laporan as $i => $item) {
            $data[] = [
                $i + 1,
                optional($item->pengajuan->penyewa)->name ?? '-',
                $item->pengajuan->kamar->nama_kamar,
                $item->created_at->format('d M Y'),
                $item->metode->nama_metode,
                'Rp ' . number_format($item->pengajuan->total_bayar, 0, ',', '.')
            ];
        }

        // 🔥 Total keseluruhan (sama seperti PDF)
        $total = $this->laporan->sum(function ($item) {
            return $item->pengajuan->total_bayar ?? 0;
        });

        $data[] = []; // spasi
        $data[] = [
            '',
            '',
            '',
            '',
            'Total',
            'Rp ' . number_format($total, 0, ',', '.')
        ];

        return $data;
    }
}
