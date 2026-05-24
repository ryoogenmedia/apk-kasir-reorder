<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ImportProductData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-product-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import produk dan kategori dari excel dan copy gambar ke storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses import data...');

        require_once app_path('Services/SimpleXLSX.php');

        $excelPath = public_path('template/example/data_produk_dan_category.xlsx');
        if (!file_exists($excelPath)) {
            $this->error("File excel tidak ditemukan di: {$excelPath}");
            return;
        }

        $xlsx = \Shuchkin\SimpleXLSX::parse($excelPath);
        if (!$xlsx) {
            $this->error("Gagal membaca excel: " . \Shuchkin\SimpleXLSX::parseError());
            return;
        }

        // Sheet 1 is 'Data Produk'
        $rows = $xlsx->rows(1);
        if (empty($rows)) {
            $this->error("Sheet Data Produk kosong atau tidak ditemukan.");
            return;
        }

        // Pastikan folder untuk gambar produk di public storage sudah ada
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        $count = 0;
        // Data dimulai dari baris ke 5 (index 4) atau ke 6 (index 5)
        // Header di index 4
        foreach (array_slice($rows, 5) as $row) {
            $kategoriName = trim($row[2] ?? '');
            $produkName = trim($row[3] ?? '');
            $imageName = trim($row[4] ?? '');

            if (empty($produkName)) {
                continue;
            }

            // 1. Buat / Ambil Kategori
            $category = Category::firstOrCreate([
                'name' => $kategoriName ?: 'Uncategorized'
            ]);

            // 2. Copy image jika ada
            $dbImagePath = null;
            if (!empty($imageName)) {
                $sourcePath = public_path('gambar-produk/' . $imageName);
                if (file_exists($sourcePath)) {
                    // Beri nama unik agar tidak bentrok
                    $newImageName = time() . '_' . Str::slug($produkName) . '_' . $imageName;
                    Storage::disk('public')->put('products/' . $newImageName, file_get_contents($sourcePath));
                    $dbImagePath = 'products/' . $newImageName;
                } else {
                    $this->warn("Gambar tidak ditemukan: {$sourcePath}");
                }
            }

            // 3. Buat Produk
            Product::create([
                'category_id' => $category->id,
                'name' => $produkName,
                'price' => rand(10, 50) * 1000, // Harga random antara 10.000 - 50.000
                'stock' => rand(20, 100), // Stok random antara 20 - 100
                'image' => $dbImagePath,
            ]);

            $count++;
        }

        $this->info("Import selesai! Berhasil menambahkan {$count} produk.");
    }
}
