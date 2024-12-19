<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Ambil daftar buku di keranjang
$query = "
    SELECT c.book_id, b.title, b.price, c.quantity, (b.price * c.quantity) AS subtotal
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$cartItems = $stmt->get_result();

// Hitung total harga
$total = 0;
$cartDetails = [];
while ($item = $cartItems->fetch_assoc()) {
    $cartDetails[] = $item;
    $total += $item['subtotal'];
}

// Set judul halaman
$pageTitle = "Checkout";

// Konten halaman
ob_start();
?>
<h1>Checkout</h1>

<h2>Daftar Buku di Keranjang</h2>

<?php if (!empty($cartDetails)): ?>
    <?php foreach ($cartDetails as $item): ?>
        <div class="cart-item">
            <h3><?= htmlspecialchars($item['title']) ?></h3>
            <p><strong>Harga:</strong> Rp <?= number_format($item['price'], 2, ',', '.') ?></p>
            <p><strong>Jumlah:</strong> <?= $item['quantity'] ?></p>
            <p><strong>Subtotal:</strong> Rp <?= number_format($item['subtotal'], 2, ',', '.') ?></p>
        </div>
    <?php endforeach; ?>

    <div class="total">
        <p><strong>Total:</strong> Rp <?= number_format($total, 2, ',', '.') ?></p>
    </div>

    <!-- Tombol Navigasi -->
    <div class="nav-buttons">
        <a href="cart.php" class="button">Kembali ke Keranjang</a>
        <a href="order.php" class="button">Selesaikan Pesanan</a>
    </div>
<?php else: ?>
    <p>Keranjang Anda kosong. <a href="shop.php">Belanja sekarang</a>.</p>
<?php endif; ?>
<?php
$content = ob_get_clean();
include '../shared/template.php';
