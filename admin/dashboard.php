<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .button-group {
            margin-top: 30px;
        }
        .button-group a {
            display: inline-block;
            margin: 10px;
            padding: 15px 25px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .button-group a:hover {
            background-color: #0056b3;
        }
        .logout {
            background-color: #dc3545;
        }
        .logout:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang, Admin</h1>
        <p>Gunakan menu di bawah ini untuk mengelola aplikasi toko buku online Anda.</p>

        <div class="button-group">
            <a href="manage_books.php">Kelola Buku</a>
            <a href="orders_list.php">Daftar Pesanan</a>
            <a href="view_users.php">Kelola Pengguna</a>
            <a href="../shared/logout.php" class="logout">Logout</a>
        </div>
    </div>
</body>
</html>
