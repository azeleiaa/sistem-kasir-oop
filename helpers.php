<?php
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value): string
{
    return "Rp " . number_format((float) $value, 0, ',', '.');
}

function setFlash(string $type, string $message): void
{
    $_SESSION[$type] = $message;
}

function redirect(string $url): void
{
    header("Location: " . $url);
    exit;
}
