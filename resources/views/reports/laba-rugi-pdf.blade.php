<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
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
    <h2>Laporan Laba Rugi</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Total Penjualan</th>
                <th>Total Modal (HPP)</th>
                <th>Keuntungan Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumPenjualan = 0; 
                $sumModal = 0; 
                $sumUntung = 0; 
            @endphp
            @forelse($records as $index => $record)
                @php
                    $untung = $record->total_amount - $record->total_cost;
                    $sumPenjualan += $record->total_amount;
                    $sumModal += $record->total_cost;
                    $sumUntung += $untung;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->order_date)->format('d/m/Y') }}</td>
                    <td>{{ $record->user->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($record->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($untung, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($sumPenjualan, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sumModal, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sumUntung, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
