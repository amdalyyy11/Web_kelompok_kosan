<?php
include_once '../config.php';

// Kalau sudah login sebagai admin, redirect ke dashboard admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    redirect('index.php');
}

// Kalau sudah login sebagai user, redirect ke dashboard user
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
    redirect('../user/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];
    
    if ($captcha != $_SESSION['captcha_answer']) {
        $error = alert('Captcha salah! Silakan coba lagi.', 'danger');
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama'] = $user['nama_lengkap'];
                redirect('index.php');
            } else {
                $error = alert('Password salah!', 'danger');
            }
        } else {
            $error = alert('Username tidak ditemukan atau bukan akun admin!', 'danger');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - KosanNyaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-box { background: white; border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .form-control { border-radius: 10px; padding: 12px; }
        .btn-login { background: #2c3e50; color: white; border-radius: 10px; padding: 12px; font-weight: 600; width: 100%; }
        .btn-login:hover { background: #1a252f; color: white; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-box">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill text-dark" style="font-size: 3rem;"></i>
                    <h3 class="mt-2 fw-bold">Login Admin</h3>
                    <p class="text-muted">Panel Pengelola <strong>KosanNyaman</strong></p>
                </div>
                
                <?= $error ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person"></i> Username Admin</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-robot"></i> Captcha</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold" style="font-family: monospace;">
                                <?php include '../captcha.php'; ?>
                            </span>
                            <input type="number" name="captcha" class="form-control" placeholder="Jawaban" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login mb-3">Masuk ke Panel</button>
                </form>
                
                <hr>
                <div class="text-center">
                    <small class="text-muted">Login sebagai Penghuni? <a href="../login.php" class="fw-bold text-decoration-none">Klik disini</a></small>
                </div>
                <div class="alert alert-dark small mt-3 mb-0">
                    <strong>Demo:</strong> admin / admin123
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>