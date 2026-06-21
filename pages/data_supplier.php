<?php
$keyword  = trim($_GET['keyword'] ?? '');
$editId   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$formData = ['id' => '', 'nama_supplier' => '', 'telepon' => '', 'alamat' => ''];

if ($editId > 0) {
    $selected = $supplierModel->getById($editId);
    if ($selected) $formData = $selected;
}

$dataSupplier = $supplierModel->getAll($keyword);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-truck me-2" style="color:var(--accent)"></i>Data Supplier</h3>
        <p class="subtitle">Kelola data master pemasok produk.</p>
    </div>
</div>

<div class="row g-3">
    <!-- FORM -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header accent">
                <i class="bi bi-pencil-square me-2"></i><?= $editId > 0 ? 'Edit Supplier' : 'Tambah Supplier' ?>
            </div>
            <div class="card-body">
                <form method="post" action="proses_supplier.php">
                    <input type="hidden" name="id" value="<?= e($formData['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier" class="form-control"
                               value="<?= e($formData['nama_supplier']) ?>"
                               placeholder="Nama perusahaan / toko" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text" style="border:1.5px solid var(--border-color);border-right:none;background:#f8fafc;font-size:13px">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" name="telepon" class="form-control" style="border-left:none"
                                   value="<?= e($formData['telepon']) ?>" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"
                                  placeholder="Jl. ..."><?= e($formData['alamat']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-save me-1"></i><?= $editId > 0 ? 'Update' : 'Simpan' ?>
                        </button>
                        <?php if ($editId > 0) : ?>
                            <a href="index.php?page=data_supplier" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <form class="d-flex gap-2 filter-area" method="get">
                    <input type="hidden" name="page" value="data_supplier">
                    <input type="search" name="keyword" class="form-control"
                           placeholder="&#128269; Cari supplier..." value="<?= e($keyword) ?>" style="max-width:280px">
                    <button type="submit" class="btn btn-outline-primary">Cari</button>
                    <?php if ($keyword) : ?>
                        <a href="index.php?page=data_supplier" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Nama Supplier</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataSupplier)) : ?>
                                <?php $no = 1; foreach ($dataSupplier as $row) : ?>
                                    <tr>
                                        <td class="text-muted"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:32px;height:32px;border-radius:8px;background:rgba(14,165,233,.1);display:flex;align-items:center;justify-content:center;color:#0ea5e9;flex-shrink:0">
                                                    <i class="bi bi-building" style="font-size:13px"></i>
                                                </div>
                                                <span style="font-weight:600"><?= e($row['nama_supplier']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($row['telepon']) : ?>
                                                <a href="tel:<?= e($row['telepon']) ?>" style="color:var(--text-main);text-decoration:none">
                                                    <i class="bi bi-telephone me-1" style="color:var(--color-green)"></i><?= e($row['telepon']) ?>
                                                </a>
                                            <?php else : ?>
                                                <span style="color:var(--text-muted)"><em>—</em></span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="color:var(--text-muted);max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($row['alamat']) ?: '<em>—</em>' ?></td>
                                        <td>
                                            <a href="index.php?page=data_supplier&id=<?= e($row['id']) ?>" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="hapus_supplier.php?id=<?= e($row['id']) ?>" class="btn btn-danger btn-sm"
                                               title="Hapus" onclick="return confirm('Yakin ingin menghapus supplier ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5" style="color:var(--text-muted)">
                                        <i class="bi bi-inbox" style="font-size:2rem;opacity:.4"></i>
                                        <p class="mt-2 mb-0">Data supplier belum tersedia.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($dataSupplier)) : ?>
                <div class="card-header" style="border-top:1px solid var(--border-color);border-bottom:none;font-size:12px;color:var(--text-muted)">
                    Menampilkan <?= count($dataSupplier) ?> data supplier
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
