<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$pageTitle = "Belanja Buku";

// Tambahkan buku ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $userId = $_SESSION['user_id'];

    // Periksa apakah buku sudah ada di keranjang
    $checkCart = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND book_id = ?");
    $checkCart->bind_param("ii", $userId, $bookId);
    $checkCart->execute();
    $result = $checkCart->get_result();

    if ($result->num_rows > 0) {
        // Jika sudah ada, tingkatkan jumlah
        $updateCart = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?");
        $updateCart->bind_param("ii", $userId, $bookId);
        $updateCart->execute();
    } else {
        // Jika belum ada, tambahkan ke keranjang
        $insertCart = $conn->prepare("INSERT INTO cart (user_id, book_id) VALUES (?, ?)");
        $insertCart->bind_param("ii", $userId, $bookId);
        $insertCart->execute();
    }
    $successMessage = "Buku berhasil ditambahkan ke keranjang!";
}

// Ambil daftar buku
$result = $conn->query("SELECT * FROM books");

// Konten utama
ob_start();
?>
<style>
    .book-container {
        border: 1px solid #ddd;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .book-container h3 {
        margin: 0 0 10px 0;
    }

    .book-container img {
        max-width: 200px;
        height: auto;
        display: block;
        margin: 10px 0;
    }

    .button {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
    }

    .button:hover {
        background-color: #218838;
    }

    .button-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .success-message {
        color: green;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>

<?php if (isset($successMessage)): ?>
    <p class="success-message"><?= $successMessage ?></p>
<?php endif; ?>

<h2>Daftar Buku</h2>
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="book-container">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p>Pengarang: <?= htmlspecialchars($row['author']) ?></p>
        <p>Harga: Rp <?= number_format($row['price'], 2, ',', '.') ?></p>
        <p><?= htmlspecialchars($row['description'] ?? 'Tidak ada deskripsi') ?></p>
        <?php if (!empty($row['image'])): ?>
            <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Gambar Buku">
        <?php else: ?>
            <p><i>Tidak ada gambar</i></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
            <button type="submit" class="button">Tambahkan ke Keranjang</button>
        </form>
    </div>
<?php endwhile; ?>

<div class="button-group">
    <a href="shop.php" class="button">Lanjutkan Belanja</a>
    <a href="cart.php" class="button">Lihat Keranjang</a>
</div>

<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
