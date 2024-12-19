<?php
session_start();
require '../server/config/db.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../shared/login.php");
    exit();
}

$pageTitle = "Beranda Toko Buku Online";

// Konten utama
ob_start();
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body, html {
        height: 100%;
    }

    .container {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    header, footer {
        flex-shrink: 0;
    }

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        text-align: center;
    }

    .welcome-text h1 {
        font-size: 2em;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }

    .center-logo img {
        height: 200px;
        width: 150px;
        object-fit: contain;
        margin-bottom: 20px;
    }

    .nav-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .nav-buttons a {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        color: white;
        background-color: #007bff;
        border-radius: 5px;
        text-align: center;
    }

    .nav-buttons a:hover {
        background-color: #0056b3;
    }

    .logout-button {
        background-color: #ff4d4d;
    }

    .logout-button:hover {
        background-color: #e60000;
    }
</style>

<main>
    <div class="welcome-text">
        <h1>Selamat Datang di Toko Buku Online</h1>
    </div>
    <div class="center-logo">
        <img src="../assets/Gramedia.jpg" alt="Logo Toko Buku">
    </div>
    <div class="nav-buttons">
        <a href="shop.php" class="button">Belanja Sekarang</a>
        <a href="cart.php" class="button">Lihat Keranjang</a>
        <a href="orders_list.php" class="button">Daftar Pesanan</a>
        <a href="../shared/logout.php" class="logout-button">Logout</a>
    </div>
</main>

<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
