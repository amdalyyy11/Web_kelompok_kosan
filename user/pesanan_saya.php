<?php
include_once '../config.php';

$title = 'Pesanan Saya';
$active = 'pesanan';

$user_id = $_SESSION['user_id'];

$q = $conn->query("SELECT ps.*, k.nomor_kamar, k.tipe, k.harga_bulanan, k.fasilitas, k.gambar 
                   FROM pesanan ps
                   JOIN kamar k ON ps.kamar_id = k.id
                   WHERE ps.user_id = $user_id
                   ORDER BY FIELD(ps.status, 'pending', 'diterima', 'ditolak'), ps.tanggal_pesan DESC");

ob_start();
?>

<h5 class="mb-3"><i class="bi bi-cart"></i> Riwayat Pesanan Saya</h5>

<div class="alert alert-light border">
    <small><i class="bi bi-info-circle"></i> <strong>Keterangan Status:</strong></small><br>
    <span class="badge bg-warning text-dark">Pending</span> <small>Menunggu konfirmasi admin</small><br>
    <span class="badge bg-success">Diterima</span> <small>Pesanan diterima, Anda resmi jadi penghuni</small><br>
    <span class="badge bg-danger">Ditolak</span> <small>Pesanan ditolak, silakan pesan kamar lain</small>
</div>

<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Kamar</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Pesan</th>
            <th>Status</th>
            <th>Keterangan</th>
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
            $icon = match($row['status']) {
                'pending' => 'bi-hourglass-split',
                'diterima' => 'bi-check-circle-fill',
                'ditolak' => 'bi-x-circle-fill',
                default => 'bi-question-circle'
            };
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <strong class="text-primary"><?= $row['nomor_kamar'] ?></strong><br>
                <small class="text-muted"><?= $row['tipe'] ?> - Rp<?= number_format($row['harga_bulanan'], 0, ',', '.') ?></small>
            </td>
            <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
            <td><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
            <td>
                <span class="badge bg-<?= $badge ?>">
                    <i class="bi <?= $icon ?>"></i> <?= ucfirst($row['status']) ?>
                </span>
            </td>
            <td><small><?= $row['keterangan'] ?: '-' ?></small></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($q->num_rows == 0): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pesanan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include 'layout.php';
?>