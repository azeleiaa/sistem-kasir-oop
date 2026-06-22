<?php
// Menyiapkan data pilihan produk dan nomor faktur baru
$produkOptions = $produkModel->getOptions();
$noFaktur = $penjualanModel->generateNoFaktur();
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-cart-plus me-2" style="color:var(--accent)"></i>Tambah Penjualan</h3>
        <p class="subtitle">Transaksi baru dengan hitung total & kembalian otomatis.</p>
    </div>
    <a href="index.php?page=data_penjualan" class="btn btn-outline-secondary">
        <i class="bi bi-receipt me-1"></i>Data Penjualan
    </a>
</div>

<?php if (empty($produkOptions)) : ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Belum ada produk aktif dengan stok tersedia. <a href="index.php?page=data_produk" class="alert-link">Tambah produk</a> terlebih dahulu.
    </div>
<?php endif; ?>

<form method="post" action="proses_penjualan.php" id="formPenjualan">
    <!-- Informasi transaksi -->
    <div class="card mb-3">
        <div class="card-header accent">
            <i class="bi bi-receipt-cutoff me-2"></i>Informasi Transaksi
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">No Faktur</label>
                    <input type="text" class="form-control" value="<?= e($noFaktur) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" value="Umum" placeholder="Nama pelanggan" required>
                </div>
            </div>
        </div>
    </div>

    <!-- Item belanja -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-basket me-2" style="color:var(--accent)"></i>Item Belanja</span>
            <button type="button" class="btn btn-sm btn-primary" id="btnTambahItem" <?= empty($produkOptions) ? 'disabled' : '' ?>>
                <i class="bi bi-plus-lg me-1"></i>Tambah Item
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="tabelItem">
                    <thead>
                        <tr>
                            <th style="min-width:240px">Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Stok</th>
                            <th style="width:130px">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody id="itemRows">
                        <tr class="item-row">
                            <td>
                                <select name="id_produk[]" class="form-select product-select" required>
                                    <option value="">— Pilih produk —</option>
                                    <?php foreach ($produkOptions as $p) : ?>
                                        <option value="<?= e($p['id']) ?>" data-harga="<?= e($p['harga_jual']) ?>" data-stok="<?= e($p['stok']) ?>">
                                            <?= e($p['kode_produk']) ?> — <?= e($p['nama_produk']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="harga-text text-end" style="color:var(--text-muted)">Rp 0</td>
                            <td class="stok-text text-center"><span class="badge badge-secondary-soft">0</span></td>
                            <td>
                                <input type="number" name="qty[]" class="form-control qty-input text-center" min="1" value="1" required>
                            </td>
                            <td class="subtotal-text text-end" style="font-weight:700;color:var(--accent)">Rp 0</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-remove" title="Hapus baris">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Panel ringkasan di kanan bawah -->
    <div class="row g-3 justify-content-end">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calculator me-2" style="color:var(--accent)"></i>Ringkasan Pembayaran
                </div>
                <div class="card-body">
                    <!-- Total keseluruhan belanja -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border-color)">
                        <span style="color:var(--text-muted);font-weight:600;font-size:13px">TOTAL BELANJA</span>
                        <span class="fw-bold" id="grandTotal" style="font-size:22px;color:var(--text-main)">Rp 0</span>
                    </div>

                    <!-- Pilih cara bayar tunai atau QRIS -->
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <input type="hidden" name="metode_pembayaran" id="metodePembayaran" value="Tunai">
                        <div class="d-flex gap-2">
                            <button type="button" id="btnTunai" onclick="setMetode('Tunai')"
                                class="btn flex-fill d-flex align-items-center justify-content-center gap-2"
                                style="border:2px solid var(--accent);background:var(--accent);color:#fff;border-radius:10px;padding:10px">
                                <i class="bi bi-cash-coin" style="font-size:18px"></i>
                                <span style="font-weight:700">Tunai</span>
                            </button>
                            <button type="button" id="btnQris" onclick="setMetode('QRIS')"
                                class="btn flex-fill d-flex align-items-center justify-content-center gap-2"
                                style="border:2px solid var(--border-color);background:#fff;color:var(--text-muted);border-radius:10px;padding:10px">
                                <i class="bi bi-qr-code-scan" style="font-size:18px"></i>
                                <span style="font-weight:700">QRIS</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tampil kalau bayar tunai -->
                    <div id="sectionTunai">
                        <div class="mb-3">
                            <label class="form-label">Bayar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border:1.5px solid var(--border-color);border-right:none;background:#f8fafc;font-size:13px">Rp</span>
                                <input type="number" name="bayar" id="bayar" class="form-control" style="border-left:none;font-size:16px;font-weight:600" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3" style="background:rgba(16,185,129,.06);border-radius:var(--radius-sm);border:1px solid rgba(16,185,129,.15)">
                            <span style="font-weight:600;font-size:13px;color:#059669">KEMBALIAN</span>
                            <span id="kembali" style="font-size:20px;font-weight:800;color:var(--color-green)">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tampil kalau pilih QRIS -->
                    <div id="sectionQris" style="display:none">
                        <div class="text-center mb-4 p-3" style="background:#f8fafc;border-radius:12px;border:1px dashed var(--border-color)">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=KOYTASHOP-QRIS-PAYMENT&color=6366f1&bgcolor=ffffff"
                                 alt="QRIS Code" style="width:160px;height:160px;border-radius:8px" onerror="this.style.display='none'">
                            <div style="margin-top:10px;font-weight:700;font-size:13px;color:var(--text-main)">Scan untuk membayar</div>
                            <div style="font-size:12px;color:var(--text-muted)">KOYTA SHOP — QRIS</div>
                            <div id="qrisTotal" class="mt-2" style="font-size:18px;font-weight:800;color:var(--accent)">Rp 0</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnSimpan" style="height:46px;font-size:15px" <?= empty($produkOptions) ? 'disabled' : '' ?>>
                        <i class="bi bi-save me-2"></i>Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const itemRows     = document.getElementById('itemRows');
