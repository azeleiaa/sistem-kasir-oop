<?php
// Menyediakan koneksi PDO ke database kasir
class Database
{
    private ?PDO $conn = null;

    // Membuat koneksi database sekali lalu dipakai ulang
    public function getConnection(): PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        $host     = "localhost";
        $port     = "3306";
        $dbname   = "db_kasir_oop";
        $username = "root";
        $password = "";

        try {
            // Konfigurasi koneksi memakai charset utf8mb4 dan mode error exception
            $this->conn = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch (PDOException $e) {
            // Hentikan aplikasi jika koneksi gagal
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}
