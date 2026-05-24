<?php
include_once '../config.php';

$title = 'Kelola Penghuni';
$active = 'penghuni';

$msg = '';
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM penghuni WHERE id=$id");
    $msg = alert('Data penghuni dihapus!', 'success');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $user_id = (int)$_POST['user_id'];
    $kamar_id = (int)$_POST['kamar_id'];
    $tgl_masuk = $_POST['tanggal_masuk'];
    $tgl_keluar = $_POST['tanggal_keluar'] ?: null;
    $status = clean($_POST['status']);
    
    if ($id) {
        $stmt = $conn->prepare("UPDATE penghuni SET user_id=?, kamar_id=?, tanggal_masuk=?, tanggal_keluar=?, status=? WHERE id=?");
        $stmt->bind_param("iisssi", $user_id, $kamar_id, $tgl_masuk, $tgl_keluar, $status, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO penghuni (user_id, kamar_id, tanggal_masuk, tanggal_keluar, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $kamar_id, $tgl_masuk, $tgl_keluar, $status);
        // Update kamar jadi terisi
        $conn->query("UPDATE kamar SET status='terisi' WHERE id=$kamar_id");
    }
    $stmt->execute();
    $msg = alert('Data penghuni disimpan!', 'success');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = $conn->query("SELECT * FROM penghuni WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}

$users = $conn->query("SELECT id, nama_lengkap FROM users WHERE role='user'");
$kamars = $conn->query("SELECT id, nomor_kamar FROM kamar WHERE status='tersedia' OR id=" . ($edit['kamar_id'] ?? 0));

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="m-0">Data Penghuni</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPenghuni">
        <i class="bi bi-plus-lg"></i> Tambah Penghuni
    </button>
</div>

<?= $msg ?>

<div class="table-container">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kamar</th>
                <th>Masuk</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $conn->query("SELECT p.*, u.nama_lengkap, k.nomor_kamar 
                               FROM penghuni p 
                               JOIN users u ON p.user_id = u.id 
                               JOIN kamar k ON p.kamar_id = k.id 
                               ORDER BY p.tanggal_masuk DESC");
            $no = 1;
            while ($row = $q->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_lengkap'] ?></td>
                <td><?= $row['nomor_kamar'] ?></td>
                <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
                <td><span class="badge bg-<?= $row['status'] == 'aktif' ? 'success' : 'secondary' ?>"><?= ucfirst($row['status']) ?></span></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalPenghuni" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $edit ? 'Edit' : 'Tambah' ?> Penghuni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Pilih User</label>
                        <select name="user_id" class="form-select" required>
                            <?php while($u = $users->fetch_assoc()): ?>
                            <option value="<?= $u['id'] ?>" <?= ($edit['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= $u['nama_lengkap'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Kamar</label>
                        <select name="kamar_id" class="form-select" required>
                            <?php while($k = $kamars->fetch_assoc()): ?>
                            <option value="<?= $k['id'] ?>" <?= ($edit['kamar_id'] ?? '') == $k['id'] ? 'selected' : '' ?>><?= $k['nomor_kamar'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= $edit['tanggal_masuk'] ?? date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Keluar (opsional)</label>
                        <input type="date" name="tanggal_keluar" class="form-control" value="<?= $edit['tanggal_keluar'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" <?= ($edit['status'] ?? '') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="keluar" <?= ($edit['status'] ?? '') == 'keluar' ? 'selected' : '' ?>>Keluar</option>
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
<script>new bootstrap.Modal(document.getElementById('modalPenghuni')).show();</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>