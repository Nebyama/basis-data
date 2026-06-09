<div class="page-card card">
    <h2>Nilai Alternatif</h2>
    <p>Data alternatif kini dapat ditambah, diubah, dan dihapus untuk mendukung fitur tambahan.</p>

    <?php
    $message = '';
    $messageType = 'alert-card';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                mysqli_query($db, 'SET FOREIGN_KEY_CHECKS = 0');
                mysqli_query($db, 'DELETE FROM ranking_oreste WHERE alternatif_id = ' . $id);
                mysqli_query($db, 'DELETE FROM distance_score WHERE alternatif_id = ' . $id);
                mysqli_query($db, 'DELETE FROM normalisasi WHERE alternatif_id = ' . $id);
                mysqli_query($db, 'DELETE FROM besson_rank WHERE alternatif_id = ' . $id);
                mysqli_query($db, 'DELETE FROM alternatif WHERE id = ' . $id);
                mysqli_query($db, 'SET FOREIGN_KEY_CHECKS = 1');
                $message = 'Data alternatif berhasil dihapus.';
                $messageType = 'alert-card';
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $nama = trim($_POST['nama'] ?? '');
            $c1 = (int) ($_POST['c1'] ?? 0);
            $c2 = (int) ($_POST['c2'] ?? 0);
            $c3 = (int) ($_POST['c3'] ?? 0);
            $c4 = (int) ($_POST['c4'] ?? 0);
            $c5 = (int) ($_POST['c5'] ?? 0);

            if ($nama === '') {
                $message = 'Nama alternatif wajib diisi.';
                $messageType = 'alert-card';
            } else {
                if ($id > 0) {
                    $stmt = mysqli_prepare($db, 'UPDATE alternatif SET nama=?, c1=?, c2=?, c3=?, c4=?, c5=? WHERE id=?');
                    mysqli_stmt_bind_param($stmt, 'siiiiii', $nama, $c1, $c2, $c3, $c4, $c5, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Data alternatif berhasil diperbarui.';
                } else {
                    $stmt = mysqli_prepare($db, 'INSERT INTO alternatif (nama, c1, c2, c3, c4, c5) VALUES (?, ?, ?, ?, ?, ?)');
                    mysqli_stmt_bind_param($stmt, 'siiiii', $nama, $c1, $c2, $c3, $c4, $c5);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Data alternatif berhasil ditambahkan.';
                }
            }
        }
    }

    $editingId = (int) ($_GET['edit_id'] ?? 0);
    $editing = null;
    if ($editingId > 0) {
        $editResult = mysqli_query($db, 'SELECT * FROM alternatif WHERE id = ' . $editingId . ' LIMIT 1');
        $editing = mysqli_fetch_assoc($editResult);
    }

    if ($message !== '') {
        echo '<div class="' . $messageType . ' card" style="margin-bottom: 1rem; background: #EAF8F0; color: #1F5D40;">' . htmlspecialchars($message) . '</div>';
    }
    ?>

    <div class="grid feature-grid" style="margin-bottom: 1rem;">
        <div class="card">
            <strong><?= $editing ? 'Ubah Alternatif' : 'Tambah Alternatif Baru' ?></strong>
            <form method="post" action="dashboard.php?page=alternatif">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">
                <div class="form-group">
                    <label for="nama">Nama Alternatif</label>
                    <input id="nama" name="nama" type="text" value="<?= htmlspecialchars($editing['nama'] ?? '') ?>" placeholder="Contoh: Remaja 41">
                </div>
                <div class="grid stats-grid">
                    <?php for ($i = 1; $i <= 5; $i++): $field = 'c' . $i; ?>
                    <div class="form-group">
                        <label for="c<?= $i ?>">C<?= $i ?></label>
                        <input id="c<?= $i ?>" name="c<?= $i ?>" type="number" min="1" max="100" value="<?= htmlspecialchars((string) ($editing[$field] ?? 50)) ?>">
                    </div>
                    <?php endfor; ?>
                </div>
                <button type="submit"><?= $editing ? 'Simpan Perubahan' : 'Tambah Alternatif' ?></button>
            </form>
            <?php if ($editing): ?>
                <p style="margin-top: 0.75rem;"><a href="dashboard.php?page=alternatif">Batal edit</a></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <strong>Catatan Fitur</strong>
            <p>Setiap baris memiliki tombol ubah dan hapus. Setelah data dihapus, data terkait di tabel hasil juga ikut dibersihkan.</p>
            <p style="color: var(--text-medium);">Skala nilai tetap 1–100 untuk semua kriteria C1 hingga C5.</p>
        </div>
    </div>

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
                    <th>Aksi</th>
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
                        <td>
                            <a href="dashboard.php?page=alternatif&edit_id=<?= (int) $row['id'] ?>" class="button" style="display: inline-block; padding: 0.55rem 0.8rem; font-size: 0.9rem;">Ubah</a>
                            <form method="post" action="dashboard.php?page=alternatif" style="display: inline-block; margin-left: 0.35rem;" onsubmit="return confirm('Hapus data alternatif ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" style="padding: 0.55rem 0.8rem; font-size: 0.9rem; border-radius: 999px; background: linear-gradient(135deg, #E8735A 0%, #FF8C69 100%); color: #fff; border: none; cursor: pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top: 1rem; color: var(--text-medium);">Keterangan: Nilai alternatif menggunakan skala 1–100 untuk setiap kriteria.</p>
</div>
