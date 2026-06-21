<?php
$keyword     = trim($_GET['keyword'] ?? '');
$tanggalAwal = trim($_GET['tanggal_awal'] ?? '');
$tanggalAkhir = trim($_GET['tanggal_akhir'] ?? '');
$dataPenjualan = $penjualanModel->getAll($keyword, $tanggalAwal, $tanggalAkhir);
$ringkasan     = $penjualanModel->getRingkasan($keyword, $tanggalAwal, $tanggalAkhir);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-receipt me-2" style="color:var(--accent)"></i>Data Penjualan</h3>
        <p class="subtitle">Laporan transaksi penjualan.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
        <a href="index.php?page=form_penjualan" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Penjualan
        </a>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-indigo">
            <div class="stat-card-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Jumlah Transaksi</div>
                <div class="stat-card-value"><?= e($ringkasan['jumlah_transaksi']) ?></div>
                <?php if ($keyword || $tanggalAwal || $tanggalAkhir) : ?>
                    <div class="stat-card-sub">Hasil filter aktif</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Total Pendapatan</div>
                <div class="stat-card-value" style="font-size:18px;margin-top:4px"><?= rupiah($ringkasan['total_pendapatan']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE CARD -->
<div class="card">
    <div class="card-header">
        <form class="d-flex flex-wrap gap-2 filter-area" method="get">
            <input type="hidden" name="page" value="data_penjualan">
            <input type="search" name="keyword" class="form-control" style="min-width:160px;flex:1"
                   placeholder="&#128269; Cari faktur / pelanggan..." value="<?= e($keyword) ?>">
            <input type="date" name="tanggal_awal" class="form-control" style="min-width:140px;flex:1" value="<?= e($tanggalAwal) ?>">
            <input type="date" name="tanggal_akhir" class="form-control" style="min-width:140px;flex:1" value="<?= e($tanggalAkhir) ?>">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <?php if ($keyword || $tanggalAwal || $tanggalAkhir) : ?>
                <a href="index.php?page=data_penjualan" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Item</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Bayar</th>
                        <th class="text-end">Kembali</th>
                        <th class="text-center">Status</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataPenjualan)) : ?>
                        <?php foreach ($dataPenjualan as $row) : ?>
                            <tr>
                                <td>
                                    <a href="index.php?page=detail_penjualan&id=<?= e($row['id']) ?>"
                                       style="font-weight:700;color:var(--accent);text-decoration:none;font-size:13px">
                                        <?= e($row['no_faktur']) ?>
                                    </a>
                                </td>
                                <td style="color:var(--text-muted)"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td style="font-weight:500"><?= e($row['nama_pelanggan']) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-secondary-soft"><?= e($row['jumlah_item']) ?> item</span>
                                </td>
                                <td class="text-end" style="font-weight:700"><?= rupiah($row['total']) ?></td>
                                <td class="text-end" style="color:var(--text-muted)"><?= rupiah($row['bayar']) ?></td>
                                <td class="text-end" style="color:var(--color-green);font-weight:600"><?= rupiah($row['kembali']) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-success-soft"><?= e($row['status']) ?></span>
                                </td>
                                <td>
                                    <a href="index.php?page=detail_penjualan&id=<?= e($row['id']) ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" class="text-center py-5" style="color:var(--text-muted)">
                                <i class="bi bi-inbox" style="font-size:2rem;opacity:.4"></i>
                                <p class="mt-2 mb-0">Data penjualan belum tersedia.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($dataPenjualan)) : ?>
        <div class="card-header" style="border-top:1px solid var(--border-color);border-bottom:none;font-size:12px;color:var(--text-muted)">
            Menampilkan <?= count($dataPenjualan) ?> transaksi
        </div>
    <?php endif; ?>
</div>
