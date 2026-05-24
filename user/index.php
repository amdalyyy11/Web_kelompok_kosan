<?php
include_once '../config.php';

$title = 'Dashboard';
$active = 'dashboard';

$user_id = $_SESSION['user_id'];

// Info kamar aktif (kalau sudah jadi penghuni)
$penghuni = $conn->query("SELECT p.*, k.nomor_kamar, k.tipe, k.harga_bulanan, k.fasilitas 
                          FROM penghuni p 
                          JOIN kamar k ON p.kamar_id = k.id 
                          WHERE p.user_id = $user_id AND p.status='aktif'")->fetch_assoc();

// Statistik
$tagihan_belum = $conn->query("SELECT COUNT(*) as total FROM pembayaran pb 
                                JOIN penghuni p ON pb.penghuni_id = p.id 
                                WHERE p.user_id = $user_id AND pb.status != 'lunas'")->fetch_assoc()['total'];

$pesanan_pending = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE user_id=$user_id AND status='pending'")->fetch_assoc()['total'];
$pesanan_diterima = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE user_id=$user_id AND status='diterima'")->fetch_assoc()['total'];

ob_start();
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white border-0" style="border-radius: 15px;">
            <div class="card-body">
                <h6><i class="bi bi-door-open"></i> Kamar Saya</h6>
                <h2><?= $penghuni ? $penghuni['nomor_kamar'] : '-' ?></h2>
                <p class="mb-0"><?= $penghuni ? $penghuni['tipe'] : 'Belum memiliki kamar' ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark border-0" style="border-radius: 15px;">
            <div class="card-body">
                <h6><i class="bi bi-receipt"></i> Tagihan Pending</h6>
                <h2><?= $tagihan_belum ?></h2>
                <p class="mb-0">Tagihan belum lunas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white border-0" style="border-radius: 15px;">
            <div class="card-body">
                <h6><i class="bi bi-cart"></i> Status Pesanan</h6>
                <h2><?= $pesanan_pending > 0 ? 'Pending' : ($pesanan_diterima > 0 ? 'Diterima' : 'Kosong') ?></h2>
                <p class="mb-0"><?= $pesanan_pending ?> menunggu konfirmasi</p>
            </div>
        </div>
    </div>
</div>

<?php if ($penghuni): ?>
<div class="card border-0 mb-4" style="border-radius: 15px; background: #f8f9fa;">
    <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-info-circle"></i> Informasi Kamar</h5>
        <table class="table table-borderless">
            <tr><td width="30%"><strong>Nomor Kamar</strong></td><td>: <?= $penghuni['nomor_kamar'] ?></td></tr>
            <tr><td><strong>Tipe</strong></td><td>: <?= $penghuni['tipe'] ?></td></tr>
            <tr><td><strong>Fasilitas</strong></td><td>: <?= $penghuni['fasilitas'] ?></td></tr>
            <tr><td><strong>Tanggal Masuk</strong></td><td>: <?= date('d M Y', strtotime($penghuni['tanggal_masuk'])) ?></td></tr>
            <tr><td><strong>Harga/Bulan</strong></td><td>: Rp<?= number_format($penghuni['harga_bulanan'], 0, ',', '.') ?></td></tr>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <h5><i class="bi bi-info-circle"></i> Belum Memiliki Kamar</h5>
    <p>Anda belum terdaftar sebagai penghuni. Silakan pesan kamar melalui menu <strong>Kamar Tersedia</strong>.</p>
    <a href="kamar_tersedia.php" class="btn btn-primary"><i class="bi bi-search"></i> Cari Kamar</a>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>