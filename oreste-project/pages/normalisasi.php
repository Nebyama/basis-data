<div class="page-card card">
    <h2>Normalisasi</h2>
    <p>Nilai normalisasi dari Besson Rank untuk setiap alternatif.</p>
    <div class="table-wrapper">
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
                <?php
                $result = mysqli_query($db, 'SELECT a.nama, n.c1_norm, n.c2_norm, n.c3_norm, n.c4_norm, n.c5_norm FROM normalisasi n JOIN alternatif a ON a.id = n.alternatif_id ORDER BY a.id');
                $index = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $index++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= number_format($row['c1_norm'], 4) ?></td>
                        <td><?= number_format($row['c2_norm'], 4) ?></td>
                        <td><?= number_format($row['c3_norm'], 4) ?></td>
                        <td><?= number_format($row['c4_norm'], 4) ?></td>
                        <td><?= number_format($row['c5_norm'], 4) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
