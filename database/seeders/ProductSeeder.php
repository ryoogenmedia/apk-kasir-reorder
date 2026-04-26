<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang-barang elektronik'],
            ['name' => 'Alat Tulis', 'description' => 'Kebutuhan kantor dan sekolah'],
            ['name' => 'Perlengkapan Rumah', 'description' => 'Kebutuhan rumah tangga'],
            ['name' => 'Aksesoris', 'description' => 'Aksesoris gadget dan fashion'],
            ['name' => 'Alat Kebersihan', 'description' => 'Peralatan untuk membersihkan rumah'],
        ];

        foreach ($categories as $catData) {
            $category = Category::create($catData);

            // Generate products for each category
            $products = [];
            
            if ($catData['name'] === 'Elektronik') {
                $products = ['Mouse Wireless', 'Keyboard Mechanical', 'Headset Gaming', 'USB Flashdisk 32GB', 'Powerbank 10000mAh', 'Kabel Data Type-C', 'Charger Laptop', 'Monitor 24 Inch', 'Webcam Full HD', 'Speaker Bluetooth'];
            } elseif ($catData['name'] === 'Alat Tulis') {
                $products = ['Buku Tulis A5', 'Pulpen Gel Hitam', 'Pensil 2B', 'Penghapus', 'Penggaris 30cm', 'Map Diamond', 'Kertas HVS A4', 'Stapler', 'Isi Staples', 'Gunting'];
            } elseif ($catData['name'] === 'Perlengkapan Rumah') {
                $products = ['Lampu LED 10W', 'Sapu Ijuk', 'Pel Lantai', 'Ember Plastik', 'Gayung', 'Keset Kaki', 'Gantungan Baju', 'Botol Minum 1L', 'Tempat Sampah', 'Stop Kontak 5 Lubang'];
            } elseif ($catData['name'] === 'Aksesoris') {
                $products = ['Dompet Kulit', 'Jam Tangan Digital', 'Kacamata Hitam', 'Gantungan Kunci', 'Tas Ransel', 'Topi Baseball', 'Ikat Pinggang', 'Payung Lipat', 'Masker Kain', 'Sarung Tangan'];
            } elseif ($catData['name'] === 'Alat Kebersihan') {
                $products = ['Sikat Kamar Mandi', 'Kain Lap Microfiber', 'Kemoceng Bulu Ayam', 'Semprotan Air', 'Spons Cuci Piring', 'Sikat Baju', 'Wiper Jendela', 'Pengki Debu', 'Sabun Cuci Tangan', 'Pengharum Ruangan'];
            }

            foreach ($products as $productName) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'description' => 'Deskripsi untuk ' . $productName,
                    'price' => rand(10, 500) * 1000,
                    'stock' => rand(10, 100),
                    'low_stock_threshold' => rand(5, 10),
                ]);
            }
        }
    }
}
