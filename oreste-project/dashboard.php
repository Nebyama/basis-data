<?php
session_start();
if (empty($_SESSION['is_login'])) {
    header('Location: login.php');
    exit;
}
require 'service/database.php';
$page = $_GET['page'] ?? 'ranking';
$allowedPages = ['bobot', 'alternatif', 'besson', 'normalisasi', 'distance', 'ranking'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'ranking';
}
$pageTitle = 'Dashboard ORESTE';
$active = 'dashboard';
include 'layout/header.php';

$top3Query = mysqli_query($db, 'SELECT r.ranking, a.nama, r.akumulasi FROM ranking_oreste r JOIN alternatif a ON r.alternatif_id = a.id ORDER BY r.ranking ASC LIMIT 3');
$top3 = mysqli_fetch_all($top3Query, MYSQLI_ASSOC);
$totalAlternatifQuery = mysqli_query($db, 'SELECT COUNT(*) AS total FROM alternatif');
$totalAlternatif = mysqli_fetch_assoc($totalAlternatifQuery)['total'] ?? 0;
?>
<section class="section">
    <div class="container dashboard-grid">
        <aside class="page-sidebar">
            <h3>Menu Dashboard</h3>
            <nav>
                <a href="dashboard.php?page=ranking" class="<?= $page === 'ranking' ? 'active' : '' ?>">Ranking Utama</a>
                <a href="dashboard.php?page=bobot" class="<?= $page === 'bobot' ? 'active' : '' ?>">Bobot Kriteria</a>
                <a href="dashboard.php?page=alternatif" class="<?= $page === 'alternatif' ? 'active' : '' ?>">Nilai Alternatif</a>
                <a href="dashboard.php?page=besson" class="<?= $page === 'besson' ? 'active' : '' ?>">Besson Rank</a>
                <a href="dashboard.php?page=normalisasi" class="<?= $page === 'normalisasi' ? 'active' : '' ?>">Normalisasi</a>
                <a href="dashboard.php?page=distance" class="<?= $page === 'distance' ? 'active' : '' ?>">Distance Score</a>
            </nav>
        </aside>
        <div class="page-content">
            <div class="page-card card">
                <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?>.</h2>
                <p>Ini adalah halaman dashboard hasil ORESTE. Silakan pilih tampilan data di menu.</p>
                <div class="grid stats-grid" style="margin-top: 1.5rem;">
                    <div class="card">
                        <strong>Top 3 Ranking</strong>
                        <?php foreach ($top3 as $item): ?>
                            <p>#<?= $item['ranking'] ?> - <?= htmlspecialchars($item['nama']) ?> (<?= number_format($item['akumulasi'], 3) ?>)</p>
                        <?php endforeach; ?>
                    </div>
                    <div class="card">
                        <strong>Alternatif</strong>
                        <h3><?= $totalAlternatif ?> Remaja</h3>
                    </div>
                    <div class="card">
                        <strong>Metode</strong>
                        <h3>ORESTE + Besson Rank</h3>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
        </div>
    </div>
</section>
<?php include 'layout/footer.php';
