<?php
include_once '../config.php';

$title = 'Kamar Tersedia';
$active = 'kamar';

$user_id = $_SESSION['user_id'];
$msg = '';

// Cek apakah user sudah punya kamar aktif
$punya_kamar = $conn->query("SELECT id FROM penghuni WHERE user_id=$user_id AND status='aktif'")->num_rows > 0;

// Proses pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$punya_kamar) {
    $kamar_id = (int)$_POST['kamar_id'];
    $tgl_masuk = $_POST['tanggal_masuk'];
    $keterangan = clean($_POST['keterangan']);
    
    // Cek apakah sudah ada pesanan pending untuk kamar ini
    $cek = $conn->query("SELECT id FROM pesanan WHERE kamar_id=$kamar_id AND status='pending'");
    if ($cek->num_rows > 0) {
        $msg = alert('Kamar ini sedang dalam proses pemesanan orang lain. Silakan pilih kamar lain.', 'warning');
    } else {
        $stmt = $conn->prepare("INSERT INTO pesanan (user_id, kamar_id, tanggal_masuk, keterangan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $kamar_id, $tgl_masuk, $keterangan);
        if ($stmt->execute()) {
            $msg = alert('Pesanan berhasil diajukan! Tunggu konfirmasi admin.', 'success');
        } else {
            $msg = alert('Gagal mengajukan pesanan.', 'danger');
        }
    }
}

$kamars = $conn->query("SELECT * FROM kamar WHERE status='tersedia' ORDER BY nomor_kamar");

// Mulai buffer — PASTIKAN tidak ada echo/print sebelum ini
ob_start();
?>

<h5 class="mb-3"><i class="bi bi-door-open"></i> Kamar Yang Tersedia</h5>

<?= $msg ?>

<?php if ($punya_kamar): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> Anda sudah memiliki kamar aktif. Tidak bisa memesan kamar lagi.
</div>
<?php endif; ?>

<div class="row">
    <?php while ($k = $kamars->fetch_assoc()): 
        $gambar_fisik = $k['gambar'] ? __DIR__ . '/../' . $k['gambar'] : '';
        $ada_gambar = $k['gambar'] && file_exists($gambar_fisik);
        $gambar_url = $ada_gambar ? '../' . $k['gambar'] : '';
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow:hidden;">
            
            <?php if ($ada_gambar): ?>
            <div style="height: 200px; overflow: hidden; position: relative;">
                <img src="<?= $gambar_url ?>" alt="Kamar <?= $k['nomor_kamar'] ?>" 
                     style="width:100%; height:100%; object-fit:cover;">
                <span class="badge bg-success position-absolute" style="top:10px; left:10px; z-index:2;">Tersedia</span>
            </div>
            <?php else: ?>
            <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; display:flex; align-items:center; justify-content:center; flex-direction:column;">
                <i class="bi bi-house-door-fill" style="font-size: 3rem; color: rgba(255,255,255,0.8);"></i>
                <span style="color: rgba(255,255,255,0.9); margin-top:8px; font-size:0.9rem;">Kamar <?= $k['nomor_kamar'] ?></span>
                <span class="badge bg-light text-dark position-absolute" style="top:10px; left:10px;">Tersedia</span>
            </div>
            <?php endif; ?>
            
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold text-primary m-0"><?= $k['nomor_kamar'] ?></h4>
                    <h5 class="text-warning fw-bold m-0">Rp<?= number_format($k['harga_bulanan'], 0, ',', '.') ?><small class="text-muted" style="font-size:0.7rem;">/bulan</small></h5>
                </div>
                <p class="mb-1"><strong>Tipe:</strong> <span class="badge bg-info text-dark"><?= $k['tipe'] ?></span></p>
                <p class="text-muted mb-3"><i class="bi bi-stars"></i> <?= $k['fasilitas'] ?></p>
                
                <?php if (!$punya_kamar): ?>
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPesan<?= $k['id'] ?>">
                    <i class="bi bi-cart-plus"></i> Pesan Kamar
                </button>
                <?php else: ?>
                <button class="btn btn-secondary w-100" disabled><i class="bi bi-check-circle"></i> Sudah Punya Kamar</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Pesan -->
    <div class="modal fade" id="modalPesan<?= $k['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Pesan Kamar <?= $k['nomor_kamar'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="kamar_id" value="<?= $k['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Bawa penghasil tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Ajukan Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    
    <?php if ($kamars->num_rows == 0): ?>
    <div class="col-12">
        <div class="alert alert-secondary text-center">Tidak ada kamar tersedia saat ini.</div>
    </div>
    <?php endif; ?>
</div>

<?php
// Simpan buffer ke variabel — PASTIKAN tidak ada output setelah ini
$content = ob_get_clean();

// Include layout SEKALI di akhir
include 'layout.php';
?>