<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Ambil daftar pesanan
$query = "
    SELECT o.id, o.total, o.status, o.created_at, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group a {
            display: inline-block;
            margin-right: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-group a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Daftar Pesanan</h1>
    <table>
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Pelanggan</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal Pesanan</th>
            <th>Aksi</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td>Rp <?= number_format($order['total'], 2, ',', '.') ?></td>
            <td><?= ucfirst($order['status']) ?></td>
            <td><?= $order['created_at'] ?></td>
            <td>
                <a href="view_order.php?id=<?= $order['id'] ?>">Lihat Detail</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="button-group">
        <a href="dashboard.php">Kembali ke Dashboard Admin</a>
    </div>
</body>
</html>
