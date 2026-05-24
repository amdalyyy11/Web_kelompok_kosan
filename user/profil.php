<?php
include_once '../config.php';

$title = 'Profil Saya';
$active = 'profil';

$user_id = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean($_POST['nama_lengkap']);
    $email = clean($_POST['email']);
    $no_hp = clean($_POST['no_hp']);
    
    if (!empty($_POST['password_baru'])) {
        $hash = password_hash($_POST['password_baru'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET nama_lengkap=?, email=?, no_hp=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $email, $no_hp, $hash, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama_lengkap=?, email=?, no_hp=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $email, $no_hp, $user_id);
    }
    $stmt->execute();
    $msg = alert('Profil berhasil diperbarui!', 'success');
    $_SESSION['nama'] = $nama;
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

ob_start();
?>
<h5 class="mb-3"><i class="bi bi-person"></i> Profil Saya</h5>

<?= $msg ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= $user['nama_lengkap'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">No. HP</label>
        <input type="text" name="no_hp" class="form-control" value="<?= $user['no_hp'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password Baru (kosongkan jika tidak diganti)</label>
        <input type="password" name="password_baru" class="form-control" minlength="6">
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
</form>
<?php
$content = ob_get_clean();
include 'layout.php';
?>