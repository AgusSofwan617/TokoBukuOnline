<?php
session_start();
require '../server/config/db.php';
//require '../shared/template.php';

$pageTitle = "Daftar Pesanan";

// Ambil data pesanan
$userId = $_SESSION['user_id'] ?? 0;
$query = "SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Buat konten halaman
ob_start();
?>
<h2>Daftar Pesanan Anda</h2>
<?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td>Rp <?= number_format($order['total'], 2, ',', '.') ?></td>
                    <td><?= ucfirst(htmlspecialchars($order['status'])) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td><a href="order_status.php?order_id=<?= $order['id'] ?>">Lihat Detail</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada pesanan.</p>
<?php endif; ?>

<div class="nav-buttons">
    <a href="shop.php">Lanjutkan Belanja</a>
    <a href="cart.php">Lihat Keranjang</a>
</div>
<?php
$content = ob_get_clean();

// Tampilkan template
include '../shared/template.php';
