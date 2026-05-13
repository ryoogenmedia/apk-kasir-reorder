<x-filament-panels::page>
    <x-filament::card>
        <div class="space-y-2">
            <h2 class="text-lg font-bold">Informasi Perhitungan ROP (Reorder Point)</h2>
            <p>Grafik di atas membandingkan stok saat ini dengan batas minimum (saat ini) dan ROP Saran (Kalkulasi). ROP Saran dihitung secara dinamis berdasarkan penjualan 30 hari terakhir.</p>
            <ul class="list-disc ml-5 text-sm">
                <li><strong>Penjualan Rata-rata Harian:</strong> Total produk terjual 30 hari terakhir dibagi 30.</li>
                <li><strong>Lead Time (Waktu Tunggu):</strong> Diasumsikan 3 hari.</li>
                <li><strong>Safety Stock (Stok Aman):</strong> Diasumsikan 5 unit.</li>
                <li><strong>Rumus ROP:</strong> (Penjualan Rata-rata Harian &times; Lead Time) + Safety Stock</li>
            </ul>
        </div>
    </x-filament::card>
</x-filament-panels::page>
