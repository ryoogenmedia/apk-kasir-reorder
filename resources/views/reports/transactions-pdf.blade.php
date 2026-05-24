<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 25px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #777; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .summary-box {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            background-color: #fafafa;
            width: 300px;
            float: right;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .summary-row-bold {
            font-weight: bold;
            border-top: 1px dashed #ddd;
            padding-top: 4px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 18px;">LAPORAN TRANSAKSI KEUANGAN</h1>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Tipe</th>
                <th style="width: 18%;">Kasir / PJ</th>
                <th>Keterangan / Detail</th>
                <th style="width: 20%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $tx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $tx->date->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($tx->type === 'pemasukan')
                            <span class="badge badge-success">Pemasukan</span>
                        @else
                            <span class="badge badge-danger">Pengeluaran</span>
                        @endif
                    </td>
                    <td>{{ $tx->actor_name ?? '-' }}</td>
                    <td>
                        @if($tx->type === 'pemasukan')
                            Penjualan ({{ $tx->detail === 'cash' ? 'Tunai' : 'QRIS' }})
                        @else
                            Pembelian ({{ $tx->detail }})
                        @endif
                    </td>
                    <td class="text-right" style="color: {{ $tx->type === 'pemasukan' ? '#047857' : '#b91c1c' }};">
                        {{ $tx->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 20px;">
        <table style="width: 300px; float: right; margin-top: 0; border: 1px solid #ddd;">
            <tr class="total-row">
                <td style="padding: 6px 8px; border: 1px solid #ddd;">Total Pemasukan:</td>
                <td class="text-right" style="color: #047857; padding: 6px 8px; border: 1px solid #ddd;">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td style="padding: 6px 8px; border: 1px solid #ddd;">Total Pengeluaran:</td>
                <td class="text-right" style="color: #b91c1c; padding: 6px 8px; border: 1px solid #ddd;">Rp {{ number_format($stats['total_purchases'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background-color: #e0f2fe;">
                <td style="padding: 6px 8px; border: 1px solid #ddd;">Balance (Net):</td>
                <td class="text-right" style="color: #0369a1; padding: 6px 8px; border: 1px solid #ddd;">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
