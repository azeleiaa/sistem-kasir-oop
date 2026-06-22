<?php
// Menyiapkan data produk, kategori, dan supplier untuk tampilan
$keyword       = trim($_GET['keyword'] ?? '');
$filterKategori = trim($_GET['id_kategori'] ?? '');
$editId        = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dataKategori  = $kategoriModel->getAll();
$dataSupplier  = $supplierModel->getAll();

$formData = [
    'id' => '', 'kode_produk' => $produkModel->generateKode(),
    'nama_produk' => '', 'id_kategori' => '', 'id_supplier' => '',
    'harga_beli' => '', 'harga_jual' => '', 'stok' => '', 'status' => 'Aktif',
];

if ($editId > 0) {
    $selected = $produkModel->getById($editId);
    if ($selected) $formData = $selected;
}

$dataProduk = $produkModel->getAll($keyword, $filterKategori);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-box-seam me-2" style="color:var(--accent)"></i>Data Produk</h3>
        <p class="subtitle">Kelola data master produk yang dijual di kasir.</p>
    </div>
</div>

<div class="row g-3">
    <!-- Form produk -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header accent">
                <i class="bi bi-pencil-square me-2"></i><?= $editId > 0 ? 'Edit Produk' : 'Tambah Produk' ?>
            </div>
            <div class="card-body">
                <form method="post" action="proses_produk.php">
                    <input type="hidden" name="id" value="<?= e($formData['id']) ?>">

                    <div class="mb-3">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control"
                               value="<?= e($formData['kode_produk']) ?>" readonly>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px"><i class="bi bi-info-circle me-1"></i>Kode dibuat otomatis</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control"
                               value="<?= e($formData['nama_produk']) ?>" placeholder="Nama produk" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">Pilih...</option>
                                <?php foreach ($dataKategori as $k) : ?>
                                    <option value="<?= e($k['id']) ?>" <?= (string) $formData['id_kategori'] === (string) $k['id'] ? 'selected' : '' ?>>
                                        <?= e($k['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="id_supplier" class="form-select" required>
                                <option value="">Pilih...</option>
                                <?php foreach ($dataSupplier as $s) : ?>
                                    <option value="<?= e($s['id']) ?>" <?= (string) $formData['id_supplier'] === (string) $s['id'] ? 'selected' : '' ?>>
                                        <?= e($s['nama_supplier']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text" style="border:1.5px solid var(--border-color);border-right:none;background:#f8fafc;font-size:12px">Rp</span>
                                <input type="number" name="harga_beli" class="form-control" style="border-left:none" min="0" value="<?= e($formData['harga_beli']) ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border:1.5px solid var(--border-color);border-right:none;background:#f8fafc;font-size:12px">Rp</span>
                                <input type="number" name="harga_jual" class="form-control" style="border-left:none" min="1" value="<?= e($formData['harga_jual']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" min="0" value="<?= e($formData['stok']) ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Aktif" <?= $formData['status'] === 'Aktif' ? 'selected' : '' ?>>✅ Aktif</option>
                                <option value="Nonaktif" <?= $formData['status'] === 'Nonaktif' ? 'selected' : '' ?>>⛔ Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-save me-1"></i><?= $editId > 0 ? 'Update' : 'Simpan' ?>
                        </button>
                        <?php if ($editId > 0) : ?>
                            <a href="index.php?page=data_produk" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel produk -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <form class="d-flex flex-wrap gap-2 filter-area" method="get">
                    <input type="hidden" name="page" value="data_produk">
                    <input type="search" name="keyword" class="form-control" style="min-width:160px;flex:1"
                           placeholder="&#128269; Cari produk..." value="<?= e($keyword) ?>">
                    <select name="id_kategori" class="form-select" style="min-width:130px;flex:1">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($dataKategori as $k) : ?>
                            <option value="<?= e($k['id']) ?>" <?= (string) $filterKategori === (string) $k['id'] ? 'selected' : '' ?>>
                                <?= e($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <?php if ($keyword || $filterKategori) : ?>
                        <a href="index.php?page=data_produk" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Status</th>
                                <th style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataProduk)) : ?>
                                <?php foreach ($dataProduk as $row) : ?>
                                    <tr>
                                        <td><code style="font-size:11.5px;background:#f1f5f9;padding:3px 7px;border-radius:5px;color:var(--accent)"><?= e($row['kode_produk']) ?></code></td>
                                        <td>
                                            <div style="font-weight:600"><?= e($row['nama_produk']) ?></div>
                                            <div style="font-size:11.5px;color:var(--text-muted)"><?= e($row['nama_supplier']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-indigo-soft"><?= e($row['nama_kategori']) ?></span>
                                        </td>
                                        <td class="text-end" style="font-weight:600"><?= rupiah($row['harga_jual']) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= (int) $row['stok'] <= 5 ? 'badge-danger-soft' : 'badge-secondary-soft' ?>">
                                                <?= e($row['stok']) ?> unit
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $row['status'] === 'Aktif' ? 'badge-success-soft' : 'badge-secondary-soft' ?>">
                                                <?= e($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="index.php?page=data_produk&id=<?= e($row['id']) ?>" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="hapus_produk.php?id=<?= e($row['id']) ?>" class="btn btn-danger btn-sm"
                                               title="Hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5" style="color:var(--text-muted)">
                                        <i class="bi bi-inbox" style="font-size:2rem;opacity:.4"></i>
                                        <p class="mt-2 mb-0">Data produk belum tersedia.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($dataProduk)) : ?>
                <div class="card-header" style="border-top:1px solid var(--border-color);border-bottom:none;font-size:12px;color:var(--text-muted)">
                    Menampilkan <?= count($dataProduk) ?> produk
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
