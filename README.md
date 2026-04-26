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

## Akun Login (Default)
Semua akun menggunakan password: `password`

| Role | Email | Deskripsi |
| --- | --- | --- |
| **Super Admin** | `superadmin@example.com` | Akses penuh ke seluruh sistem, termasuk laporan |
| **Admin** | `admin@example.com` | Manajemen produk, kategori, dan supplier |
| **Kasir** | `cashier@example.com` | Melakukan transaksi penjualan |

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
