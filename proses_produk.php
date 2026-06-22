<?php
// Menangani simpan dan update data produk
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Produk.php";

$database = new Database();
$db = $database->getConnection();
$produkModel = new Produk($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Akses tidak valid.');
    redirect('index.php?page=data_produk');
}

// Ambil input form lalu normalisasi nilainya
$id = trim($_POST['id'] ?? '');
$kodeProduk = trim($_POST['kode_produk'] ?? '');
$namaProduk = trim($_POST['nama_produk'] ?? '');
$idKategori = (int) ($_POST['id_kategori'] ?? 0);
$idSupplier = (int) ($_POST['id_supplier'] ?? 0);
$hargaBeli = (float) ($_POST['harga_beli'] ?? 0);
$hargaJual = (float) ($_POST['harga_jual'] ?? 0);
$stok = (int) ($_POST['stok'] ?? 0);
$status = trim($_POST['status'] ?? 'Aktif');
$redirectBack = 'index.php?page=data_produk' . ($id !== '' ? '&id=' . (int) $id : '');

if ($kodeProduk === '') {
    // Kode dibuat otomatis jika tidak dikirim dari form
    $kodeProduk = $produkModel->generateKode();
}

if ($namaProduk === '' || $idKategori <= 0 || $idSupplier <= 0) {
    setFlash('error', 'Nama produk, kategori, dan supplier wajib diisi.');
    redirect($redirectBack);
}

if ($hargaBeli < 0 || $hargaJual <= 0 || $stok < 0) {
    setFlash('error', 'Harga dan stok harus diisi dengan nilai yang benar.');
    redirect($redirectBack);
}

if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
    setFlash('error', 'Status produk tidak valid.');
    redirect($redirectBack);
}

$data = [
    'kode_produk' => $kodeProduk,
    'nama_produk' => $namaProduk,
    'id_kategori' => $idKategori,
    'id_supplier' => $idSupplier,
    'harga_beli' => $hargaBeli,
    'harga_jual' => $hargaJual,
    'stok' => $stok,
    'status' => $status,
];

try {
    // Simpan data baru atau update data lama berdasarkan isi id
    if ($id === '') {
        $produkModel->insert($data);
        setFlash('success', 'Data produk berhasil ditambahkan.');
    } else {
        $produkModel->update((int) $id, $data);
        setFlash('success', 'Data produk berhasil diperbarui.');
    }
} catch (PDOException $e) {
    setFlash('error', 'Proses produk gagal: ' . $e->getMessage());
}

redirect('index.php?page=data_produk');
