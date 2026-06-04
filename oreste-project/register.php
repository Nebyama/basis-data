<?php
session_start();
if (!empty($_SESSION['is_login'])) {
    header('Location: dashboard.php');
    exit;
}
require 'service/database.php';
$error = '';
$success = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $check = mysqli_prepare($db, 'SELECT id FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 's', $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'Username sudah terdaftar, silakan pilih yang lain.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($db, 'INSERT INTO users (username, password) VALUES (?, ?)');
            mysqli_stmt_bind_param($insert, 'ss', $username, $passwordHash);
            if (mysqli_stmt_execute($insert)) {
                $success = 'Pendaftaran berhasil. Silakan login.';
                $username = '';
            } else {
                $error = 'Terjadi kesalahan saat menyimpan data.';
            }
            mysqli_stmt_close($insert);
        }
        mysqli_stmt_close($check);
    }
}
$pageTitle = 'Register - ORESTE';
$active = 'register';
include 'layout/header.php';
?>
<section class="section">
    <div class="container">
        <div class="page-card card" style="max-width: 520px; margin: 0 auto;">
            <h2>Daftar Akun</h2>
            <p>Buat akun untuk melihat hasil analisis ORESTE.</p>
            <?php if ($error): ?>
                <div class="alert-card card" style="background: #FFE7E0; color: #7D2A18;"><?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="alert-card card" style="background: #E6FFEE; color: #2A5F3F;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="<?= htmlspecialchars($username) ?>" placeholder="Pilih username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Buat password">
                </div>
                <button type="submit">Daftar Sekarang</button>
            </form>
            <p style="margin-top: 1rem; color: var(--text-medium);">Sudah punya akun? <a href="login.php">Login sekarang</a>.</p>
        </div>
    </div>
</section>
<?php include 'layout/footer.php';
