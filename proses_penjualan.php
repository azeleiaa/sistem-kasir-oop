<?php
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Penjualan.php";

$database = new Database();
$db = $database->getConnection();
$penjualanModel = new Penjualan($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Akses tidak valid.');
    redirect('index.php?page=data_penjualan');
}

$tanggal        = trim($_POST['tanggal'] ?? '');
$namaPelanggan  = trim($_POST['nama_pelanggan'] ?? 'Umum');
$metodePembayaran = trim($_POST['metode_pembayaran'] ?? 'Tunai');
$bayar          = (float) ($_POST['bayar'] ?? 0);
$produkList     = $_POST['id_produk'] ?? [];
$qtyList        = $_POST['qty'] ?? [];

if (!in_array($metodePembayaran, ['Tunai', 'QRIS'], true)) {
    $metodePembayaran = 'Tunai';
}

if ($tanggal === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    setFlash('error', 'Tanggal transaksi wajib diisi dengan benar.');
    redirect('index.php?page=form_penjualan');
}

if ($namaPelanggan === '') {
    $namaPelanggan = 'Umum';
}

// kalau QRIS, nilai bayar akan di-set server-side jadi tidak perlu validasi nominal di sini
if ($metodePembayaran === 'QRIS') {
    $bayar = PHP_INT_MAX;
} elseif ($bayar <= 0) {
    setFlash('error', 'Nominal bayar wajib diisi.');
    redirect('index.php?page=form_penjualan');
}

$itemsMap = [];
foreach ($produkList as $index => $idProduk) {
    $idProduk = (int) $idProduk;
    $qty = (int) ($qtyList[$index] ?? 0);

    if ($idProduk > 0 && $qty > 0) {
        if (!isset($itemsMap[$idProduk])) {
            $itemsMap[$idProduk] = [
                'id_produk' => $idProduk,
                'qty' => 0,
            ];
        }
        $itemsMap[$idProduk]['qty'] += $qty;
    }
}

if (empty($itemsMap)) {
    setFlash('error', 'Minimal pilih satu produk penjualan.');
    redirect('index.php?page=form_penjualan');
}

$data = [
    'tanggal'             => $tanggal,
    'nama_pelanggan'      => $namaPelanggan,
    'metode_pembayaran'   => $metodePembayaran,
    'bayar'               => $bayar,
];

try {
    $idPenjualan = $penjualanModel->create($data, array_values($itemsMap));
    setFlash('success', 'Transaksi penjualan berhasil disimpan.');
    redirect('index.php?page=detail_penjualan&id=' . $idPenjualan);
} catch (Throwable $e) {
    setFlash('error', 'Transaksi gagal: ' . $e->getMessage());
    redirect('index.php?page=form_penjualan');
}
