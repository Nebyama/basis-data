<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database_name = 'oreste_db';

$connection = mysqli_connect($hostname, $username, $password);
if (!$connection) {
    die('Koneksi awal ke MySQL gagal: ' . mysqli_connect_error());
}

if (!mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS $database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die('Gagal membuat database: ' . mysqli_error($connection));
}

mysqli_select_db($connection, $database_name);

$tables = [
    'CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)',
    'CREATE TABLE IF NOT EXISTS kriteria (id INT AUTO_INCREMENT PRIMARY KEY, kode VARCHAR(5), nama VARCHAR(100), bobot DECIMAL(4,2), rank_bobot INT)',
    'CREATE TABLE IF NOT EXISTS alternatif (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50), c1 INT, c2 INT, c3 INT, c4 INT, c5 INT)',
    'CREATE TABLE IF NOT EXISTS besson_rank (id INT AUTO_INCREMENT PRIMARY KEY, alternatif_id INT, c1_rank DECIMAL(5,1), c2_rank DECIMAL(5,1), c3_rank DECIMAL(5,1), c4_rank DECIMAL(5,1), c5_rank DECIMAL(5,1), FOREIGN KEY (alternatif_id) REFERENCES alternatif(id))',
    'CREATE TABLE IF NOT EXISTS normalisasi (id INT AUTO_INCREMENT PRIMARY KEY, alternatif_id INT, c1_norm DECIMAL(6,4), c2_norm DECIMAL(6,4), c3_norm DECIMAL(6,4), c4_norm DECIMAL(6,4), c5_norm DECIMAL(6,4), FOREIGN KEY (alternatif_id) REFERENCES alternatif(id))',
    'CREATE TABLE IF NOT EXISTS distance_score (id INT AUTO_INCREMENT PRIMARY KEY, alternatif_id INT, d_c1 DECIMAL(8,3), d_c2 DECIMAL(8,3), d_c3 DECIMAL(8,3), d_c4 DECIMAL(8,3), d_c5 DECIMAL(8,3), akumulasi DECIMAL(10,3), FOREIGN KEY (alternatif_id) REFERENCES alternatif(id))',
    'CREATE TABLE IF NOT EXISTS ranking_oreste (id INT AUTO_INCREMENT PRIMARY KEY, ranking INT, alternatif_id INT, akumulasi DECIMAL(10,3), FOREIGN KEY (alternatif_id) REFERENCES alternatif(id))',
];

foreach ($tables as $sql) {
    if (!mysqli_query($connection, $sql)) {
        die('Gagal membuat tabel: ' . mysqli_error($connection));
    }
}

$dataPath = __DIR__ . '/oreste_data.json';
if (!file_exists($dataPath)) {
    die('File data tidak ditemukan: ' . $dataPath);
}

$data = json_decode(file_get_contents($dataPath), true);
if ($data === null) {
    die('Gagal membaca data JSON.');
}

function execute($conn, $sql) {
    if (!mysqli_query($conn, $sql)) {
        die('Error SQL: ' . mysqli_error($conn));
    }
}

execute($connection, 'SET FOREIGN_KEY_CHECKS = 0');
execute($connection, 'TRUNCATE TABLE ranking_oreste');
execute($connection, 'TRUNCATE TABLE distance_score');
execute($connection, 'TRUNCATE TABLE normalisasi');
execute($connection, 'TRUNCATE TABLE besson_rank');
execute($connection, 'TRUNCATE TABLE alternatif');
execute($connection, 'TRUNCATE TABLE kriteria');
execute($connection, 'TRUNCATE TABLE users');
execute($connection, 'SET FOREIGN_KEY_CHECKS = 1');

$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$insertUser = mysqli_prepare($connection, 'INSERT INTO users (username, password) VALUES (?, ?)');
mysqli_stmt_bind_param($insertUser, 'ss', $adminUsername, $adminPassword);
$adminUsername = 'admin';
mysqli_stmt_execute($insertUser);
mysqli_stmt_close($insertUser);

