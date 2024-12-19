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
$queryCart = "
    SELECT c.book_id, b.title, b.price, c.quantity, (b.price * c.quantity) AS subtotal
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
";
$stmtCart = $conn->prepare($queryCart);
$stmtCart->bind_param("i", $userId);
$stmtCart->execute();
$cartItems = $stmtCart->get_result();

// Jika keranjang kosong, kembali ke halaman keranjang
if ($cartItems->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

// Tambahkan pesanan ke database
$conn->begin_transaction();
try {
    // Tambahkan ke tabel orders
    $queryOrder = "
        INSERT INTO orders (user_id, total, status, created_at)
        VALUES (?, ?, 'Pending', NOW())
    ";
    $stmtOrder = $conn->prepare($queryOrder);
    $totalPrice = 0;
    while ($item = $cartItems->fetch_assoc()) {
        $totalPrice += $item['subtotal'];
    }
    $stmtOrder->bind_param("id", $userId, $totalPrice);
    $stmtOrder->execute();
    $orderId = $stmtOrder->insert_id;

    // Tambahkan item pesanan ke tabel order_items
    $cartItems->data_seek(0); // Reset pointer
    $queryOrderItems = "
        INSERT INTO order_items (order_id, book_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ";
    $stmtOrderItems = $conn->prepare($queryOrderItems);
    while ($item = $cartItems->fetch_assoc()) {
        $stmtOrderItems->bind_param(
            "iiid",
            $orderId,
            $item['book_id'],
            $item['quantity'],
            $item['price']
        );
        $stmtOrderItems->execute();
    }

    // Kosongkan keranjang
    $queryClearCart = "DELETE FROM cart WHERE user_id = ?";
    $stmtClearCart = $conn->prepare($queryClearCart);
    $stmtClearCart->bind_param("i", $userId);
    $stmtClearCart->execute();

    // Commit transaksi
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan dalam memproses pesanan Anda. Silakan coba lagi.");
}

// Set judul halaman
$pageTitle = "Pesanan Berhasil";

// Konten halaman
ob_start();
?>
<h1>Pesanan Berhasil</h1>

<p>Pesanan Anda telah diterima dengan ID pesanan: <strong><?= htmlspecialchars($orderId) ?></strong>.</p>
<p>Total pesanan: <strong>Rp <?= number_format($totalPrice, 2, ',', '.') ?></strong></p>
<p>Status pesanan Anda saat ini: <strong>Pending</strong>.</p>

<div class="nav-buttons">
    <a href="orders_list.php" class="button">Lihat Daftar Pesanan</a>
    <a href="shop.php" class="button">Lanjutkan Belanja</a>
</div>
<?php
$content = ob_get_clean();
include '../shared/template.php';
