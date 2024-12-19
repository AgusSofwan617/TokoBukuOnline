<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Toko Buku Online') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        header img {
            max-height: 100px;
            position: absolute;
            top: 15px;
            left: 10px;
        }
        header h1 {
            margin: 0;
            line-height: 100px;
            font-size: 1.8em;
        }
        main {
            flex: 1; /* Membuat bagian main memenuhi ruang di antara header dan footer */
            padding: 20px;
            background: #fff;
            max-width: 900px;
            margin: 20px auto;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }
        .nav-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .nav-buttons a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav-buttons a:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
        img.book-image {
            width: 75px;
            height: 100px;
            object-fit: cover; /* Membuat gambar sesuai ukuran */
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="../assets/Gramedia.jpg" alt="Logo Toko Buku">
            <h1><?= htmlspecialchars($pageTitle ?? 'Toko Buku Online') ?></h1>
        </header>

        <main>
            <?php if (isset($content)) : ?>
                <?= $content ?>
            <?php else : ?>
                <p class="error">Konten tidak tersedia.</p>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; 2024 Toko Buku Online. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
