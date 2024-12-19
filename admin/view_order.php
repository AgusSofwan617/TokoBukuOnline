<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Ambil ID pesanan dari URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($orderId <= 0) {
    die("ID pesanan tidak valid.");
}

// Ambil informasi pesanan
$queryOrder = "
    SELECT o.*, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
";
$stmtOrder = $conn->prepare($queryOrder);
$stmtOrder->bind_param("i", $orderId);
$stmtOrder->execute();
$orderResult = $stmtOrder->get_result();

if ($orderResult->num_rows === 0) {
    die("Pesanan tidak ditemukan.");
}

$order = $orderResult->fetch_assoc();

// Ambil detail item pesanan
$queryItems = "
    SELECT oi.*, b.title, b.price 
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
";
$stmtItems = $conn->prepare($queryItems);
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
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
    <h1>Detail Pesanan #<?= $order['id'] ?></h1>

    <h2>Informasi Pesanan</h2>
    <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($order['username']) ?></p>
    <p><strong>Total:</strong> Rp <?= number_format($order['total'], 2, ',', '.') ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Tanggal Pesanan:</strong> <?= $order['created_at'] ?></p>

    <h2>Item Pesanan</h2>
    <table>
        <tr>
            <th>Judul Buku</th>
            <th>Harga Satuan</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
        <?php while ($item = $itemsResult->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($item['title']) ?></td>
            <td>Rp <?= number_format($item['price'], 2, ',', '.') ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>Rp <?= number_format($item['quantity'] * $item['price'], 2, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="button-group">
        <a href="orders_list.php">Kembali ke Daftar Pesanan</a>
        <a href="../admin/dashboard.php">Kembali ke Dashboard Admin</a>
    </div>
</body>
</html>
