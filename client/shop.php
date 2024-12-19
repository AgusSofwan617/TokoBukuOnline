<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$pageTitle = "Daftar Buku";

// Ambil daftar buku dari database
$query = "SELECT id, title, author, price, image FROM books";
$result = $conn->query($query);

// Konten utama
ob_start();
?>
<h1>Daftar Buku</h1>
<div class="book-list">
    <?php while ($book = $result->fetch_assoc()): ?>
        <div class="book-item">
            <img src="../<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
            <h3><?= htmlspecialchars($book['title']) ?></h3>
            <p><strong>Pengarang:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Harga:</strong> Rp <?= number_format($book['price'], 2, ',', '.') ?></p>
            <a href="add_to_cart.php?book_id=<?= $book['id'] ?>" class="button">Tambah ke Keranjang</a>
        </div>
    <?php endwhile; ?>
</div>

<!-- Tombol Navigasi -->
<div class="nav-buttons">
    <a href="cart.php" class="button">Lihat Keranjang</a>
    <a href="orders_list.php" class="button">Daftar Pesanan</a>
    <a href="index.php" class="button">Kembali ke Beranda</a>
</div>
<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
