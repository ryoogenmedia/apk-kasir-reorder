---
description: USULAN TAMBAHAN LAPORAN (REPORTS)
---

# Analisis Kebutuhan Laporan Aplikasi Kasir (Warung Campuran)

Selain **Laporan Transaksi (Buku Besar/Pemasukan & Pengeluaran)** yang sudah direncanakan, sebuah aplikasi kasir yang berfokus pada fitur "Reorder" dan manajemen warung yang baik sangat membutuhkan beberapa laporan esensial berikut untuk memantau kesehatan bisnis:

## 1. [LAPORAN LABA RUGI (PROFIT & LOSS)]

**Description:**
Laporan ini menghitung keuntungan bersih kotor (Gross Profit) berdasarkan `Total Penjualan (Pendapatan)` dikurangi `Harga Pokok Penjualan (Modal/Purchase Price)`. 
* **Alasan:** Omset jualan besar belum tentu untung jika tidak dihitung selisih modalnya. Fitur ini sangat disukai oleh *owner* warung.

## 2. [LAPORAN STOK & REORDER (INVENTORY & REORDER REPORT)]

**Description:**
Laporan yang berfokus pada manajemen barang. Menampilkan barang apa saja yang stoknya sudah habis, menipis (di bawah *low stock threshold*), atau berlebihan (di atas *max stock threshold*).
* **Alasan:** Sesuai dengan nama aplikasi "apk-kasir-reorder", fitur laporan ini akan menjadi pedoman utama *owner* sebelum melakukan belanja ke Supplier.

## 3. [LAPORAN PRODUK TERLARIS (BEST SELLING PRODUCTS)]

**Description:**
Laporan analitik yang menampilkan daftar produk yang paling banyak dibeli atau paling banyak menghasilkan uang dalam rentang waktu tertentu (Harian/Bulanan).
* **Alasan:** Membantu pemilik warung mengambil keputusan strategi penyediaan barang. Produk yang terlaris jangan sampai kehabisan stok.

## 4. [LAPORAN SHIFT / REKAP HARIAN KASIR]

**Description:**
Laporan rekap tutup kasir di penghujung hari yang memisahkan jumlah uang tunai (Cash) yang harus ada di laci kasir dengan jumlah uang digital (QRIS).
* **Alasan:** Untuk mencegah kerugian akibat selisih uang atau kesalahan kasir (Fraud). Laporan ini mencocokkan sistem dengan uang fisik yang ada di laci.

## 5. [LAPORAN PEMBELIAN PER SUPPLIER]

**Description:**
Laporan yang menampilkan rincian total belanja warung ke setiap Supplier, rentang waktunya, dan statusnya.
* **Alasan:** Berguna untuk melihat pengeluaran spesifik per supplier dan membantu melakukan evaluasi supplier mana yang paling sering memasok barang.

---
Silakan direview usulan laporan di atas. Jika Anda setuju, kita bisa mulai mengerjakannya satu per satu ke dalam sistem Filament!
