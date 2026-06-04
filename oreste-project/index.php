<?php
$pageTitle = 'Landing Page ORESTE Research';
$active = 'home';
include 'layout/header.php';
?>
<section class="hero">
    <div class="container">
        <h1>Di Balik Layar: Dampak Internet Intensif pada Kesehatan Mental Remaja</h1>
        <p>Analisis data ORESTE untuk 40 remaja, lengkap dengan bobot kriteria, Besson Rank, normalisasi, distance score, dan perankingan akhir.</p>
        <a href="login.php" class="hero-button">Lihat Hasil</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Tentang Penelitian</h2>
            <p>Metode ORESTE membantu menyusun prioritas dampak internet terhadap kesehatan mental remaja menggunakan nilai bobot kriteria dan perhitungan jarak.</p>
        </div>
        <div class="grid feature-grid">
            <div class="feature-card card">
                <div class="feature-icon">C1</div>
                <h3>Durasi Penggunaan Internet</h3>
                <p>Menilai intensitas waktu yang dihabiskan remaja di dunia digital setiap hari.</p>
            </div>
            <div class="feature-card card">
                <div class="feature-icon">C2</div>
                <h3>Kondisi Mental</h3>
                <p>Mengukur status psikologis dan kesejahteraan emosional setiap alternatif.</p>
            </div>
            <div class="feature-card card">
                <div class="feature-icon">C3</div>
                <h3>Kualitas Tidur</h3>
                <p>Memetakan pengaruh interaksi internet terhadap pola dan kualitas tidur remaja.</p>
            </div>
            <div class="feature-card card">
                <div class="feature-icon">C4</div>
                <h3>Interaksi Sosial</h3>
                <p>Menilai dampak penggunaan internet pada hubungan sosial dan komunikasi sehari-hari.</p>
            </div>
            <div class="feature-card card">
                <div class="feature-icon">C5</div>
                <h3>Kesadaran Diri</h3>
                <p>Mengevaluasi seberapa sadar remaja terhadap perilaku dan konsekuensi digitalnya.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-title">
            <h2>Statistik Penelitian</h2>
        </div>
        <div class="grid stats-grid">
            <div class="card">
                <strong>40 Alternatif</strong>
                <h3>Remaja 1–40</h3>
            </div>
            <div class="card">
                <strong>5 Kriteria</strong>
                <h3>C1 hingga C5</h3>
            </div>
            <div class="card">
                <strong>1 Metode</strong>
                <h3>ORESTE + Besson Rank</h3>
            </div>
        </div>
    </div>
</section>

<?php include 'layout/footer.php';
