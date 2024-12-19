<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

$message = '';

// Proses jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imagePath = '';

    // Upload gambar jika ada
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $message = "Gagal mengunggah gambar.";
        } else {
            $imagePath = str_replace("../", "", $imagePath); // Simpan path relatif
        }
    }

    // Simpan buku ke database
    $stmt = $conn->prepare("INSERT INTO books (title, author, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $title, $author, $price, $description, $imagePath);

    if ($stmt->execute()) {
        $message = "Buku berhasil ditambahkan!";
    } else {
        $message = "Terjadi kesalahan saat menambahkan buku.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
        .button-group a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            color: #fff;
            background-color: #6c757d;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-group a:hover {
            background-color: #5a6268;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Buku Baru</h1>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'berhasil') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="title">Judul Buku</label>
            <input type="text" name="title" id="title" required>

            <label for="author">Pengarang</label>
            <input type="text" name="author" id="author" required>

            <label for="price">Harga</label>
            <input type="number" name="price" id="price" step="0.01" required>

            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" rows="4"></textarea>

            <label for="image">Gambar Buku</label>
            <input type="file" name="image" id="image">

            <button type="submit">Tambah Buku</button>
        </form>

        <div class="button-group">
            <a href="manage_books.php">Kembali ke Kelola Buku</a>
            <a href="dashboard.php">Kembali ke Dashboard</a>
            <a href="../shared/logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
