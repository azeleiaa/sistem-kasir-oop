CREATE DATABASE IF NOT EXISTS db_kasir_oop;
USE db_kasir_oop;

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    telepon VARCHAR(30) NULL,
    alamat TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(30) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    id_kategori INT NOT NULL,
    id_supplier INT NOT NULL,
    harga_beli DECIMAL(12,2) NOT NULL DEFAULT 0,
    harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id),
    CONSTRAINT fk_produk_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id)
);

CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_faktur VARCHAR(40) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    nama_pelanggan VARCHAR(100) NOT NULL DEFAULT 'Umum',
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    bayar DECIMAL(12,2) NOT NULL DEFAULT 0,
    kembali DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'Selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    id_produk INT NOT NULL,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    qty INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_detail_penjualan FOREIGN KEY (id_penjualan) REFERENCES penjualan(id),
    CONSTRAINT fk_detail_produk FOREIGN KEY (id_produk) REFERENCES produk(id)
);

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Makanan', 'Produk makanan ringan dan siap jual'),
('Minuman', 'Produk minuman kemasan'),
('Kebutuhan Harian', 'Barang kebutuhan harian toko');

INSERT INTO supplier (nama_supplier, telepon, alamat) VALUES
('PT Sumber Sejahtera', '081234567890', 'Jl. Gatot Subroto No. 12'),
('CV Prima Distribusi', '081298765432', 'Jl. Tukad Yeh Aya No. 8'),
('UD Toko Grosir', '081122334455', 'Jl. Diponegoro No. 21');

INSERT INTO produk (kode_produk, nama_produk, id_kategori, id_supplier, harga_beli, harga_jual, stok, status) VALUES
('PRD260615001', 'Air Mineral 600ml', 2, 1, 2500, 4000, 50, 'Aktif'),
('PRD260615002', 'Roti Coklat', 1, 2, 3500, 6000, 35, 'Aktif'),
('PRD260615003', 'Sabun Mandi', 3, 3, 7000, 10000, 25, 'Aktif');
