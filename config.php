<?php
$host = 'localhost';
$db   = 'trading_db';
$user = 'root';
$pass = ''; // Sesuaikan jika ada password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

function formatIDR($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}
