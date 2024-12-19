<?php
require '../server/config/db.php'; // Pastikan koneksi database benar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validasi data
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Semua bidang wajib diisi.";
    } else {
        // Periksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password dan simpan ke database
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $passwordHash, $role);

            if ($stmt->execute()) {
                header("Location: login.php?message=success");
                exit();
            } else {
                $error = "Gagal mendaftarkan pengguna. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
</head>
<body>
    <h1>Registrasi</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Role:</label><br>
        <select name="role" required>
            <option value="client">Client</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>
