# APK Kasir & Reorder System

Sistem Point-of-Sale (POS) dan Inventory Management menggunakan Laravel Filament.

## Persyaratan
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB

## Cara Menjalankan
1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd apk-kasir-reorder
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   - Salin `.env.example` ke `.env`
   - Sesuaikan konfigurasi database di `.env`
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi dan Seeding**
   Jalankan perintah ini untuk membuat tabel dan mengisi data awal (user & produk):
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   npm run dev
   ```
   Akses aplikasi di `http://localhost:8000/admin`

## Perintah Database Seeder

Aplikasi menyediakan beberapa database seeder untuk inisialisasi data:

1. **DatabaseSeeder (Utama)**
   ```bash
   php artisan db:seed
   ```
   *Catatan Perilaku Environment:*
   - **Local/Development (`APP_ENV=local`):** Menjalankan `RolesAndPermissionsSeeder` (membuat role & user default) dan `ProductSeeder` (membuat kategori & produk dummy untuk testing).
   - **Production (`APP_ENV=production`):** Hanya menjalankan `RolesAndPermissionsSeeder` untuk keamanan data produksi. `ProductSeeder` (data dummy) secara otomatis diabaikan.

2. **Roles and Permissions Seeder**
   Untuk menginisialisasi role (`owner`, `admin`, `cashier`) beserta user bawaan:
   ```bash
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

3. **Product Seeder (Hanya Local)**
   Untuk menginisialisasi kategori dan produk dummy (untuk keperluan testing/development):
   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

4. **Logo Seeder**
   Untuk menginisialisasi pengaturan branding seperti logo dan favicon bawaan ke dalam database & storage:
   ```bash
   php artisan db:seed --class=LogoSeeder
   ```

## Perintah Konsol Kustom (Custom Commands)

Sistem memiliki perintah artisan kustom untuk mempermudah operasional:

### 1. Import Data Produk dari Excel
Perintah ini digunakan untuk mengimpor data produk dan kategori dari template file Excel (`public/template/example/data_produk_dan_category.xlsx`) ke dalam database dan menyalin file gambar produk ke local storage.
```bash
php artisan app:import-product-data
```

## Akun Login (Default)
Semua akun menggunakan password: `password`

| Role | Email | Deskripsi |
| --- | --- | --- |
| **Owner** | `owner@example.com` | Akses monitor laporan keuangan, stok/ROP, dan pengaturan umum (Read-only) |
| **Admin** | `admin@example.com` | Akses penuh CRUD semua data produk, kategori, supplier, pembelian, transaksi, & user |
| **Kasir** | `cashier@example.com` | Hanya akses halaman POS kasir, riwayat transaksi penjualan, dan rekap shift |

## Struktur Tabel Utama
- **users**: Menyimpan data pengguna dan role (via Spatie Permission).
- **categories**: Kategori produk (Elektronik, Alat Tulis, dll).
- **products**: Data barang, harga, stok, dan threshold stok menipis.
- **suppliers**: Data vendor/supplier barang.
- **orders**: Data transaksi penjualan.
- **order_items**: Detail item dalam setiap transaksi penjualan.
- **purchases**: Data pembelian barang ke supplier (Reorder).
- **purchase_items**: Detail item dalam setiap pembelian stock.

## Fitur Utama
- **Dashboard**: Ringkasan stok dan penjualan.
- **Manajemen POS**: Input transaksi penjualan dengan mudah.
- **Inventory Reorder**: Manajemen stok dan pembelian kembali ke supplier.
- **Reporting**: Laporan penjualan untuk Superadmin.
- **Role Management**: Pembatasan akses berdasarkan peran pengguna.