$insertKriteria = mysqli_prepare($connection, 'INSERT INTO kriteria (kode, nama, bobot, rank_bobot) VALUES (?, ?, ?, ?)');
foreach ($data['bobot'] as $item) {
    $kode = strtoupper(trim(preg_replace('/.*\((C[1-5])\).*/', '$1', $item['nama'])) ?: '');
    $nama = $item['nama'];
    $bobot = $item['bobot'];
    $rank = $item['rank_bobot'];
    mysqli_stmt_bind_param($insertKriteria, 'ssdi', $kode, $nama, $bobot, $rank);
    mysqli_stmt_execute($insertKriteria);
}
mysqli_stmt_close($insertKriteria);

$insertAlternatif = mysqli_prepare($connection, 'INSERT INTO alternatif (nama, c1, c2, c3, c4, c5) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($data['alternatif'] as $item) {
    mysqli_stmt_bind_param($insertAlternatif, 'siiiii', $item['nama'], $item['c1'], $item['c2'], $item['c3'], $item['c4'], $item['c5']);
    mysqli_stmt_execute($insertAlternatif);
}
mysqli_stmt_close($insertAlternatif);

$alternatifMap = [];
$result = mysqli_query($connection, 'SELECT id, nama FROM alternatif');
while ($row = mysqli_fetch_assoc($result)) {
    $alternatifMap[$row['nama']] = $row['id'];
}

$insertBesson = mysqli_prepare($connection, 'INSERT INTO besson_rank (alternatif_id, c1_rank, c2_rank, c3_rank, c4_rank, c5_rank) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($data['besson'] as $nama => $values) {
    $altId = $alternatifMap[$nama] ?? null;
    if (!$altId) continue;
    mysqli_stmt_bind_param(
        $insertBesson,
        'iddddd',
        $altId,
        $values['c1']['besson_rank'],
        $values['c2']['besson_rank'],
        $values['c3']['besson_rank'],
        $values['c4']['besson_rank'],
        $values['c5']['besson_rank']
    );
    mysqli_stmt_execute($insertBesson);
}
mysqli_stmt_close($insertBesson);

$insertNorm = mysqli_prepare($connection, 'INSERT INTO normalisasi (alternatif_id, c1_norm, c2_norm, c3_norm, c4_norm, c5_norm) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($data['normalisasi'] as $item) {
    $altId = $alternatifMap[$item['nama']] ?? null;
    if (!$altId) continue;
    mysqli_stmt_bind_param($insertNorm, 'iddddd', $altId, $item['c1'], $item['c2'], $item['c3'], $item['c4'], $item['c5']);
    mysqli_stmt_execute($insertNorm);
}
mysqli_stmt_close($insertNorm);

$insertDistance = mysqli_prepare($connection, 'INSERT INTO distance_score (alternatif_id, d_c1, d_c2, d_c3, d_c4, d_c5, akumulasi) VALUES (?, ?, ?, ?, ?, ?, ?)');
foreach ($data['distance_score'] as $item) {
    $altId = $alternatifMap[$item['nama']] ?? null;
    if (!$altId) continue;
    mysqli_stmt_bind_param($insertDistance, 'idddddd', $altId, $item['d_c1'], $item['d_c2'], $item['d_c3'], $item['d_c4'], $item['d_c5'], $item['akumulasi']);
    mysqli_stmt_execute($insertDistance);
}
mysqli_stmt_close($insertDistance);

$insertRanking = mysqli_prepare($connection, 'INSERT INTO ranking_oreste (ranking, alternatif_id, akumulasi) VALUES (?, ?, ?)');
foreach ($data['ranking'] as $item) {
    $altId = $alternatifMap[$item['nama']] ?? null;
    if (!$altId) continue;
    $rankingNumber = $item['ranking'];
    mysqli_stmt_bind_param($insertRanking, 'iid', $rankingNumber, $altId, $item['akumulasi']);
    mysqli_stmt_execute($insertRanking);
}
mysqli_stmt_close($insertRanking);

echo "Seeder selesai. Database oreste_db sudah terisi.\n";
