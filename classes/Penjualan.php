<?php
class Penjualan
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function generateNoFaktur(): string
    {
        $prefix = "TRX" . date('Ymd') . "-";
        $stmt = $this->conn->prepare(
            "SELECT no_faktur FROM penjualan
             WHERE no_faktur LIKE :prefix
             ORDER BY no_faktur DESC LIMIT 1"
        );
        $stmt->execute([':prefix' => $prefix . '%']);
        $last = $stmt->fetchColumn();
        $next = $last ? ((int) substr($last, -3) + 1) : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function create(array $data, array $items): int
    {
        $this->conn->beginTransaction();

        try {
            $cleanItems = [];
            $total = 0;

            foreach ($items as $item) {
                $idProduk = (int) $item['id_produk'];
                $qty = (int) $item['qty'];

                if ($idProduk <= 0 || $qty <= 0) {
                    throw new Exception("Produk dan jumlah wajib diisi dengan benar.");
                }

                $stmtProduk = $this->conn->prepare(
                    "SELECT id, nama_produk, harga_jual, stok
                     FROM produk
                     WHERE id = :id AND status = 'Aktif'
                     FOR UPDATE"
                );
                $stmtProduk->execute([':id' => $idProduk]);
                $produk = $stmtProduk->fetch();

                if (!$produk) {
                    throw new Exception("Produk tidak ditemukan atau tidak aktif.");
                }

                if ((int) $produk['stok'] < $qty) {
                    throw new Exception("Stok produk {$produk['nama_produk']} tidak mencukupi.");
                }

                $harga = (float) $produk['harga_jual'];
                $subtotal = $harga * $qty;
                $total += $subtotal;

                $cleanItems[] = [
                    'id_produk' => $idProduk,
                    'harga' => $harga,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($cleanItems)) {
                throw new Exception("Minimal pilih satu produk.");
            }

            $bayar = (float) $data['bayar'];
            if ($bayar < $total) {
                throw new Exception("Jumlah bayar kurang dari total belanja.");
            }

            $noFaktur = $this->generateNoFaktur();
            $metode   = $data['metode_pembayaran'] ?? 'Tunai';
            // kalau QRIS, bayar otomatis sama dengan total (tidak ada kembalian)
            $bayar    = $metode === 'QRIS' ? $total : (float) $data['bayar'];
            $kembali  = max($bayar - $total, 0);

            $stmtJual = $this->conn->prepare(
                "INSERT INTO penjualan
                 (no_faktur, tanggal, nama_pelanggan, metode_pembayaran, total, bayar, kembali, status)
                 VALUES
                 (:no_faktur, :tanggal, :nama_pelanggan, :metode_pembayaran, :total, :bayar, :kembali, 'Selesai')"
            );
            $stmtJual->execute([
                ':no_faktur'          => $noFaktur,
                ':tanggal'            => $data['tanggal'],
                ':nama_pelanggan'     => $data['nama_pelanggan'],
                ':metode_pembayaran'  => $data['metode_pembayaran'] ?? 'Tunai',
                ':total'              => $total,
                ':bayar'              => $bayar,
                ':kembali'            => $kembali,
            ]);

            $idPenjualan = (int) $this->conn->lastInsertId();
            $stmtDetail = $this->conn->prepare(
                "INSERT INTO detail_penjualan
                 (id_penjualan, id_produk, harga, qty, subtotal)
                 VALUES
                 (:id_penjualan, :id_produk, :harga, :qty, :subtotal)"
            );
            $stmtStok = $this->conn->prepare(
                "UPDATE produk SET stok = stok - :qty WHERE id = :id_produk"
            );

            foreach ($cleanItems as $item) {
                $stmtDetail->execute([
                    ':id_penjualan' => $idPenjualan,
                    ':id_produk' => $item['id_produk'],
                    ':harga' => $item['harga'],
                    ':qty' => $item['qty'],
                    ':subtotal' => $item['subtotal'],
                ]);
                $stmtStok->execute([
                    ':qty' => $item['qty'],
                    ':id_produk' => $item['id_produk'],
                ]);
            }

            $this->conn->commit();
            return $idPenjualan;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getAll(string $keyword = "", string $tanggalAwal = "", string $tanggalAkhir = ""): array
    {
        $sql = "SELECT pj.*, COALESCE(dt.jumlah_item, 0) AS jumlah_item
                FROM penjualan pj
                LEFT JOIN (
                    SELECT id_penjualan, SUM(qty) AS jumlah_item
                    FROM detail_penjualan
                    GROUP BY id_penjualan
                ) dt ON pj.id = dt.id_penjualan
                WHERE 1 = 1";
        $params = [];

        if ($keyword !== "") {
            $sql .= " AND (pj.no_faktur LIKE :keyword OR pj.nama_pelanggan LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }

        if ($tanggalAwal !== "") {
            $sql .= " AND pj.tanggal >= :tanggal_awal";
            $params[':tanggal_awal'] = $tanggalAwal;
        }

        if ($tanggalAkhir !== "") {
            $sql .= " AND pj.tanggal <= :tanggal_akhir";
            $params[':tanggal_akhir'] = $tanggalAkhir;
        }

        $sql .= " ORDER BY pj.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM penjualan WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getDetail(int $idPenjualan): array
    {
        $sql = "SELECT dp.*, p.kode_produk, p.nama_produk
                FROM detail_penjualan dp
                JOIN produk p ON dp.id_produk = p.id
                WHERE dp.id_penjualan = :id_penjualan
                ORDER BY dp.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_penjualan' => $idPenjualan]);
        return $stmt->fetchAll();
    }

    public function getRingkasan(string $keyword = "", string $tanggalAwal = "", string $tanggalAkhir = ""): array
    {
        $sql = "SELECT COUNT(*) AS jumlah_transaksi, COALESCE(SUM(total), 0) AS total_pendapatan
                FROM penjualan
                WHERE 1 = 1";
        $params = [];

        if ($keyword !== "") {
            $sql .= " AND (no_faktur LIKE :keyword OR nama_pelanggan LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }

        if ($tanggalAwal !== "") {
            $sql .= " AND tanggal >= :tanggal_awal";
            $params[':tanggal_awal'] = $tanggalAwal;
        }

        if ($tanggalAkhir !== "") {
            $sql .= " AND tanggal <= :tanggal_akhir";
            $params[':tanggal_akhir'] = $tanggalAkhir;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function countAll(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM penjualan");
        return (int) $stmt->fetchColumn();
    }

    public function totalPendapatan(): float
    {
        $stmt = $this->conn->query("SELECT COALESCE(SUM(total), 0) FROM penjualan");
        return (float) $stmt->fetchColumn();
    }

    public function latest(int $limit = 5): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM penjualan ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSalesLast7Days(): array
    {
        $sql = "SELECT
                    DATE(tanggal) AS hari,
                    COUNT(*) AS jumlah,
                    COALESCE(SUM(total), 0) AS total
                FROM penjualan
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(tanggal)
                ORDER BY hari ASC";
        $stmt = $this->conn->query($sql);
        $rows = $stmt->fetchAll();

        // Fill all 7 days, including those with no sales
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $result[$date] = ['hari' => $date, 'jumlah' => 0, 'total' => 0];
        }
        foreach ($rows as $row) {
            if (isset($result[$row['hari']])) {
                $result[$row['hari']] = $row;
            }
        }
        return array_values($result);
    }
}
