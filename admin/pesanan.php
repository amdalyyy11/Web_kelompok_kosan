<?php
include_once '../config.php';

$title = 'Kelola Pesanan';
$active = 'pesanan';

$msg = '';

// Proses Terima
if (isset($_GET['terima'])) {
    $id = (int)$_GET['terima'];
    $pesanan = $conn->query("SELECT * FROM pesanan WHERE id=$id AND status='pending'")->fetch_assoc();
    
    if ($pesanan) {
        // 1. Insert ke penghuni
        $stmt = $conn->prepare("INSERT INTO penghuni (user_id, kamar_id, tanggal_masuk, status) VALUES (?, ?, ?, 'aktif')");
        $stmt->bind_param("iis", $pesanan['user_id'], $pesanan['kamar_id'], $pesanan['tanggal_masuk']);
        $stmt->execute();
        
        // 2. Update kamar jadi terisi
        $conn->query("UPDATE kamar SET status='terisi' WHERE id=" . $pesanan['kamar_id']);
        
        // 3. Update pesanan jadi diterima (TIDAK DIHAPUS!)
        $conn->query("UPDATE pesanan SET status='diterima' WHERE id=$id");
        
        $msg = alert('Pesanan diterima! User sekarang menjadi penghuni.', 'success');
    }
}

// Proses Tolak
if (isset($_GET['tolak'])) {
    $id = (int)$_GET['tolak'];
    // Update status jadi ditolak (TIDAK DIHAPUS!)
    $conn->query("UPDATE pesanan SET status='ditolak' WHERE id=$id");
    $msg = alert('Pesanan ditolak. Riwayat tetap tersimpan.', 'warning');
}

// Filter
$filter = $_GET['filter'] ?? 'semua';
$sql = "SELECT ps.*, u.nama_lengkap, u.no_hp, k.nomor_kamar, k.tipe, k.harga_bulanan 
        FROM pesanan ps 
        JOIN users u ON ps.user_id = u.id 
        JOIN kamar k ON ps.kamar_id = k.id";
if ($filter != 'semua') $sql .= " WHERE ps.status='$filter'";
$sql .= " ORDER BY FIELD(ps.status, 'pending', 'diterima', 'ditolak'), ps.tanggal_pesan DESC";

$q = $conn->query($sql);

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="m-0"><i class="bi bi-cart"></i> Data Pesanan Kamar</h5>
    <div>
        <a href="?filter=semua" class="btn btn-sm btn-outline-dark <?= $filter=='semua'?'active':'' ?>">Semua</a>
        <a href="?filter=pending" class="btn btn-sm btn-outline-warning <?= $filter=='pending'?'active':'' ?>">Pending</a>
        <a href="?filter=diterima" class="btn btn-sm btn-outline-success <?= $filter=='diterima'?'active':'' ?>">Diterima</a>
        <a href="?filter=ditolak" class="btn btn-sm btn-outline-danger <?= $filter=='ditolak'?'active':'' ?>">Ditolak</a>
    </div>
</div>

<?= $msg ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> <strong>Catatan:</strong> Pesanan yang diterima/ditolak akan tetap tersimpan sebagai riwayat. Data tidak dihapus dari sistem.
</div>

<div class="table-container">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Pemesan</th>
                <th>No HP</th>
                <th>Kamar</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Pesan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = $q->fetch_assoc()): 
                $badge = match($row['status']) {
                    'pending' => 'warning text-dark',
                    'diterima' => 'success',
                    'ditolak' => 'danger',
                    default => 'secondary'
                };
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_lengkap'] ?></td>
                <td><?= $row['no_hp'] ?></td>
                <td>
                    <strong><?= $row['nomor_kamar'] ?></strong><br>
                    <small class="text-muted"><?= $row['tipe'] ?></small>
                </td>
                <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                    <a href="?terima=<?= $row['id'] ?>" class="btn btn-sm btn-success mb-1" onclick="return confirm('Terima pesanan? Kamar akan otomatis jadi terisi.')"><i class="bi bi-check-lg"></i> Terima</a>
                    <a href="?tolak=<?= $row['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Tolak pesanan ini? Data tetap tersimpan sebagai riwayat.')"><i class="bi bi-x-lg"></i> Tolak</a>
                    <?php else: ?>
                    <span class="text-muted"><i class="bi bi-check-all"></i> Sudah diproses</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($q->num_rows == 0): ?>
            <tr><td colspan="8" class="text-center text-muted">Tidak ada data pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include 'layout.php';
?>