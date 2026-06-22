<?php
// Model untuk mengelola data supplier
class Supplier
{
    private PDO $conn;
    private string $table = "supplier";

    // Menyimpan koneksi database untuk operasi supplier
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Mengambil semua data supplier atau hasil pencarian
    public function getAll(string $keyword = ""): array
    {
        if ($keyword !== "") {
            $sql = "SELECT * FROM {$this->table}
                    WHERE nama_supplier LIKE :keyword
                       OR telepon LIKE :keyword
                       OR alamat LIKE :keyword
                    ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':keyword' => "%{$keyword}%"]);
            return $stmt->fetchAll();
        }

        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Mengambil satu supplier berdasarkan ID
    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Menyimpan supplier baru
    public function insert(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (nama_supplier, telepon, alamat)
                VALUES (:nama_supplier, :telepon, :alamat)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nama_supplier' => $data['nama_supplier'],
            ':telepon' => $data['telepon'],
            ':alamat' => $data['alamat'],
        ]);
    }

    // Memperbarui data supplier
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nama_supplier = :nama_supplier, telepon = :telepon, alamat = :alamat
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nama_supplier' => $data['nama_supplier'],
            ':telepon' => $data['telepon'],
            ':alamat' => $data['alamat'],
        ]);
    }

    // Menghapus supplier berdasarkan ID
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Menghitung total seluruh supplier
    public function countAll(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }
}
