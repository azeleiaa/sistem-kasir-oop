# Analisis Kebutuhan Sistem Kasir

## Deskripsi Singkat Sistem

Sistem Kasir OOP adalah aplikasi web sederhana untuk membantu toko kecil mencatat data produk, kategori, supplier, dan transaksi penjualan. Aplikasi dibuat menggunakan PHP Native berbasis Object-Oriented Programming, database MySQL, PDO, dan tampilan Bootstrap.

## Masalah yang Ingin Diselesaikan

1. Pencatatan produk, kategori, dan supplier masih dilakukan manual.
2. Transaksi penjualan perlu dihitung otomatis agar mengurangi kesalahan total bayar.
3. Stok produk perlu berkurang otomatis setelah transaksi.
4. Pemilik toko membutuhkan laporan penjualan sederhana berdasarkan tanggal atau kata kunci.

## Pengguna Sistem

1. Admin toko: mengelola data kategori, supplier, dan produk.
2. Kasir: menambahkan transaksi penjualan dan melihat detail transaksi.
3. Pemilik toko: melihat dashboard dan laporan penjualan sederhana.

## Kebutuhan Fungsional

1. Sistem menampilkan dashboard ringkasan data.
2. Sistem mengelola data master kategori dengan fitur tambah, tampil, edit, hapus, dan pencarian.
3. Sistem mengelola data master supplier dengan fitur tambah, tampil, edit, hapus, dan pencarian.
4. Sistem mengelola data master produk dengan fitur tambah, tampil, edit, hapus, pencarian, dan filter kategori.
5. Sistem membuat kode produk otomatis.
6. Sistem membuat nomor faktur penjualan otomatis.
7. Sistem menambahkan transaksi penjualan dengan relasi ke produk.
8. Sistem menghitung total, pembayaran, dan kembalian secara otomatis.
9. Sistem mengurangi stok produk setelah transaksi berhasil.
10. Sistem menampilkan data transaksi dan detail transaksi.
11. Sistem menampilkan laporan sederhana dengan filter tanggal dan tombol cetak.
12. Sistem melakukan validasi form untuk data wajib, angka, tanggal, dan stok.

## Kebutuhan Non-Fungsional

1. Aplikasi menggunakan PHP Native berbasis OOP.
2. Koneksi database menggunakan PDO.
3. Tampilan menggunakan Bootstrap.
4. Query database menggunakan prepared statement.
5. Aplikasi dapat dijalankan melalui XAMPP atau Laragon.
6. Struktur folder dipisahkan menjadi konfigurasi, class, halaman, proses, dan database.

## Data Master dan Transaksi

Data master:

1. Kategori
2. Supplier
3. Produk

Data transaksi:

1. Penjualan
2. Detail Penjualan

## Tabel Database

1. kategori
2. supplier
3. produk
4. penjualan
5. detail_penjualan

## Class OOP

1. Database
2. Kategori
3. Supplier
4. Produk
5. Penjualan

## Fitur Tambahan

1. Filter data produk berdasarkan kategori.
2. Laporan sederhana penjualan berdasarkan tanggal.
3. Kode produk otomatis.
4. Nomor faktur otomatis.
5. Hitung total otomatis pada transaksi.
