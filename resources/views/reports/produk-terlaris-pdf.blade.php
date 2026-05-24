<!DOCTYPE html>
<html>
<head>
    <title>Laporan Produk Terlaris</title>
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
        <h3 style="margin: 0; font-size: 14px; text-transform: uppercase;">Laporan Produk Terlaris</h3>
        <p style="margin: 3px 0 0 0; font-size: 11px;">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Peringkat</th>
                <th>Kategori</th>
                <th>Nama Produk</th>
                <th class="text-center">Terjual (Qty)</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @php $sumQty = 0; $sumRev = 0; @endphp
            @forelse($records as $index => $record)
                @php 
                    $sumQty += $record->total_qty; 
                    $sumRev += $record->total_revenue;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $record->product->category->name ?? '-' }}</td>
                    <td>{{ $record->product->name ?? '-' }}</td>
                    <td class="text-center">{{ $record->total_qty }}</td>
                    <td class="text-right">Rp {{ number_format($record->total_revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-center">{{ $sumQty }}</th>
                <th class="text-right">Rp {{ number_format($sumRev, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
