<?php
$host = 'localhost';
$db = 'toko_buku_online';
$user = 'root'; // Sesuaikan dengan user MySQL Anda
$password = ''; // Sesuaikan dengan password MySQL Anda

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
