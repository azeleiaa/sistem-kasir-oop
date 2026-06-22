<?php
// Menangani simpan dan update data supplier
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Supplier.php";

$database = new Database();
$db = $database->getConnection();
$supplierModel = new Supplier($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Akses tidak valid.');
    redirect('index.php?page=data_supplier');
}

// Ambil input form supplier
$id = trim($_POST['id'] ?? '');
$namaSupplier = trim($_POST['nama_supplier'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

if ($namaSupplier === '') {
    setFlash('error', 'Nama supplier wajib diisi.');
    redirect('index.php?page=data_supplier' . ($id !== '' ? '&id=' . (int) $id : ''));
}

if ($telepon !== '' && !preg_match('/^[0-9+\-\s]+$/', $telepon)) {
    setFlash('error', 'Telepon hanya boleh berisi angka, spasi, tanda +, atau tanda -.');
    redirect('index.php?page=data_supplier' . ($id !== '' ? '&id=' . (int) $id : ''));
}

$data = [
    'nama_supplier' => $namaSupplier,
    'telepon' => $telepon,
    'alamat' => $alamat,
];

try {
    // Tentukan apakah data baru atau update
    if ($id === '') {
        $supplierModel->insert($data);
        setFlash('success', 'Data supplier berhasil ditambahkan.');
    } else {
        $supplierModel->update((int) $id, $data);
        setFlash('success', 'Data supplier berhasil diperbarui.');
    }
} catch (PDOException $e) {
    setFlash('error', 'Proses supplier gagal: ' . $e->getMessage());
}

redirect('index.php?page=data_supplier');
