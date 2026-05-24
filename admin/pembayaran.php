<?php
include_once '../config.php';

$title = 'Kelola Pembayaran';
$active = 'pembayaran';
$msg = '';
if (isset($_GET['hapus'])) {
    $conn->query("DELETE FROM pembayaran WHERE id=" . (int)$_GET['hapus']);
    $msg = alert('Data pembayaran dihapus!', 'success');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $penghuni_id = (int)$_POST['penghuni_id'];
    $bulan = clean($_POST['bulan']);
    $tahun = (int)$_POST['tahun'];
    $jumlah = (float)$_POST['jumlah'];
    $status = clean($_POST['status']);
    $tgl_bayar = ($status == 'lunas') ? date('Y-m-d H:i:s') : null;
    
    if ($id) {
        $stmt = $conn->prepare("UPDATE pembayaran SET penghuni_id=?, bulan=?, tahun=?, jumlah=?, status=?, tanggal_bayar=? WHERE id=?");
        $stmt->bind_param("isidssi", $penghuni_id, $bulan, $tahun, $jumlah, $status, $tgl_bayar, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO pembayaran (penghuni_id, bulan, tahun, jumlah, status, tanggal_bayar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isidss", $penghuni_id, $bulan, $tahun, $jumlah, $status, $tgl_bayar);
    }
    $stmt->execute();
    $msg = alert('Data pembayaran disimpan!', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = $conn->query("SELECT * FROM pembayaran WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}

$penghunis = $conn->query("SELECT p.id, u.nama_lengkap, k.nomor_kamar 
                             FROM penghuni p 
                             JOIN users u ON p.user_id = u.id 
                             JOIN kamar k ON p.kamar_id = k.id 
                             WHERE p.status='aktif'");

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="m-0">Data Pembayaran</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBayar">
        <i class="bi bi-plus-lg"></i> Tambah Pembayaran
    </button>
</div>

<?= $msg ?>

<div class="table-container">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Periode</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $conn->query("SELECT pb.*, u.nama_lengkap, k.nomor_kamar 
                               FROM pembayaran pb
                               JOIN penghuni p ON pb.penghuni_id = p.id
                               JOIN users u ON p.user_id = u.id
                               JOIN kamar k ON p.kamar_id = k.id
                               ORDER BY pb.tahun DESC, FIELD(pb.bulan, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')");
            $no = 1;
            while ($row = $q->fetch_assoc()):
                $badge = $row['status'] == 'lunas' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger');
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_lengkap'] ?></td>
                <td><?= $row['nomor_kamar'] ?></td>
                <td><?= $row['bulan'] ?> <?= $row['tahun'] ?></td>
                <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $edit ? 'Edit' : 'Tambah' ?> Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Penghuni</label>
                        <select name="penghuni_id" class="form-select" required>
                            <?php while($p = $penghunis->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>" <?= ($edit['penghuni_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= $p['nama_lengkap'] ?> (<?= $p['nomor_kamar'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                <?php foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b): ?>
                                <option value="<?= $b ?>" <?= ($edit['bulan'] ?? '') == $b ? 'selected' : '' ?>><?= $b ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= $edit['tahun'] ?? date('Y') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" value="<?= $edit['jumlah'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="belum_bayar" <?= ($edit['status'] ?? '') == 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                            <option value="pending" <?= ($edit['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="lunas" <?= ($edit['status'] ?? '') == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit): ?>
<script>new bootstrap.Modal(document.getElementById('modalBayar')).show();</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>