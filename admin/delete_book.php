<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../server/config/db.php';

$id = intval($_GET['id']);
$query = "DELETE FROM books WHERE id = $id";
if ($conn->query($query)) {
    header("Location: manage_books.php");
    exit();
} else {
    echo "Gagal menghapus buku: " . $conn->error;
}
?>
