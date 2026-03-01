<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanKeuanganExport implements FromArray, WithStyles, ShouldAutoSize, WithEvents
{
    protected $laporan;
    protected $lastRow;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function array(): array
    {
        $data = [];

        // Judul
        $data[] = ['Laporan Keuangan'];

        // HEADER (langsung baris ke 2, TANPA spasi kosong)
        $data[] = [
            'No',
            'Nama Penyewa',
            'Nama Kamar',
            'Tanggal Bayar',
            'Metode Pembayaran',
            'Total'
        ];

        // DATA
        foreach ($this->laporan as $i => $item) {
            $data[] = [
                $i + 1,
                optional($item->pengajuan->penyewa)->name ?? '-',
                $item->pengajuan->kamar->nama_kamar ?? '-',
                optional($item->created_at)->format('d/m/Y'),
                $item->metode->nama_metode ?? '-',
                $item->pengajuan->total_bayar ?? 0
            ];
        }

        // TOTAL
        $total = $this->laporan->sum(
            fn($item) =>
            $item->pengajuan->total_bayar ?? 0
        );

        $data[] = ['', '', '', '', 'Total', $total];

        $this->lastRow = count($data);

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;
                $lastRow = $this->lastRow;

                // Merge & Center Judul
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // 🔥 HEADER HIJAU (BARIS 2)
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '00B050'],
                    ],
                ]);

                // Format angka Total
                // Format angka kolom Total jadi Rp
                $sheet->getStyle("F3:F{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp " #,##0');
                // 🔥 BARIS TOTAL HIJAU MUDA
                $sheet->getStyle("A{$lastRow}:F{$lastRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '92D050'],
                        ],
                    ]);

                // Border semua tabel
                $sheet->getStyle("A2:F{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);
            },
        ];
    }
}
