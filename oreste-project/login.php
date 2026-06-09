<?php
session_start();
if (!empty($_SESSION['is_login'])) {
    header('Location: dashboard.php');
    exit;
}
require 'service/database.php';
$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($db, 'SELECT id, password FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId, $passwordHash);
        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $passwordHash)) {
                $_SESSION['is_login'] = true;
                $_SESSION['username'] = $username;
                header('Location: dashboard.php');
                exit;
            }
        }
        $error = 'Username atau password salah.';
        mysqli_stmt_close($stmt);
    }
}
$pageTitle = 'Login - ORESTE';
$active = 'login';
include 'layout/header.php';
?>
<section class="section">
    <div class="container">
        <div class="page-card card" style="max-width: 520px; margin: 0 auto;">
            <h2>Login Pengguna</h2>
            <p>Masuk untuk melihat dashboard ORESTE dan analisis ranking.</p>
            <?php if ($error): ?>
                <div class="alert-card card" style="background: #FFE7E0; color: #7D2A18;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="<?= htmlspecialchars($username) ?>" placeholder="Masukkan username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Masukkan password">
                </div>
                <button type="submit">Login Sekarang</button>
            </form>
            <p style="margin-top: 1rem; color: var(--text-medium);">Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
        </div>
    </div>
</section>
<?php include 'layout/footer.php';
