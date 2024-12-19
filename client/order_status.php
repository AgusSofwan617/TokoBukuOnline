<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$pageTitle = "Status Pesanan";
$userId = $_SESSION['user_id'];

// Ambil daftar pesanan untuk user
$query = "
    SELECT id, total, status, created_at
    FROM orders
    WHERE user_id = ? -- Pastikan kolom ini sesuai dengan tabel Anda
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error dalam query SQL: " . $conn->error); // Debug jika query gagal
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Buat konten untuk halaman
ob_start();
?>

<h1>Status Pesanan Anda</h1>
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
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
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td>Rp <?= number_format($row['total'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a href="view_order.php?order_id=<?= $row['id'] ?>">Lihat Detail</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Belum ada pesanan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="nav-buttons">
    <a href="shop.php" class="button">Lanjutkan Belanja</a>
    <a href="cart.php" class="button">Lihat Keranjang</a>
</div>

<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
