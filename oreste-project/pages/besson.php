<div class="page-card card">
    <h2>Besson Rank</h2>
    <p>Perbandingan nilai alternatif per kriteria disertai Besson Rank dan normalisasi.</p>
    <?php
    $criteria = [
        ['label' => 'C1 (Durasi Penggunaan Internet)', 'nilaiColumn' => 'c1', 'rankColumn' => 'c1_rank', 'normColumn' => 'c1_norm'],
        ['label' => 'C2 (Kondisi Mental)', 'nilaiColumn' => 'c2', 'rankColumn' => 'c2_rank', 'normColumn' => 'c2_norm'],
        ['label' => 'C3 (Kualitas Tidur)', 'nilaiColumn' => 'c3', 'rankColumn' => 'c3_rank', 'normColumn' => 'c3_norm'],
        ['label' => 'C4 (Interaksi Sosial)', 'nilaiColumn' => 'c4', 'rankColumn' => 'c4_rank', 'normColumn' => 'c4_norm'],
        ['label' => 'C5 (Kesadaran Diri)', 'nilaiColumn' => 'c5', 'rankColumn' => 'c5_rank', 'normColumn' => 'c5_norm'],
    ];
    foreach ($criteria as $criterion):
        $sql = sprintf(
            'SELECT a.nama, a.%1$s AS nilai, b.%2$s AS besson_rank, n.%3$s AS normalisasi FROM alternatif a JOIN besson_rank b ON a.id = b.alternatif_id JOIN normalisasi n ON a.id = n.alternatif_id ORDER BY b.%2$s ASC',
            $criterion['nilaiColumn'],
            $criterion['rankColumn'],
            $criterion['normColumn']
        );
        $result = mysqli_query($db, $sql);
    ?>
        <div class="page-card card" style="padding: 1.5rem;">
            <h3><?= htmlspecialchars($criterion['label']) ?></h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Alternatif</th>
                            <th>Nilai</th>
                            <th>Besson Rank</th>
                            <th>Normalisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nilai']) ?></td>
                            <td><?= htmlspecialchars($row['besson_rank']) ?></td>
                            <td><?= number_format($row['normalisasi'], 4) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <p style="margin-top: 1rem; color: var(--text-medium);">Cara hitung Besson Rank: urutkan nilai kriteria, kemudian hitung peringkat dan normalisasi r/40.</p>
        </div>
    <?php endforeach; ?>
</div>
