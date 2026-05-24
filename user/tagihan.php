<?php
include_once '../config.php';

$title = 'Tagihan Saya';
$active = 'tagihan';

$user_id = $_SESSION['user_id'];

$q = $conn->query("SELECT pb.* 
                   FROM pembayaran pb
                   JOIN penghuni p ON pb.penghuni_id = p.id
                   WHERE p.user_id = $user_id
                   ORDER BY pb.tahun DESC, FIELD(pb.bulan, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')");

ob_start();
?>
<h5 class="mb-3"><i class="bi bi-receipt"></i> Riwayat Tagihan</h5>

<table class="table table-hover">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Periode</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Tanggal Bayar</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $q->fetch_assoc()): 
            $badge = $row['status'] == 'lunas' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger');
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['bulan'] ?> <?= $row['tahun'] ?></td>
            <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
            <td><?= $row['tanggal_bayar'] ? date('d M Y', strtotime($row['tanggal_bayar'])) : '-' ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($q->num_rows == 0): ?>
        <tr><td colspan="5" class="text-center text-muted">Belum ada data tagihan</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include 'layout.php';
?>