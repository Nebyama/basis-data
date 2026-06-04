<div class="page-card card">
    <h2>Nilai Alternatif</h2>
    <p>Data 40 remaja dengan skor untuk setiap kriteria C1 sampai C5.</p>
    <form method="get" action="dashboard.php" style="margin-bottom: 1.5rem; display: grid; gap: 0.75rem;">
        <input type="hidden" name="page" value="alternatif">
        <div class="form-group">
            <label for="q">Cari Nama Alternatif</label>
            <input id="q" name="q" type="text" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Contoh: Remaja 1">
        </div>
        <button type="submit">Cari Alternatif</button>
    </form>
    <div class="table-wrapper">
        <?php
        $keyword = trim($_GET['q'] ?? '');
        $where = '';
        if ($keyword !== '') {
            $search = mysqli_real_escape_string($db, '%' . $keyword . '%');
            $where = "WHERE nama LIKE '$search'";
        }
        $query = "SELECT * FROM alternatif $where ORDER BY id";
        $result = mysqli_query($db, $query);
        ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Alternatif</th>
                    <th>C1</th>
                    <th>C2</th>
                    <th>C3</th>
                    <th>C4</th>
                    <th>C5</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['c1']) ?></td>
                        <td><?= htmlspecialchars($row['c2']) ?></td>
                        <td><?= htmlspecialchars($row['c3']) ?></td>
                        <td><?= htmlspecialchars($row['c4']) ?></td>
                        <td><?= htmlspecialchars($row['c5']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top: 1rem; color: var(--text-medium);">Keterangan: Nilai alternatif menggunakan skala 1–100 untuk setiap kriteria.</p>
</div>
