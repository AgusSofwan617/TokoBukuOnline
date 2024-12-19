<?php
session_start();
require_once '../server/config/db.php';

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Query untuk mengambil data pesanan
$query = "SELECT o.id AS order_id, 
                 o.status, 
                 o.created_at, 
                 SUM(oi.quantity * oi.price) AS total 
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          GROUP BY o.id, o.status, o.created_at
          ORDER BY o.created_at DESC";

$result = $conn->query($query);

if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .action-buttons a {
            text-decoration: none;
            margin-right: 10px;
            color: blue;
        }
        .action-buttons a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Kelola Pesanan</h1>
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Total</th>
                <th>Status</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contoh Data -->
            <tr>
                <td>1</td>
                <td>Rp 150,000.00</td>
                <td>Pending</td>
                <td>2024-11-28 10:00:00</td>
                <td class="action-buttons">
                    <a href="view_order.php?id=1">Lihat Detail</a>
                    <a href="update_order_status.php?id=1">Perbarui Status</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Rp 350,000.00</td>
                <td>Completed</td>
                <td>2024-11-27 14:30:00</td>
                <td class="action-buttons">
                    <a href="view_order.php?id=2">Lihat Detail</a>
                    <a href="update_order_status.php?id=2">Perbarui Status</a>
                </td>
            </tr>
            <!-- Tambahkan data pesanan lain di sini -->
        </tbody>
    </table>
</body>
</html>
