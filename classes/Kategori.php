<?php
// Model untuk mengelola data kategori di database
class Kategori
{
    private PDO $conn;
    private string $table = "kategori";

    // Menyimpan koneksi database agar bisa dipakai di semua method
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Mengambil semua data kategori atau memfilter berdasarkan kata kunci
    public function getAll(string $keyword = ""): array
    {
        // Jika ada kata kunci, cari nama kategori atau deskripsi yang cocok
        if ($keyword !== "") {
            $sql = "SELECT * FROM {$this->table}
                    WHERE nama_kategori LIKE :keyword OR deskripsi LIKE :keyword
                    ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':keyword' => "%{$keyword}%"]);
            return $stmt->fetchAll();
        }

        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Mengambil satu data kategori berdasarkan ID
    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Menambahkan data kategori baru ke tabel
    public function insert(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (nama_kategori, deskripsi)
                VALUES (:nama_kategori, :deskripsi)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nama_kategori' => $data['nama_kategori'],
            ':deskripsi' => $data['deskripsi'],
        ]);
    }

    // Memperbarui data kategori yang sudah ada
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET nama_kategori = :nama_kategori, deskripsi = :deskripsi
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nama_kategori' => $data['nama_kategori'],
            ':deskripsi' => $data['deskripsi'],
        ]);
    }

    // Menghapus kategori berdasarkan ID
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Menghitung total seluruh data kategori
    public function countAll(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }
}
