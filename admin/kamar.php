<?php
include_once '../config.php';

$title = 'Kelola Kamar';
$active = 'kamar';
$msg = '';

// Folder upload
$upload_dir = __DIR__ . '/../assets/uploads/kamar/';
$upload_url = '../assets/uploads/kamar/';

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $old = $conn->query("SELECT gambar FROM kamar WHERE id=$id")->fetch_assoc();
    if ($old && $old['gambar'] && file_exists(__DIR__ . '/../' . $old['gambar'])) {
        unlink(__DIR__ . '/../' . $old['gambar']);
    }
    $conn->query("DELETE FROM kamar WHERE id=$id");
    $msg = alert('Kamar berhasil dihapus!', 'success');
}

// Proses Simpan (Tambah/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $nomor = clean($_POST['nomor_kamar']);
    $tipe = clean($_POST['tipe']);
    $harga = (float)$_POST['harga_bulanan'];
    $fasilitas = clean($_POST['fasilitas']);
    $status = clean($_POST['status']);
    
    $gambar_path = '';
    $upload_error = '';
    
    // Handle upload gambar
    if (!empty($_FILES['gambar']['tmp_name'])) {
        $file = $_FILES['gambar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($ext, $allowed)) {
            $upload_error = 'Format gambar harus JPG, PNG, atau WEBP!';
        } elseif ($file['size'] > $max_size) {
            $upload_error = 'Ukuran gambar maksimal 2MB!';
        } else {
            $new_name = 'kamar_' . uniqid() . '.' . $ext;
            $target = $upload_dir . $new_name;
            
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $gambar_path = 'assets/uploads/kamar/' . $new_name;
                
                // Hapus gambar lama kalau edit
                if ($id) {
                    $old = $conn->query("SELECT gambar FROM kamar WHERE id=" . (int)$id)->fetch_assoc();
                    if ($old && $old['gambar'] && file_exists(__DIR__ . '/../' . $old['gambar'])) {
                        unlink(__DIR__ . '/../' . $old['gambar']);
                    }
                }
            } else {
                $upload_error = 'Gagal upload gambar.';
            }
        }
    }
    
    // Kalau tidak ada error upload, lanjut simpan DB
    if (empty($upload_error)) {
        if ($id) {
            if ($gambar_path) {
                $stmt = $conn->prepare("UPDATE kamar SET nomor_kamar=?, tipe=?, harga_bulanan=?, fasilitas=?, status=?, gambar=? WHERE id=?");
                $stmt->bind_param("ssdsssi", $nomor, $tipe, $harga, $fasilitas, $status, $gambar_path, $id);
            } else {
                $stmt = $conn->prepare("UPDATE kamar SET nomor_kamar=?, tipe=?, harga_bulanan=?, fasilitas=?, status=? WHERE id=?");
                $stmt->bind_param("ssdssi", $nomor, $tipe, $harga, $fasilitas, $status, $id);
            }
            $msg = alert('Kamar berhasil diupdate!', 'success');
        } else {
            if (empty($gambar_path)) {
                $msg = alert('Gambar kamar wajib diupload untuk kamar baru!', 'danger');
            } else {
                $stmt = $conn->prepare("INSERT INTO kamar (nomor_kamar, tipe, harga_bulanan, fasilitas, status, gambar) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsss", $nomor, $tipe, $harga, $fasilitas, $status, $gambar_path);
                $msg = alert('Kamar berhasil ditambahkan!', 'success');
            }
        }
        if (empty($msg) || strpos($msg, 'alert-success') !== false) {
            if (isset($stmt)) $stmt->execute();
        }
    } else {
        $msg = alert($upload_error, 'danger');
    }
}

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="m-0">Data Seluruh Kamar</h5>
    <button class="btn btn-primary" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#modalKamar">
        <i class="bi bi-plus-lg"></i> Tambah Kamar
    </button>
</div>

<?= $msg ?>

<div class="table-container">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Nomor</th>
                <th>Tipe</th>
                <th>Harga/Bulan</th>
                <th>Fasilitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $conn->query("SELECT * FROM kamar ORDER BY nomor_kamar");
            $no = 1;
            while ($row = $q->fetch_assoc()):
                $badge = $row['status'] == 'tersedia' ? 'success' : ($row['status'] == 'terisi' ? 'danger' : 'warning');
                $img = $row['gambar'] ? '../' . $row['gambar'] : '../assets/img/no-image.png';
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <img src="<?= $img ?>" alt="<?= $row['nomor_kamar'] ?>" style="width:80px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                </td>
                <td><strong><?= $row['nomor_kamar'] ?></strong></td>
                <td><?= $row['tipe'] ?></td>
                <td>Rp<?= number_format($row['harga_bulanan'], 0, ',', '.') ?></td>
                <td><small><?= $row['fasilitas'] ?></small></td>
                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                <td>
                    <button class="btn btn-sm btn-warning" 
                        onclick='editKamar(<?= json_encode($row) ?>)' 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalKamar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kamar ini? Gambar juga akan terhapus.')">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Form (Satu Modal untuk Tambah & Edit) -->
<div class="modal fade" id="modalKamar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formKamar">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kamar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="inputId" value="">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Kamar</label>
                            <input type="text" name="nomor_kamar" id="inputNomor" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select name="tipe" id="inputTipe" class="form-select" required>
                                <option value="Standard">Standard</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Bulanan (Rp)</label>
                            <input type="number" name="harga_bulanan" id="inputHarga" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="inputStatus" class="form-select">
                                <option value="tersedia">Tersedia</option>
                                <option value="terisi">Terisi</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="fasilitas" id="inputFasilitas" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Kamar</label>
                        <input type="file" name="gambar" id="inputGambar" class="form-control" accept="image/png, image/jpeg, image/webp">
                        <div class="form-text">
                            Format: JPG, PNG, WEBP. Maksimal 2MB.
                            <span id="infoGambar" class="d-block mt-1"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('formKamar').reset();
    document.getElementById('inputId').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Kamar';
    document.getElementById('infoGambar').innerHTML = '';
    document.getElementById('inputGambar').required = true;
}

function editKamar(data) {
    document.getElementById('inputId').value = data.id;
    document.getElementById('inputNomor').value = data.nomor_kamar;
    document.getElementById('inputTipe').value = data.tipe;
    document.getElementById('inputHarga').value = data.harga_bulanan;
    document.getElementById('inputStatus').value = data.status;
    document.getElementById('inputFasilitas').value = data.fasilitas;
    document.getElementById('modalTitle').innerText = 'Edit Kamar ' + data.nomor_kamar;
    
    // Gambar tidak wajib saat edit
    document.getElementById('inputGambar').required = false;
    
    // Tampilkan info gambar existing
    if (data.gambar) {
        document.getElementById('infoGambar').innerHTML = 
            '<span class="text-success"><i class="bi bi-check-circle"></i> Gambar saat ini: <a href="../' + data.gambar + '" target="_blank">Lihat</a></span>';
    } else {
        document.getElementById('infoGambar').innerHTML = '<span class="text-muted">Belum ada gambar</span>';
    }
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>