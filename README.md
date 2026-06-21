# Sistem Kasir OOP

Aplikasi kasir sederhana sesuai ketentuan UAS Pemrograman Web. Dibuat dengan PHP Native OOP, MySQL PDO, dan Bootstrap.

## Ruang Lingkup

- Dashboard
- CRUD data master Kategori
- CRUD data master Supplier
- CRUD data master Produk
- Tambah dan tampil data transaksi Penjualan
- Detail transaksi penjualan
- Pencarian data
- Filter produk dan laporan penjualan
- Validasi form
- Kode produk otomatis
- Nomor faktur otomatis
- Hitung total otomatis
- Cetak laporan sederhana

## Struktur Folder

```text
sistem-kasir-oop/
|-- classes/
|   |-- Kategori.php
|   |-- Penjualan.php
|   |-- Produk.php
|   |-- Supplier.php
|-- config/
|   |-- Database.php
|-- database/
|   |-- db_kasir_oop.sql
|-- pages/
|   |-- dashboard.php
|   |-- data_kategori.php
|   |-- data_penjualan.php
|   |-- data_produk.php
|   |-- data_supplier.php
|   |-- detail_penjualan.php
|   |-- form_penjualan.php
|-- hapus_kategori.php
|-- hapus_produk.php
|-- hapus_supplier.php
|-- helpers.php
|-- index.php
|-- proses_kategori.php
|-- proses_penjualan.php
|-- proses_produk.php
|-- proses_supplier.php
```

## Cara Menjalankan

1. Pindahkan folder `sistem-kasir-oop` ke folder web server:
   - XAMPP: `htdocs`
   - Laragon: `www`
2. Buka phpMyAdmin.
3. Import file `database/db_kasir_oop.sql`.
4. Sesuaikan akun database pada `config/Database.php` jika username atau password MySQL berbeda.
5. Buka aplikasi di browser:

```text
http://localhost/sistem-kasir-oop/
```

## Pengaturan Database Default

```php
private string $host = "localhost";
private string $dbname = "db_kasir_oop";
private string $username = "root";
private string $password = "";
```

## Catatan

Bootstrap dan Bootstrap Icons digunakan melalui CDN, jadi tampilan paling rapi saat perangkat terhubung ke internet.
