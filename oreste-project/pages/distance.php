<div class="page-card card">
    <h2>Distance Score</h2>
    <p>Perhitungan distance score berdasarkan rumus ORESTE dan Besson Rank.</p>
    <p style="font-weight: 600;">Rumus: D(a, Cj) = [½ × rc<sup>R</sup> + ½ × rc(a)<sup>R</sup>]^(1/R) dengan R = 3</p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Alternatif</th>
                    <th>D(C1)</th>
                    <th>D(C2)</th>
                    <th>D(C3)</th>
                    <th>D(C4)</th>
                    <th>D(C5)</th>
                    <th>Akumulasi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $minQuery = mysqli_query($db, 'SELECT MIN(akumulasi) AS min_akumulasi FROM distance_score');
                $minRow = mysqli_fetch_assoc($minQuery);
                $minAkumulasi = $minRow['min_akumulasi'] ?? 0;
                $result = mysqli_query($db, 'SELECT a.nama, d.d_c1, d.d_c2, d.d_c3, d.d_c4, d.d_c5, d.akumulasi FROM distance_score d JOIN alternatif a ON a.id = d.alternatif_id ORDER BY d.id');
                $index = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="<?= abs($row['akumulasi'] - $minAkumulasi) < 0.001 ? 'highlight-row' : '' ?>">
                        <td><?= $index++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= number_format($row['d_c1'], 3) ?></td>
                        <td><?= number_format($row['d_c2'], 3) ?></td>
                        <td><?= number_format($row['d_c3'], 3) ?></td>
                        <td><?= number_format($row['d_c4'], 3) ?></td>
                        <td><?= number_format($row['d_c5'], 3) ?></td>
                        <td><?= number_format($row['akumulasi'], 3) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
