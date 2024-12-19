<?php
session_start();
require_once '../server/config/db.php';

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Periksa apakah ID pesanan diberikan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID pesanan tidak valid.");
}

$order_id = $_GET['id'];

// Periksa apakah form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];

    // Validasi status
    $valid_status = ['pending', 'processed', 'shipped', 'completed', 'canceled'];
    if (!in_array($new_status, $valid_status)) {
        die("Status tidak valid.");
    }

    // Perbarui status di database
    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        header("Location: manage_orders.php");
        exit();
    } else {
        die("Gagal memperbarui status pesanan: " . $conn->error);
    }
}

// Ambil data pesanan untuk ditampilkan
$order_query = "SELECT id, status FROM orders WHERE id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbarui Status Pesanan</title>
</head>
<body>
    <h1>Perbarui Status Pesanan</h1>
    <form action="" method="POST">
        <p><strong>ID Pesanan:</strong> <?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Status Saat Ini:</strong> <?= ucfirst($order['status']) ?></p>
        <label for="status">Status Baru:</label>
        <select name="status" id="status">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processed" <?= $order['status'] === 'processed' ? 'selected' : '' ?>>Processed</option>
            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="canceled" <?= $order['status'] === 'canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>
        <br><br>
        <button type="submit">Perbarui Status</button>
    </form>
</body>
</html>
