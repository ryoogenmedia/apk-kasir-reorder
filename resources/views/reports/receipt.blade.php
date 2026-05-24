<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 300px;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header {
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-table, .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            font-size: 11px;
        }
        .item-table th {
            border-bottom: 1px dashed #000;
            text-align: left;
            padding: 4px 0;
            font-size: 11px;
        }
        .item-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .totals {
            margin-top: 10px;
            width: 100%;
        }
        .totals td {
            padding: 2px 0;
            font-size: 11px;
        }
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }
        @media print {
            body {
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold; background: #3b82f6; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cetak Struk</button>
        <button onclick="window.close()" style="padding: 6px 12px; font-weight: bold; background: #9ca3af; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">Tutup</button>
    </div>

    <div class="header text-center">
        <h2>TOKO KASIR</h2>
        <p>Jl. Contoh No. 123, Indonesia</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td>No. Struk: #{{ $order->id }}</td>
            <td class="text-right">Kasir: {{ $order->user?->name ?? 'Kasir' }}</td>
        </tr>
        <tr>
            <td>Tanggal: {{ $order->order_date->format('d/m/Y') }}</td>
            <td class="text-right">Metode: {{ $order->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="item-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product?->name ?? 'Produk Terhapus' }}<br>
                        <small>@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</small>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">{{ $item->quantity }}</td>
                    <td class="text-right" style="vertical-align: middle;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td>TOTAL TAGIHAN:</td>
            <td class="text-right font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>BAYAR:</td>
            <td class="text-right">Rp {{ number_format($order->amount_paid ?? $order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="font-bold">
            <td>KEMBALIAN:</td>
            <td class="text-right" style="color: #000;">Rp {{ number_format($order->change_amount ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        <p class="font-bold">Terima Kasih</p>
        <p>Atas Kunjungan Anda</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
