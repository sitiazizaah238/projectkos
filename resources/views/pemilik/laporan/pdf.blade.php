<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; }
        table { width:100%; border-collapse: collapse; }
        th, td {
            border:1px solid #000;
            padding:8px;
            text-align:center;
        }
        th { background:#eee; }
    </style>
</head>
<body>

<h2 style="text-align:center;">LAPORAN KEUANGAN</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Penyewa</th>
            <th>Nama Kamar</th>
            <th>Tanggal</th>
            <th>Metode</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($laporan as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ optional($item->pengajuan->penyewa)->name ?? '-' }}</td>
            <td>{{ $item->pengajuan->kamar->nama_kamar }}</td>
            <td>{{ $item->created_at->format('d M Y') }}</td>
            <td>{{ $item->metode->nama_metode }}</td>
            <td>
                Rp {{ number_format($item->pengajuan->total_bayar,0,',','.') }}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="5"><b>Total</b></td>
            <td>
                Rp {{ number_format($laporan->sum(fn($i) => $i->pengajuan->total_bayar),0,',','.') }}
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>
