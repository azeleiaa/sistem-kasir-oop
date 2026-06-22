<?php
// Menyiapkan ringkasan dashboard dari berbagai model
$totalKategori  = $kategoriModel->countAll();
$totalSupplier  = $supplierModel->countAll();
$totalProduk    = $produkModel->countAll();
$stokRendah     = $produkModel->countLowStock();
$totalTransaksi = $penjualanModel->countAll();
$totalPendapatan = $penjualanModel->totalPendapatan();
$penjualanTerbaru = $penjualanModel->latest(5);
$salesChart = $penjualanModel->getSalesLast7Days();

$chartLabels = array_map(fn($r) => date('d M', strtotime($r['hari'])), $salesChart);
$chartTotals = array_map(fn($r) => (float) $r['total'], $salesChart);
$chartCounts = array_map(fn($r) => (int) $r['jumlah'], $salesChart);
?>

<div class="page-header">
    <div>
        <h3><i class="bi bi-grid-1x2 me-2 text-indigo" style="color:var(--accent)"></i>Dashboard</h3>
        <p class="subtitle">Selamat datang kembali! Berikut ringkasan hari ini.</p>
    </div>
    <a href="index.php?page=form_penjualan" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Penjualan
    </a>
</div>

<!-- Kartu ringkasan utama -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-indigo">
            <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Kategori</div>
                <div class="stat-card-value"><?= $totalKategori ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-sky">
            <div class="stat-card-icon"><i class="bi bi-truck"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Supplier</div>
                <div class="stat-card-value"><?= $totalSupplier ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-purple">
            <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Produk</div>
                <div class="stat-card-value"><?= $totalProduk ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-rose">
            <div class="stat-card-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Produk Kritis</div>
                <div class="stat-card-value"><?= $stokRendah ?></div>
                <div class="stat-card-sub"><?= $stokRendah === 0 ? 'Semua aman ✓' : $stokRendah . ' produk stok ≤5' ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-amber">
            <div class="stat-card-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Transaksi</div>
                <div class="stat-card-value"><?= $totalTransaksi ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card stat-green">
            <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-label">Pendapatan</div>
                <div class="stat-card-value" style="font-size:clamp(13px,1.4vw,18px);line-height:1.3"><?= rupiah($totalPendapatan) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik dan transaksi terbaru -->
<div class="row g-3">
    <!-- Grafik penjualan -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-line me-2" style="color:var(--accent)"></i>Penjualan 7 Hari Terakhir</span>
                <span class="badge badge-indigo-soft">Per Hari</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Transaksi terbaru -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2" style="color:var(--accent)"></i>Transaksi Terbaru</span>
                <a href="index.php?page=data_penjualan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($penjualanTerbaru)) : ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($penjualanTerbaru as $row) : ?>
                            <li class="list-group-item px-4 py-3 border-0" style="border-bottom: 1px solid var(--border-color) !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:38px;height:38px;border-radius:10px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:16px;flex-shrink:0">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="flex-1 min-w-0" style="flex:1;min-width:0">
                                        <div class="fw-600" style="font-size:13px;font-weight:600;color:var(--text-main)"><?= e($row['no_faktur']) ?></div>
                                        <div style="font-size:12px;color:var(--text-muted)"><?= e($row['nama_pelanggan']) ?> &middot; <?= date('d M', strtotime($row['tanggal'])) ?></div>
                                    </div>
                                    <div class="text-end" style="flex-shrink:0">
                                        <div style="font-size:13.5px;font-weight:700;color:var(--color-green)"><?= rupiah($row['total']) ?></div>
                                        <span class="badge badge-success-soft"><?= e($row['status']) ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <div class="text-center py-5" style="color:var(--text-muted)">
                        <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.4"></i>
                        <p class="mt-2 mb-0" style="font-size:13px">Belum ada transaksi.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?= json_encode($chartLabels) ?>;
    const totals = <?= json_encode($chartTotals) ?>;

    const ctx = document.getElementById('salesChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: totals,
                fill: true,
                backgroundColor: gradient,
                borderColor: '#6366f1',
                borderWidth: 2.5,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#94a3b8',
                    bodyColor: '#fff',
                    borderColor: 'rgba(99,102,241,.3)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,.05)', drawBorder: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#94a3b8',
                        callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                    }
                }
            }
        }
    });
})();
</script>
