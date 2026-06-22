<?php
// Menangani penghapusan produk
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Produk.php";

$database = new Database();
$db = $database->getConnection();
$produkModel = new Produk($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    setFlash('error', 'ID produk tidak valid.');
    redirect('index.php?page=data_produk');
}

try {
    // Hapus produk berdasarkan ID yang dipilih
    $produkModel->delete($id);
    setFlash('success', 'Data produk berhasil dihapus.');
} catch (PDOException $e) {
    setFlash('error', 'Produk tidak dapat dihapus karena sudah digunakan pada transaksi.');
}

redirect('index.php?page=data_produk');
