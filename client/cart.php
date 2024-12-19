<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$pageTitle = "Keranjang Belanja";

// Ambil daftar item di keranjang
$userId = $_SESSION['user_id'];
$query = "
    SELECT c.book_id, b.title, b.price, c.quantity, (b.price * c.quantity) AS subtotal
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Hitung total harga
$total = 0;
$cartItems = [];
while ($item = $result->fetch_assoc()) {
    $cartItems[] = $item;
    $total += $item['subtotal'];
}

// Konten utama
ob_start();
?>
<h1>Keranjang Belanja</h1>

<?php if (empty($cartItems)): ?>
    <p>Keranjang belanja Anda kosong. <a href="shop.php">Lanjutkan Belanja</a></p>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td>Rp <?= number_format($item['price'], 2, ',', '.') ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rp <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                <td>
                    <a href="update_cart.php?book_id=<?= $item['book_id'] ?>" class="button">Ubah</a>
                    <a href="remove_from_cart.php?book_id=<?= $item['book_id'] ?>" class="button delete">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-summary">
        <p><strong>Total:</strong> Rp <?= number_format($total, 2, ',', '.') ?></p>
    </div>

    <!-- Tombol Navigasi -->
    <div class="nav-buttons">
        <a href="shop.php" class="button">Lanjutkan Belanja</a>
        <a href="checkout.php" class="button">Checkout</a>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
