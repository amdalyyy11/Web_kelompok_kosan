<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?> - KosanNyaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #2c3e50; --accent: #e67e22; }
        body { background: #f8f9fa; }
        .navbar-user { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .sidebar-user { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); padding: 1.5rem; }
        .nav-link-user { color: #666; padding: 10px 15px; border-radius: 8px; margin-bottom: 5px; text-decoration: none; display: block; transition: all 0.3s; }
        .nav-link-user:hover, .nav-link-user.active { background: var(--accent); color: white; }
        .nav-link-user i { margin-right: 8px; }
        .main-user { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <nav class="navbar-user navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-house-heart-fill text-warning"></i> KosanNyaman</a>
            <div class="d-flex align-items-center">
                <span class="me-3"><i class="bi bi-person-circle"></i> <?= $_SESSION['nama'] ?></span>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="sidebar-user">
                    <h6 class="text-muted mb-3">MENU USER</h6>
                    <a href="index.php" class="nav-link-user <?= ($active ?? '') == 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="kamar_tersedia.php" class="nav-link-user <?= ($active ?? '') == 'kamar' ? 'active' : '' ?>">
                        <i class="bi bi-door-open"></i> Kamar Tersedia
                    </a>
                    <a href="pesanan_saya.php" class="nav-link-user <?= ($active ?? '') == 'pesanan' ? 'active' : '' ?>">
                        <i class="bi bi-cart"></i> Pesanan Saya
                    </a>
                    <a href="tagihan.php" class="nav-link-user <?= ($active ?? '') == 'tagihan' ? 'active' : '' ?>">
                        <i class="bi bi-receipt"></i> Tagihan Saya
                    </a>
                    <a href="profil.php" class="nav-link-user <?= ($active ?? '') == 'profil' ? 'active' : '' ?>">
                        <i class="bi bi-person"></i> Profil
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="main-user">
                    <?= $content ?? '' ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>