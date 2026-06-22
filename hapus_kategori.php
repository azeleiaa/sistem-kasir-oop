<?php
// Menangani penghapusan kategori
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Kategori.php";

$database = new Database();
$db = $database->getConnection();
$kategoriModel = new Kategori($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    setFlash('error', 'ID kategori tidak valid.');
    redirect('index.php?page=data_kategori');
}

try {
    // Hapus kategori berdasarkan ID yang dipilih
    $kategoriModel->delete($id);
    setFlash('success', 'Data kategori berhasil dihapus.');
} catch (PDOException $e) {
    setFlash('error', 'Kategori tidak dapat dihapus karena masih dipakai oleh produk.');
}

redirect('index.php?page=data_kategori');
