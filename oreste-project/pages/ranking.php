<div class="page-card card">
    <h2>Perankingan Akhir</h2>
    <p>Semakin kecil akumulasi distance score, semakin berat dampak internet terhadap remaja.</p>
    <?php
    $podiumQuery = mysqli_query($db, 'SELECT r.ranking, a.nama, r.akumulasi FROM ranking_oreste r JOIN alternatif a ON a.id = r.alternatif_id WHERE r.ranking <= 3 ORDER BY r.ranking');
    $fullQuery = mysqli_query($db, 'SELECT r.ranking, a.nama, r.akumulasi FROM ranking_oreste r JOIN alternatif a ON a.id = r.alternatif_id ORDER BY r.ranking');
    $podium = mysqli_fetch_all($podiumQuery, MYSQLI_ASSOC);
    ?>
    <div class="podium-grid">
        <?php foreach ($podium as $row): ?>
            <?php
                $rank = (int) $row['ranking'];
                $class = 'podium-card';
                if ($rank === 1) { $class .= ' podium-1'; }
                if ($rank === 2) { $class .= ' podium-2'; }
                if ($rank === 3) { $class .= ' podium-3'; }
            ?>
            <div class="<?= $class ?>">
                <span style="font-size: 1.1rem; font-weight: 700;">#<?= $rank ?></span>
                <h3><?= htmlspecialchars($row['nama']) ?></h3>
                <p>Akumulasi: <?= number_format($row['akumulasi'], 3) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="table-wrapper" style="margin-top: 1.5rem;">
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama Alternatif</th>
                    <th>Akumulasi Distance Score</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($fullQuery)): ?>
                    <?php
                        $rank = (int) $row['ranking'];
                        $class = '';
                        if ($rank === 1) { $class = 'highlight-row'; }
                        elseif ($rank === 2) { $class = 'highlight-row'; }
                        elseif ($rank === 3) { $class = 'highlight-row'; }
                        elseif ($rank <= 10) { $class = 'badge green'; }
                    ?>
                    <tr class="<?= $class ?>">
                        <td>
                            <?php if ($rank === 1): ?>
                                🥇
                            <?php elseif ($rank === 2): ?>
                                🥈
                            <?php elseif ($rank === 3): ?>
                                🥉
                            <?php else: ?>
                                <?= $rank ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= number_format($row['akumulasi'], 3) ?></td>
                        <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top: 1rem; color: var(--text-medium);">Semakin kecil akumulasi = dampak internet semakin berat.</p>
</div>
