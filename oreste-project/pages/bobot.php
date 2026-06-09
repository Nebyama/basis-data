<div class="page-card card">
    <h2>Bobot Kriteria</h2>
    <p>Data bobot kriteria diambil dari tabel <code>kriteria</code> dalam database.</p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kriteria</th>
                    <th>Nilai Bobot (Wi)</th>
                    <th>Rank Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($db, 'SELECT * FROM kriteria ORDER BY id');
                $totalBobot = 0;
                while ($row = mysqli_fetch_assoc($result)): 
                    $totalBobot += (float) $row['bobot'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= number_format($row['bobot'], 2) ?></td>
                        <td><?= htmlspecialchars($row['rank_bobot']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total Bobot</strong></td>
                    <td><strong><?= number_format($totalBobot, 2) ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
