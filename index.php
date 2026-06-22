<?php
// File utama yang memuat semua halaman aplikasi
session_start();

require_once "helpers.php";
require_once "config/Database.php";
require_once "classes/Kategori.php";
require_once "classes/Supplier.php";
require_once "classes/Produk.php";
require_once "classes/Penjualan.php";

$database = new Database();
$db = $database->getConnection();

$kategoriModel = new Kategori($db);
$supplierModel = new Supplier($db);
$produkModel = new Produk($db);
$penjualanModel = new Penjualan($db);

// Tentukan halaman aktif berdasarkan parameter page
$page = $_GET['page'] ?? 'dashboard';
$allowedPages = [
    'dashboard'        => 'pages/dashboard.php',
    'data_kategori'    => 'pages/data_kategori.php',
    'data_supplier'    => 'pages/data_supplier.php',
    'data_produk'      => 'pages/data_produk.php',
    'form_penjualan'   => 'pages/form_penjualan.php',
    'data_penjualan'   => 'pages/data_penjualan.php',
    'detail_penjualan' => 'pages/detail_penjualan.php',
];

// Judul halaman yang ditampilkan di tab browser dan header
$pageTitles = [
    'dashboard'        => 'Dashboard',
    'data_kategori'    => 'Data Kategori',
    'data_supplier'    => 'Data Supplier',
    'data_produk'      => 'Data Produk',
    'form_penjualan'   => 'Tambah Penjualan',
    'data_penjualan'   => 'Data Penjualan',
    'detail_penjualan' => 'Detail Penjualan',
];

