<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Ambil daftar buku dari database
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .button-group {
            margin-top: 20px;
            text-align: center;
        }
        .button-group a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-group a:hover {
            background-color: #0056b3;
        }
        .add-book {
            background-color: #28a745;
        }
        .add-book:hover {
            background-color: #218838;
        }
        .logout {
            background-color: #dc3545;
        }
        .logout:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kelola Buku</h1>

        <div class="button-group">
            <a href="add_book.php" class="add-book">Tambah Buku Baru</a>
            <a href="dashboard.php">Kembali ke Dashboard</a>
            <a href="../shared/logout.php" class="logout">Logout</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td>Rp <?= number_format($row['price'], 2, ',', '.') ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Gambar Buku" style="max-width: 100px;">
                            <?php else: ?>
                                <i>Tidak ada gambar</i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_book.php?id=<?= $row['id'] ?>">Edit</a> |
                            <a href="delete_book.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus buku ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
