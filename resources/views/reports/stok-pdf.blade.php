<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok & Reorder</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .danger { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Laporan Stok & Reorder</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Produk</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Min. Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                @php
                    $status = 'Aman';
                    $class = 'success';
                    if ($record->stock == 0) { $status = 'Habis'; $class = 'danger'; }
                    elseif ($record->stock <= $record->low_stock_threshold) { $status = 'Perlu Reorder'; $class = 'warning'; }
                    elseif ($record->max_stock_threshold && $record->stock >= $record->max_stock_threshold) { $status = 'Overstock'; $class = ''; }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->category->name ?? '-' }}</td>
                    <td>{{ $record->name }}</td>
                    <td class="text-center {{ $class }}">{{ $record->stock }}</td>
                    <td class="text-center">{{ $record->low_stock_threshold }}</td>
                    <td class="{{ $class }}">{{ $status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data produk</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
