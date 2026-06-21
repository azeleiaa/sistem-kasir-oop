<?php
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Supplier.php";

$database = new Database();
$db = $database->getConnection();
$supplierModel = new Supplier($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    setFlash('error', 'ID supplier tidak valid.');
    redirect('index.php?page=data_supplier');
}

try {
    $supplierModel->delete($id);
    setFlash('success', 'Data supplier berhasil dihapus.');
} catch (PDOException $e) {
    setFlash('error', 'Supplier tidak dapat dihapus karena masih dipakai oleh produk.');
}

redirect('index.php?page=data_supplier');