$currentTitle = $pageTitles[$page] ?? 'Sistem Kasir';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($currentTitle) ?> — Koyta Shop</title>
    <meta name="description" content="Sistem Kasir berbasis OOP untuk manajemen produk, supplier, kategori, dan transaksi penjualan.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
          /* =============================================
              DESIGN SYSTEM — CSS CUSTOM PROPERTIES
          ============================================= */
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(99, 102, 241, 0.15);
            --sidebar-active-bg: linear-gradient(135deg, #6366f1, #8b5cf6);
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-width: 265px;

            --accent:        #6366f1;
            --accent-hover:  #4f46e5;
            --accent-light:  rgba(99, 102, 241, 0.1);

            --color-green:   #10b981;
            --color-amber:   #f59e0b;
            --color-rose:    #f43f5e;
            --color-sky:     #0ea5e9;
            --color-purple:  #8b5cf6;
            --color-indigo:  #6366f1;

            --bg-body:       #f1f5f9;
            --bg-card:       #ffffff;
            --border-color:  #e2e8f0;
            --text-main:     #1e293b;
            --text-muted:    #64748b;

            --radius-sm:     8px;
            --radius-md:     12px;
            --radius-lg:     16px;

            --shadow-sm:     0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
            --shadow-md:     0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:     0 10px 40px rgba(0,0,0,.12);

            --transition:    all .2s ease;
        }

        /* Reset dasar dan font */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            font-size: 14px;
        }

        /* Navbar atas yang sticky */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 64px;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 17px;
            color: var(--text-main);
            text-decoration: none;
            white-space: nowrap;
        }

        .topbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--sidebar-active-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
        }

        .topbar-brand span { color: var(--accent); }

        .topbar-divider {
            width: 1px;
            height: 28px;
            background: var(--border-color);
        }

        .topbar-breadcrumb {
            font-size: 13px;
            color: var(--text-muted);
            flex: 1;
        }

        .topbar-breadcrumb strong {
            color: var(--text-main);
            font-weight: 600;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
            font-size: 16px;
        }

        .topbar-btn:hover {
            background: var(--accent-light);
            border-color: var(--accent);
            color: var(--accent);
        }

        .topbar-date {
            font-size: 12px;
            color: var(--text-muted);
            background: var(--bg-body);
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            white-space: nowrap;
        }

        /* Wrapper utama yang membungkus sidebar dan konten */
        .app-shell {
            display: flex;
            min-height: calc(100vh - 64px);
        }

        /* Sidebar navigasi kiri */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            padding: 20px 12px;
            position: sticky;
            top: 64px;
            height: calc(100vh - 64px);
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #475569;
            padding: 0 12px;
            margin-bottom: 6px;
        }

        .sidebar-section-label:not(:first-child) {
            margin-top: 24px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0 0 4px 0;
        }

        .sidebar-nav li { margin-bottom: 2px; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 13.5px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .sidebar-nav a:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
        }

        .sidebar-nav a.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .sidebar-nav a.active i { color: #fff; }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,.06);
        }

        .sidebar-footer-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: rgba(255,255,255,.04);
        }

        .sidebar-footer-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-footer-text { line-height: 1.3; }
        .sidebar-footer-name { font-size: 12.5px; font-weight: 600; color: #e2e8f0; }
        .sidebar-footer-role { font-size: 11px; color: #475569; }

        /* area konten utama di sebelah kanan sidebar */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        /* notifikasi sukses / error / warning */
        .alert {
            border: none;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 500;
            padding: 14px 16px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left: 4px solid var(--color-green);
        }

        .alert-danger {
            background: rgba(244, 63, 94, 0.1);
            color: #9f1239;
            border-left: 4px solid var(--color-rose);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #78350f;
            border-left: 4px solid var(--color-amber);
        }

        /* card / panel putih berisi konten */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            background: var(--bg-card);
            transition: var(--transition);
        }

        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            border-bottom: 1px solid var(--border-color);
            border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
            padding: 14px 20px;
            font-weight: 600;
            font-size: 14px;
            background: var(--bg-card) !important;
        }

        .card-header.accent {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #fff;
            border-bottom: none;
        }

        .card-body { padding: 20px; }

        /* kartu statistik berwarna di dashboard */
        .stat-card {
            border: none;
            border-radius: var(--radius-md);
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            height: 100%;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            flex-shrink: 0;
        }

        .stat-card-body { width: 100%; }
        .stat-card-label { font-size: 10.5px; font-weight: 700; opacity: .8; color: #fff; letter-spacing: .08em; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .stat-card-value { font-size: 28px; font-weight: 800; color: #fff; line-height: 1.2; margin-top: 2px; }
        .stat-card-sub { font-size: 11px; opacity: .7; color: #fff; margin-top: 2px; }

        .stat-indigo  { background: linear-gradient(135deg, #6366f1, #818cf8); }
        .stat-green   { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-amber   { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-rose    { background: linear-gradient(135deg, #f43f5e, #fb7185); }
        .stat-sky     { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
        .stat-purple  { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

        /* tabel data */
        .table {
            font-size: 13.5px;
            border-color: var(--border-color);
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 2px solid var(--border-color);
            padding: 10px 14px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 12px 14px;
            border-color: var(--border-color);
            vertical-align: middle;
            color: var(--text-main);
        }

        .table tbody tr {
            transition: background .15s ease;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table-bordered { border: 1px solid var(--border-color); }
        .table-bordered thead th:first-child { border-radius: 0; }

        /* badge / label kecil untuk status */
        .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: .02em;
        }

        .badge-success-soft { background: rgba(16,185,129,.12); color: #059669; }
        .badge-secondary-soft { background: rgba(100,116,139,.1); color: #475569; }
        .badge-danger-soft { background: rgba(244,63,94,.12); color: #e11d48; }
        .badge-amber-soft { background: rgba(245,158,11,.12); color: #d97706; }
        .badge-indigo-soft { background: rgba(99,102,241,.12); color: #4f46e5; }

        /* tombol-tombol aksi */
        .btn {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 13.5px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            padding: 8px 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(99,102,241,.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 4px 16px rgba(99,102,241,.5);
            transform: translateY(-1px);
        }

        .btn-warning {
            background: rgba(245,158,11,.12);
            border: 1px solid rgba(245,158,11,.3);
            color: #d97706;
        }

        .btn-warning:hover {
            background: #f59e0b;
            color: #fff;
        }

        .btn-danger {
            background: rgba(244,63,94,.1);
            border: 1px solid rgba(244,63,94,.3);
            color: #e11d48;
        }

        .btn-danger:hover {
            background: #f43f5e;
            color: #fff;
        }

        .btn-secondary {
            background: #f1f5f9;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .btn-outline-primary {
            border: 1px solid var(--accent);
            color: var(--accent);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--accent);
            color: #fff;
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 6px; }

        /* input, select, label */
        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .form-control, .form-select {
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 9px 12px;
            transition: var(--transition);
            background: var(--bg-card);
            color: var(--text-main);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
            outline: none;
        }

        .form-control[readonly] {
            background: #f8fafc;
            color: var(--text-muted);
            cursor: not-allowed;
        }

        /* judul halaman + tombol aksi di pojok kanan */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .page-header h3 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .page-header .subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        /* sembunyikan elemen UI saat dicetak */
        @media print {
            .topbar, .sidebar, .btn, .page-header .btn,
            form.filter-area, .alert { display: none !important; }
            .app-shell { display: block; }
            .main-content { padding: 0; }
            body { background: #fff; font-size: 12px; }
            .card { box-shadow: none; border: 1px solid #ccc; }
        }

        /* layar kecil: sembunyikan sidebar */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { padding: 16px; }
            .topbar { padding: 0 16px; }
            .stat-card-value { font-size: 20px; }
        }
    </style>
</head>
<body>

<!-- navbar atas -->
<header class="topbar">
    <a href="index.php" class="topbar-brand">
        <div class="brand-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 7h16l-1.5 11a2 2 0 01-2 1.8H7.5a2 2 0 01-2-1.8L4 7z" fill="rgba(255,255,255,0.9)"/>
                <path d="M9 7V5.5a3 3 0 016 0V7" stroke="white" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                <!-- bintang kecil di tengah tas -->
                <path d="M12 11l.6 1.8H14.5l-1.5 1.1.6 1.8L12 14.6l-1.6 1.1.6-1.8-1.5-1.1h1.9L12 11z" fill="#6366f1"/>
            </svg>
        </div>
        <span style="color:#000">Koyta<span style="color:var(--accent)">Shop</span></span>
    </a>
    <div class="topbar-divider d-none d-md-block"></div>
    <div class="topbar-breadcrumb d-none d-md-block">
        Halaman &rsaquo; <strong><?= e($currentTitle) ?></strong>
    </div>
    <div class="topbar-actions">
        <div class="topbar-date d-none d-sm-block">
            <i class="bi bi-calendar3 me-1"></i>
            <?= date('d M Y') ?>
        </div>
        <a href="index.php?page=form_penjualan" class="btn btn-primary btn-sm d-none d-md-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Transaksi
        </a>
    </div>
</header>

<div class="app-shell">
    <!-- navigasi sidebar kiri -->
    <aside class="sidebar">
        <div class="sidebar-section-label">Menu Utama</div>
        <ul class="sidebar-nav">
            <li>
                <a href="index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Data Master</div>
        <ul class="sidebar-nav">
            <li>
                <a href="index.php?page=data_kategori" class="<?= $page === 'data_kategori' ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i> Kategori
                </a>
            </li>
            <li>
                <a href="index.php?page=data_supplier" class="<?= $page === 'data_supplier' ? 'active' : '' ?>">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>
            <li>
                <a href="index.php?page=data_produk" class="<?= $page === 'data_produk' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i> Produk
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Transaksi</div>
        <ul class="sidebar-nav">
            <li>
                <a href="index.php?page=form_penjualan" class="<?= $page === 'form_penjualan' ? 'active' : '' ?>">
                    <i class="bi bi-cart-plus"></i> Tambah Penjualan
                </a>
            </li>
            <li>
                <a href="index.php?page=data_penjualan" class="<?= in_array($page, ['data_penjualan', 'detail_penjualan'], true) ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i> Data Penjualan
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-footer-info">
                <div class="sidebar-footer-avatar">K</div>
                <div class="sidebar-footer-text">
                    <div class="sidebar-footer-name">Admin Kasir</div>
                    <div class="sidebar-footer-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- konten halaman utama -->
    <main class="main-content">
        <?php
        // Ambil notifikasi dari session jika ada
        $notifType = '';
        $notifMessage = '';
        if (isset($_SESSION['success'])) {
            $notifType = 'success';
            $notifMessage = e($_SESSION['success']);
            unset($_SESSION['success']);
        } elseif (isset($_SESSION['error'])) {
            $notifType = 'error';
            $notifMessage = e($_SESSION['error']);
            unset($_SESSION['error']);
        }
        ?>

        <?php
        if (array_key_exists($page, $allowedPages)) {
            include $allowedPages[$page];
        } else {
            echo '<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>Halaman tidak ditemukan.</div>';
        }
        ?>
    </main>
</div>

<?php if ($notifType !== '') : ?>
<!-- modal notifikasi -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;box-shadow:var(--shadow-lg);text-align:center;padding:24px 20px">
            <div class="modal-body p-0">
                <?php if ($notifType === 'success') : ?>
                    <div style="font-size:54px;color:var(--color-green);margin-bottom:12px;line-height:1">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h5 style="font-weight:800;color:var(--text-main);margin-bottom:8px">Berhasil!</h5>
                <?php else : ?>
                    <div style="font-size:54px;color:var(--color-rose);margin-bottom:12px;line-height:1">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h5 style="font-weight:800;color:var(--text-main);margin-bottom:8px">Oops!</h5>
                <?php endif; ?>
                <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px">
                    <?= $notifMessage ?>
                </p>
                <button type="button" class="btn <?= $notifType === 'success' ? 'btn-primary' : 'btn-danger' ?> w-100" data-bs-dismiss="modal" style="border-radius:10px;padding:10px;font-size:14px">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var notifModal = new bootstrap.Modal(document.getElementById('notifModal'));
    notifModal.show();
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
