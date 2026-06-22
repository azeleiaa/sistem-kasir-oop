<?php
// Menyiapkan data kategori untuk form dan tabel
$keyword = trim($_GET['keyword'] ?? '');
$editId  = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$formData = ['id' => '', 'nama_kategori' => '', 'deskripsi' => ''];

if ($editId > 0) {
    $selected = $kategoriModel->getById($editId);
    if ($selected) $formData = $selected;
}

$dataKategori = $kategoriModel->getAll($keyword);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-tags me-2" style="color:var(--accent)"></i>Data Kategori</h3>
        <p class="subtitle">Kelola data master kategori produk.</p>
    </div>
</div>

<div class="row g-3">
    <!-- Form kategori -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header accent">
                <i class="bi bi-pencil-square me-2"></i><?= $editId > 0 ? 'Edit Kategori' : 'Tambah Kategori' ?>
            </div>
            <div class="card-body">
                <form method="post" action="proses_kategori.php">
                    <input type="hidden" name="id" value="<?= e($formData['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="<?= e($formData['nama_kategori']) ?>"
                               placeholder="Contoh: Makanan" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"
                                  placeholder="Deskripsi singkat kategori..."><?= e($formData['deskripsi']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-save me-1"></i><?= $editId > 0 ? 'Update' : 'Simpan' ?>
                        </button>
                        <?php if ($editId > 0) : ?>
                            <a href="index.php?page=data_kategori" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel kategori -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <form class="d-flex gap-2 filter-area" method="get">
                    <input type="hidden" name="page" value="data_kategori">
                    <input type="search" name="keyword" class="form-control"
                           placeholder="&#128269; Cari kategori..." value="<?= e($keyword) ?>" style="max-width:280px">
                    <button type="submit" class="btn btn-outline-primary">Cari</button>
                    <?php if ($keyword) : ?>
                        <a href="index.php?page=data_kategori" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataKategori)) : ?>
                                <?php $no = 1; foreach ($dataKategori as $row) : ?>
                                    <tr>
                                        <td class="text-muted"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--accent);flex-shrink:0">
                                                    <i class="bi bi-tag" style="font-size:13px"></i>
                                                </div>
                                                <span style="font-weight:600"><?= e($row['nama_kategori']) ?></span>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-muted)"><?= e($row['deskripsi']) ?: '<em>—</em>' ?></td>
                                        <td>
                                            <a href="index.php?page=data_kategori&id=<?= e($row['id']) ?>" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="hapus_kategori.php?id=<?= e($row['id']) ?>" class="btn btn-danger btn-sm"
                                               title="Hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5" style="color:var(--text-muted)">
                                        <i class="bi bi-inbox" style="font-size:2rem;opacity:.4"></i>
                                        <p class="mt-2 mb-0">Data kategori belum tersedia.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($dataKategori)) : ?>
                <div class="card-header" style="border-top:1px solid var(--border-color);border-bottom:none;font-size:12px;color:var(--text-muted)">
                    Menampilkan <?= count($dataKategori) ?> data kategori
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
