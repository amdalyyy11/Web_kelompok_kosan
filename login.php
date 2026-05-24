<?php
include_once 'config.php';

// Kalau sudah login sebagai user, redirect ke dashboard user
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
    redirect('user/index.php');
}

// Kalau sudah login sebagai admin, redirect ke dashboard admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];
    
    if ($captcha != $_SESSION['captcha_answer']) {
        $error = alert('Captcha salah! Silakan coba lagi.', 'danger');
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'user'");
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
                redirect('user/index.php');
            } else {
                $error = alert('Password salah!', 'danger');
            }
        } else {
            $error = alert('Username tidak ditemukan atau bukan akun user!', 'danger');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login User - KosanNyaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-box { background: white; border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        .form-control { border-radius: 10px; padding: 12px; }
        .btn-login { background: #e67e22; color: white; border-radius: 10px; padding: 12px; font-weight: 600; width: 100%; }
        .btn-login:hover { background: #d35400; color: white; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-box">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-2 fw-bold">Login User</h3>
                </div>
                
                <?= $error ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person"></i> Username</label>
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
                                <?php include 'captcha.php'; ?>
                            </span>
                            <input type="number" name="captcha" class="form-control" placeholder="Jawaban" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login mb-3">Masuk</button>
                    <div class="text-center">
                        <small>Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold" style="color:#e67e22">Daftar disini</a></small>
                    </div>
                </form>
                
                <hr>
            
            </div>
        </div>
    </div>
</div>
</body>
</html>