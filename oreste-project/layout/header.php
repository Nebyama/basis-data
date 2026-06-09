<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$username = $_SESSION['username'] ?? null;
$pageTitle = $pageTitle ?? 'ORESTE Research';
$active = $active ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-<?= htmlspecialchars($active) ?>">
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="brand">ORESTE Research</a>
        <nav class="site-nav">
            <a href="index.php" class="nav-link <?= $active === 'home' ? 'active' : '' ?>">Home</a>
            <?php if ($username): ?>
                <a href="dashboard.php" class="nav-link <?= $active === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
                <a href="logout.php" class="button button-secondary">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link <?= $active === 'login' ? 'active' : '' ?>">Login</a>
                <a href="register.php" class="button">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="site-main">
