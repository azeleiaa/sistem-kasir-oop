<?php
class Database
{
    private ?PDO $conn = null;

    public function getConnection(): PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        // Ambil dari Environment Variables (Railway) atau fallback ke localhost
        $host     = getenv('MYSQLHOST') ?: "localhost";
        $port     = getenv('MYSQLPORT') ?: "3306";
        $dbname   = getenv('MYSQLDATABASE') ?: "db_kasir_oop";
        $username = getenv('MYSQLUSER') ?: "root";
        $password = getenv('MYSQLPASSWORD') ?: "";

        try {
            $this->conn = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}
