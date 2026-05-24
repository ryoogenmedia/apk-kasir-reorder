<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekap Shift Harian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Rekap Shift Harian</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th class="text-center">Total Transaksi</th>
                <th>Tunai Masuk (Laci)</th>
                <th>QRIS Masuk</th>
                <th>Pengeluaran (Belanja)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumTransaksi = 0; 
                $sumTunai = 0; 
                $sumQris = 0; 
                $sumPengeluaran = 0; 
            @endphp
            @forelse($records as $index => $record)
                @php 
                    $sumTransaksi += $record->total_transaksi; 
                    $sumTunai += $record->tunai_masuk;
                    $sumQris += $record->qris_masuk;
                    $sumPengeluaran += $record->pengeluaran;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $record->total_transaksi }}</td>
                    <td class="text-right">Rp {{ number_format($record->tunai_masuk, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($record->qris_masuk, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($record->pengeluaran, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th class="text-center">{{ $sumTransaksi }}</th>
                <th class="text-right">Rp {{ number_format($sumTunai, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sumQris, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sumPengeluaran, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
