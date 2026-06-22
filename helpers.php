<?php
// Escape output agar aman ditampilkan di HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Format angka menjadi tampilan rupiah
function rupiah($value): string
{
    return "Rp " . number_format((float) $value, 0, ',', '.');
}

// Simpan pesan sementara ke session untuk ditampilkan setelah redirect
function setFlash(string $type, string $message): void
{
    $_SESSION[$type] = $message;
}

// Pindahkan pengguna ke URL tertentu lalu hentikan eksekusi
function redirect(string $url): void
{
    header("Location: " . $url);
    exit;
}
