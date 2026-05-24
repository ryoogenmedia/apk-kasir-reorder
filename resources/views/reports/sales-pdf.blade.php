<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    @php
        $shopName = \App\Models\Setting::where('key', 'shop_name')->first()?->value ?? 'Warung Campuran';
        $shopAddress = \App\Models\Setting::where('key', 'shop_address')->first()?->value ?? 'Jl. Merdeka No. 123';
        $shopPhone = \App\Models\Setting::where('key', 'shop_phone')->first()?->value ?? '081234567890';
    @endphp
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <h2 style="margin: 0; font-size: 18px; text-transform: uppercase;">{{ $shopName }}</h2>
        <p style="margin: 5px 0 0 0; font-size: 11px;">{{ $shopAddress }} | Telp: {{ $shopPhone }}</p>
    </div>

    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; font-size: 14px; text-transform: uppercase;">LAPORAN PENJUALAN</h3>
        <p style="margin: 3px 0 0 0; font-size: 11px;">Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th style="text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                    <td>{{ $order->user?->name ?? '-' }}</td>
                    <td>{{ ucfirst($order->payment_method) }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td style="text-align: right;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">GRAND TOTAL</td>
                <td style="text-align: right;">Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
