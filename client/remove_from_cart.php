<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Cek apakah book_id ada di URL
if (isset($_GET['book_id'])) {
    $bookId = (int) $_GET['book_id'];

    // Hapus item dari keranjang
    $query = "DELETE FROM cart WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $bookId);
    $stmt->execute();

    // Redirect kembali ke halaman keranjang setelah penghapusan
    header("Location: cart.php");
    exit();
} else {
    // Jika book_id tidak ada, tampilkan error
    echo "Invalid request.";
}
?>
