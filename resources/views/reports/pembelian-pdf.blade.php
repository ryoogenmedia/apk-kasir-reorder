<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian Supplier</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Laporan Pembelian Supplier</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @php $sumTotal = 0; @endphp
            @forelse($records as $index => $record)
                @php $sumTotal += $record->total_amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->purchase_date)->format('d/m/Y') }}</td>
                    <td>{{ $record->supplier->name ?? '-' }}</td>
                    <td>{{ ucfirst($record->status) }}</td>
                    <td class="text-right">Rp {{ number_format($record->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($sumTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
