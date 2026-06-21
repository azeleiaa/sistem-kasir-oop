<?php
class Supplier
{
    private PDO $conn;
    private string $table = "supplier";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

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

    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

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
}
