<?php
session_start();
require '../server/config/db.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

// Ambil ID buku dari URL
$bookId = $_GET['id'] ?? null;

if (!$bookId) {
    header("Location: manage_books.php");
    exit();
}

// Ambil data buku dari database
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_books.php");
    exit();
}

$book = $result->fetch_assoc();

// Update data buku jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $newImage = $book['image'];

    // Jika ada gambar baru yang diunggah
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = '../uploads/' . $imageName;

        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $newImage = 'uploads/' . $imageName;
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    }

    // Validasi input
    if (empty($title) || empty($author) || empty($price)) {
        $error = "Semua bidang wajib diisi.";
    } else {
        // Perbarui data buku di database
        $updateStmt = $conn->prepare("UPDATE books SET title = ?, author = ?, price = ?, description = ?, image = ? WHERE id = ?");
        $updateStmt->bind_param("ssdssi", $title, $author, $price, $description, $newImage, $bookId);

        if ($updateStmt->execute()) {
            $success = "Buku berhasil diperbarui.";
            // Perbarui data buku di variabel $book untuk ditampilkan kembali
            $book['title'] = $title;
            $book['author'] = $author;
            $book['price'] = $price;
            $book['description'] = $description;
            $book['image'] = $newImage;
        } else {
            $error = "Gagal memperbarui buku: " . $updateStmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
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
            margin: 20px 0;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
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
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-group a:hover {
            background-color: #0056b3;
        }
        .image-preview img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .message {
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
        <h1>Edit Buku</h1>
        <?php if (isset($success)): ?>
            <p class="message"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="title">Judul:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']) ?>" required>

            <label for="author">Pengarang:</label>
            <input type="text" name="author" id="author" value="<?= htmlspecialchars($book['author']) ?>" required>

            <label for="price">Harga:</label>
            <input type="number" name="price" id="price" value="<?= htmlspecialchars($book['price']) ?>" step="0.01" required>

            <label for="description">Deskripsi:</label>
            <textarea name="description" id="description"><?= htmlspecialchars($book['description']) ?></textarea>

            <label for="image">Gambar Buku:</label>
            <div class="image-preview">
                <?php if (!empty($book['image'])): ?>
                    <img src="../<?= htmlspecialchars($book['image']) ?>" alt="Gambar Buku">
                <?php else: ?>
                    <p><i>Tidak ada gambar</i></p>
                <?php endif; ?>
            </div>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit">Perbarui Buku</button>
        </form>

        <div class="button-group">
            <a href="manage_books.php">Kembali ke Kelola Buku</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="../shared/logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
