<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Cek apakah book_id dan quantity ada di URL
if (isset($_GET['book_id']) && isset($_GET['quantity'])) {
    $bookId = (int) $_GET['book_id'];
    $quantity = (int) $_GET['quantity'];

    // Validasi quantity agar lebih dari 0
    if ($quantity > 0) {
        // Update jumlah buku di keranjang
        $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $userId, $bookId);
        $stmt->execute();

        // Redirect kembali ke halaman keranjang setelah pembaruan
        header("Location: cart.php");
        exit();
    } else {
        // Jika quantity invalid, arahkan kembali ke keranjang
        header("Location: cart.php");
        exit();
    }
} else {
    // Jika book_id atau quantity tidak ada di URL
    echo "Invalid request.";
    exit();
}
?>
