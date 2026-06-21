<?php
class Produk
{
    private PDO $conn;
    private string $table = "produk";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function generateKode(): string
    {
        $prefix = "PRD" . date('ymd');
        $stmt = $this->conn->prepare(
            "SELECT kode_produk FROM {$this->table}
             WHERE kode_produk LIKE :prefix
             ORDER BY kode_produk DESC LIMIT 1"
        );
        $stmt->execute([':prefix' => $prefix . '%']);
        $last = $stmt->fetchColumn();
        $next = $last ? ((int) substr($last, -3) + 1) : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function getAll(string $keyword = "", string $idKategori = ""): array
    {
        $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier
                FROM {$this->table} p
                JOIN kategori k ON p.id_kategori = k.id
                JOIN supplier s ON p.id_supplier = s.id
                WHERE 1 = 1";
        $params = [];

        if ($keyword !== "") {
            $sql .= " AND (p.kode_produk LIKE :keyword
                       OR p.nama_produk LIKE :keyword
                       OR k.nama_kategori LIKE :keyword
                       OR s.nama_supplier LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }

        if ($idKategori !== "") {
            $sql .= " AND p.id_kategori = :id_kategori";
            $params[':id_kategori'] = (int) $idKategori;
        }

        $sql .= " ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOptions(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, kode_produk, nama_produk, harga_jual, stok
             FROM {$this->table}
             WHERE status = 'Aktif' AND stok > 0
             ORDER BY nama_produk ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier
                FROM {$this->table} p
                JOIN kategori k ON p.id_kategori = k.id
                JOIN supplier s ON p.id_supplier = s.id
                WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
                (kode_produk, nama_produk, id_kategori, id_supplier, harga_beli, harga_jual, stok, status)
                VALUES
                (:kode_produk, :nama_produk, :id_kategori, :id_supplier, :harga_beli, :harga_jual, :stok, :status)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':kode_produk' => $data['kode_produk'],
            ':nama_produk' => $data['nama_produk'],
            ':id_kategori' => $data['id_kategori'],
            ':id_supplier' => $data['id_supplier'],
            ':harga_beli' => $data['harga_beli'],
            ':harga_jual' => $data['harga_jual'],
            ':stok' => $data['stok'],
            ':status' => $data['status'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nama_produk = :nama_produk,
                    id_kategori = :id_kategori,
                    id_supplier = :id_supplier,
                    harga_beli = :harga_beli,
                    harga_jual = :harga_jual,
                    stok = :stok,
                    status = :status
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nama_produk' => $data['nama_produk'],
            ':id_kategori' => $data['id_kategori'],
            ':id_supplier' => $data['id_supplier'],
            ':harga_beli' => $data['harga_beli'],
            ':harga_jual' => $data['harga_jual'],
            ':stok' => $data['stok'],
            ':status' => $data['status'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }

    public function countLowStock(int $limit = 5): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE stok <= :limit");
        $stmt->execute([':limit' => $limit]);
        return (int) $stmt->fetchColumn();
    }
}
