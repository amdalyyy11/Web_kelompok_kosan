<?php
include 'config.php';

if (isset($_SESSION['user_id'])) redirect('index.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $nama = clean($_POST['nama_lengkap']);
    $email = clean($_POST['email']);
    $no_hp = clean($_POST['no_hp']);
    $captcha = $_POST['captcha'];
    
    if ($captcha != $_SESSION['captcha_answer']) {
        $msg = alert('Captcha salah!', 'danger');
    } else {
        $check = $conn->query("SELECT id FROM users WHERE username='$username'");
        if ($check->num_rows > 0) {
            $msg = alert('Username sudah digunakan!', 'warning');
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("sssss", $username, $hash, $nama, $email, $no_hp);
            
            if ($stmt->execute()) {
                $msg = alert('Pendaftaran berhasil! Silakan login.', 'success');
            } else {
                $msg = alert('Gagal mendaftar: ' . $conn->error, 'danger');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - KosanNyaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-box {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .form-control { border-radius: 10px; padding: 12px; }
        .btn-register {
            background: #27ae60;
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .btn-register:hover { background: #229954; color: white; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="register-box">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-2 fw-bold">Daftar Akun Baru</h3>
                </div>
                
                <?= $msg ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-robot"></i> Captcha</label>
                        <div class="input-group">
                    <span class="input-group-text bg-light fw-bold" style="font-family: monospace;">
                           <?php include __DIR__ . '/captcha.php'; ?>
                    </span>
                            <input type="number" name="captcha" class="form-control" placeholder="Jawaban" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-register mb-3">Daftar Sekarang</button>
                    <div class="text-center">
                        <small>Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold text-success">Login disini</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>