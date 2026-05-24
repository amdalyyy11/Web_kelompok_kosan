<?php
include_once '../config.php';

$title = 'Dashboard Admin';
$active = 'dashboard';

// Statistik
$total_kamar = $conn->query("SELECT COUNT(*) as total FROM kamar")->fetch_assoc()['total'];
$kamar_tersedia = $conn->query("SELECT COUNT(*) as total FROM kamar WHERE status='tersedia'")->fetch_assoc()['total'];
$total_penghuni = $conn->query("SELECT COUNT(*) as total FROM penghuni WHERE status='aktif'")->fetch_assoc()['total'];
$total_bayar = $conn->query("SELECT SUM(jumlah) as total FROM pembayaran WHERE status='lunas'")->fetch_assoc()['total'];
$pesanan_pending = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status='pending'")->fetch_assoc()['total'];

ob_start();
?>
<div class="row mb-4">
    <div class="col-md-4 col-lg-2-4 mb-3">
        <div class="card card-stat bg-primary text-white">
            <div class="card-body">
                <h6>Total Kamar</h6>
                <h3><?= $total_kamar ?></h3>
                <i class="bi bi-door-open" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 mb-3">
        <div class="card card-stat bg-success text-white">
            <div class="card-body">
                <h6>Kamar Tersedia</h6>
                <h3><?= $kamar_tersedia ?></h3>
                <i class="bi bi-check-circle" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 mb-3">
        <div class="card card-stat bg-warning text-dark">
            <div class="card-body">
                <h6>Penghuni Aktif</h6>
                <h3><?= $total_penghuni ?></h3>
                <i class="bi bi-people" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 mb-3">
        <div class="card card-stat bg-info text-white">
            <div class="card-body">
                <h6>Pesanan Masuk</h6>
                <h3><?= $pesanan_pending ?></h3>
                <i class="bi bi-cart" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2-4 mb-3">
        <div class="card card-stat bg-dark text-white">
            <div class="card-body">
                <h6>Pemasukan</h6>
                <h3>Rp<?= number_format($total_bayar ?? 0, 0, ',', '.') ?></h3>
                <i class="bi bi-cash" style="font-size: 2rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="table-container">
            <h5 class="mb-3"><i class="bi bi-clock-history"></i> Penghuni Terbaru</h5>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>No</th><th>Nama</th><th>Kamar</th><th>Masuk</th></tr>
                </thead>
                <tbody>
                    <?php
                    $q = $conn->query("SELECT p.*, u.nama_lengkap, k.nomor_kamar 
                                       FROM penghuni p JOIN users u ON p.user_id = u.id 
                                       JOIN kamar k ON p.kamar_id = k.id 
                                       WHERE p.status='aktif' ORDER BY p.tanggal_masuk DESC LIMIT 5");
                    $no = 1;
                    while ($row = $q->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><span class="badge bg-primary"><?= $row['nomor_kamar'] ?></span></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($q->num_rows == 0): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="table-container">
            <h5 class="mb-3"><i class="bi bi-cart"></i> Pesanan Pending</h5>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>No</th><th>Nama User</th><th>Kamar</th><th>Tgl Masuk</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php
                    $qp = $conn->query("SELECT ps.*, u.nama_lengkap, k.nomor_kamar 
                                        FROM pesanan ps 
                                        JOIN users u ON ps.user_id = u.id 
                                        JOIN kamar k ON ps.kamar_id = k.id 
                                        WHERE ps.status='pending' ORDER BY ps.tanggal_pesan DESC LIMIT 5");
                    $no = 1;
                    while ($row = $qp->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['nomor_kamar'] ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
                        <td><a href="pesanan.php" class="btn btn-sm btn-success">Proses</a></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($qp->num_rows == 0): ?>
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada pesanan pending</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout.php';
?>