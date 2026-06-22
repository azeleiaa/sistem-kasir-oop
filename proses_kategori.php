<?php
// Menangani simpan dan update data kategori
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Kategori.php";

$database = new Database();
$db = $database->getConnection();
$kategoriModel = new Kategori($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Akses tidak valid.');
    redirect('index.php?page=data_kategori');
}

// Ambil input form lalu bersihkan spasi di awal dan akhir
$id = trim($_POST['id'] ?? '');
$namaKategori = trim($_POST['nama_kategori'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

if ($namaKategori === '') {
    setFlash('error', 'Nama kategori wajib diisi.');
    redirect('index.php?page=data_kategori' . ($id !== '' ? '&id=' . (int) $id : ''));
}

$data = [
    'nama_kategori' => $namaKategori,
    'deskripsi' => $deskripsi,
];

try {
    // Jika id kosong berarti data baru, kalau ada berarti update
    if ($id === '') {
        $kategoriModel->insert($data);
        setFlash('success', 'Data kategori berhasil ditambahkan.');
    } else {
        $kategoriModel->update((int) $id, $data);
        setFlash('success', 'Data kategori berhasil diperbarui.');
    }
} catch (PDOException $e) {
    setFlash('error', 'Proses kategori gagal: ' . $e->getMessage());
}

redirect('index.php?page=data_kategori');
