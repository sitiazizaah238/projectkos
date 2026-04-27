<!DOCTYPE html>
<html>

<head>
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead {
            background: #000;
            color: #fff;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        tbody tr:hover {
            background: #eaeaea;
        }

        tfoot td {
            font-weight: bold;
            background: #ddd;
        }

        .text-left {
            text-align: left;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>LAPORAN KEUANGAN</h2>

    <div style="margin-bottom: 12px; font-size: 13px;">
        <div><strong>Tanggal Cetak:</strong> {{ $tanggalCetak ?? '-' }}</div>
        <div><strong>Periode:</strong> {{ $periode['label'] ?? 'Semua Periode' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Penyewa</th>
                <th>Nama Kamar</th>
                <th>Tanggal</th>
                <th>Durasi Sewa</th>
                <th>Metode Bayar</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ optional($item->pengajuan->penyewa)->name ?? '-' }}</td>
                    <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
                    <td>{{ $item->created_at->format('d M Y') }}</td>

                    {{-- DURASI SEWA --}}
                    <td>
                        {{ \App\Models\PengajuanSewa::formatDurasiByTipe((int) ($item->durasi_tagihan ?? 1), optional($item->pengajuan->kamar)->tipe_harga) }}
                    </td>

                    <td>{{ $item->metode->nama_metode }}</td>
                    <td>
                        Rp {{ number_format($item->nominal_tagihan ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6" class="text-left total">Total Keseluruhan</td>
                <td class="total">
                    Rp {{ number_format($laporan->sum(fn($i) => $i->nominal_tagihan ?? 0), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
