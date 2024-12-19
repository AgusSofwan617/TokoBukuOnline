<?php
session_start();
require '../server/config/db.php';

// Jika user sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: ../client/index.php");
    exit();
}

// Variabel untuk pesan error
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (!empty($username) && !empty($password)) {
        $query = "SELECT id, password_hash FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../client/index.php");
                exit();
            } else {
                $errorMessage = "Password yang Anda masukkan salah.";
            }
        } else {
            $errorMessage = "Username tidak ditemukan.";
        }
    } else {
        $errorMessage = "Mohon isi semua kolom.";
    }
}

// Isi konten login
ob_start();
?>
<div class="container">
    <h1>Login</h1>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
<?php
$content = ob_get_clean();
include '../shared/template.php';
?>
