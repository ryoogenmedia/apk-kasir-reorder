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
        <h3 style="margin: 0; font-size: 14px; text-transform: uppercase;">Laporan Pembelian Supplier</h3>
        <p style="margin: 3px 0 0 0; font-size: 11px;">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

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