const bayarInput   = document.getElementById('bayar');
const grandTotalEl = document.getElementById('grandTotal');
const kembaliEl    = document.getElementById('kembali');
const qrisTotalEl  = document.getElementById('qrisTotal');

function fmt(v) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v));
}

function setMetode(metode) {
    document.getElementById('metodePembayaran').value = metode;
    const isTunai = metode === 'Tunai';

    // Toggle button styles
    const btnTunai = document.getElementById('btnTunai');
    const btnQris  = document.getElementById('btnQris');
    btnTunai.style.background = isTunai ? 'var(--accent)' : '#fff';
    btnTunai.style.color      = isTunai ? '#fff' : 'var(--text-muted)';
    btnTunai.style.borderColor = isTunai ? 'var(--accent)' : 'var(--border-color)';
    btnQris.style.background  = !isTunai ? 'var(--accent)' : '#fff';
    btnQris.style.color       = !isTunai ? '#fff' : 'var(--text-muted)';
    btnQris.style.borderColor = !isTunai ? 'var(--accent)' : 'var(--border-color)';

    // Toggle sections
    document.getElementById('sectionTunai').style.display = isTunai ? '' : 'none';
    document.getElementById('sectionQris').style.display  = isTunai ? 'none' : '';

    // Toggle required on bayar input
    if (bayarInput) bayarInput.required = isTunai;

    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const sel = row.querySelector('.product-select');
        const opt = sel.options[sel.selectedIndex];
        const harga = Number(opt?.dataset.harga || 0);
        const stok  = Number(opt?.dataset.stok  || 0);
        const qty   = Number(row.querySelector('.qty-input').value || 0);
        const sub   = harga * qty;

        row.querySelector('.harga-text').textContent = fmt(harga);
        row.querySelector('.stok-text').innerHTML = `<span class="badge ${stok <= 5 && stok > 0 ? 'badge-amber-soft' : stok === 0 ? 'badge-danger-soft' : 'badge-secondary-soft'}">${stok}</span>`;
        row.querySelector('.subtotal-text').textContent = fmt(sub);
        row.querySelector('.qty-input').max = stok || 9999;
        total += sub;
    });

    grandTotalEl.textContent = fmt(total);

    // Sync QRIS display
    if (qrisTotalEl) qrisTotalEl.textContent = fmt(total);

    // Tunai change calculation
    if (bayarInput) {
        const bayar   = Number(bayarInput.value || 0);
        const kembali = bayar - total;
        kembaliEl.textContent = fmt(Math.max(kembali, 0));
        kembaliEl.style.color = kembali < 0 ? 'var(--color-rose)' : 'var(--color-green)';
    }
}

function bindRow(row) {
    row.querySelector('.product-select').addEventListener('change', hitungTotal);
    row.querySelector('.qty-input').addEventListener('input', hitungTotal);
    row.querySelector('.btn-remove').addEventListener('click', () => {
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
            hitungTotal();
        }
    });
}

document.getElementById('btnTambahItem')?.addEventListener('click', () => {
    const first  = document.querySelector('.item-row');
    const newRow = first.cloneNode(true);
    newRow.querySelector('.product-select').value = '';
    newRow.querySelector('.qty-input').value = 1;
    newRow.querySelector('.harga-text').textContent = 'Rp 0';
    newRow.querySelector('.stok-text').innerHTML = '<span class="badge badge-secondary-soft">0</span>';
    newRow.querySelector('.subtotal-text').textContent = 'Rp 0';
    itemRows.appendChild(newRow);
    bindRow(newRow);
    newRow.querySelector('.product-select').focus();
});

bayarInput?.addEventListener('input', hitungTotal);
document.querySelectorAll('.item-row').forEach(bindRow);
hitungTotal();
</script>
