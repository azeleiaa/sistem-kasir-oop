<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$penjualan = $id > 0 ? $penjualanModel->getById($id) : null;

if (!$penjualan) {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>Data penjualan tidak ditemukan.</div>';
    return;
}

$detail = $penjualanModel->getDetail($id);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-file-text me-2" style="color:var(--accent)"></i>Detail Penjualan</h3>
        <p class="subtitle"><?= e($penjualan['no_faktur']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak Struk
        </button>
        <a href="index.php?page=data_penjualan" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<!-- INFO CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body" style="padding:16px">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px">No Faktur</div>
                <div style="font-weight:700;font-size:14px;color:var(--accent)"><?= e($penjualan['no_faktur']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body" style="padding:16px">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px">Tanggal</div>
                <div style="font-weight:600;font-size:14px"><?= date('d F Y', strtotime($penjualan['tanggal'])) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body" style="padding:16px">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px">Pelanggan</div>
                <div style="font-weight:600;font-size:14px"><?= e($penjualan['nama_pelanggan']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body" style="padding:16px">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px">Pembayaran</div>
                <?php $metode = $penjualan['metode_pembayaran'] ?? 'Tunai'; ?>
                <span class="badge <?= $metode === 'QRIS' ? 'badge-indigo-soft' : 'badge-success-soft' ?>" style="font-size:13px;padding:5px 12px">
                    <?= $metode === 'QRIS' ? '📱 QRIS' : '💵 Tunai' ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ITEMS TABLE -->
<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-list-ul me-2" style="color:var(--accent)"></i>Item Pembelian
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($detail as $row) : ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td><code style="font-size:11.5px;background:#f1f5f9;padding:3px 7px;border-radius:5px;color:var(--accent)"><?= e($row['kode_produk']) ?></code></td>
                            <td style="font-weight:600"><?= e($row['nama_produk']) ?></td>
                            <td class="text-end" style="color:var(--text-muted)"><?= rupiah($row['harga']) ?></td>
                            <td class="text-center"><span class="badge badge-indigo-soft"><?= e($row['qty']) ?></span></td>
                            <td class="text-end" style="font-weight:700"><?= rupiah($row['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc">
                        <td colspan="5" class="text-end" style="font-weight:600;color:var(--text-muted);font-size:12px;text-transform:uppercase;letter-spacing:.04em">Total</td>
                        <td class="text-end" style="font-weight:800;font-size:16px;color:var(--text-main)"><?= rupiah($penjualan['total']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end" style="font-weight:600;color:var(--text-muted);font-size:12px;text-transform:uppercase;letter-spacing:.04em">Bayar</td>
                        <td class="text-end" style="font-weight:700"><?= rupiah($penjualan['bayar']) ?></td>
                    </tr>
                    <tr style="background:rgba(16,185,129,.05)">
                        <td colspan="5" class="text-end" style="font-weight:700;color:#059669;font-size:12px;text-transform:uppercase;letter-spacing:.04em">Kembali</td>
                        <td class="text-end" style="font-weight:800;font-size:16px;color:var(--color-green)"><?= rupiah($penjualan['kembali']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
