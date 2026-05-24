<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Hitung pesanan pending untuk badge
$pending_count = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status='pending'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Admin Dashboard' ?> - KosanNyaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-width: 260px; --primary: #2c3e50; --accent: #e67e22; }
        body { background: #f4f6f9; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--primary); color: white; padding-top: 1rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 4px 12px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--accent); color: white; }
        .sidebar .nav-link i { margin-right: 10px; }
        .main-content { margin-left: var(--sidebar-width); padding: 2rem; }
        .navbar-admin { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-left: var(--sidebar-width); padding: 1rem 2rem; }
        .card-stat { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: transform 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
        .table-container { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .badge-notif { position: absolute; top: 5px; right: 5px; font-size: 0.7rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4 px-3">
            <h4 class="fw-bold"><i class="bi bi-house-heart-fill text-warning"></i> KosanNyaman</h4>
            <small class="text-warning">Panel Admin</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= ($active ?? '') == 'dashboard' ? 'active' : '' ?>" href="index.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link <?= ($active ?? '') == 'pesanan' ? 'active' : '' ?>" href="pesanan.php" style="position:relative;">
                <i class="bi bi-cart"></i> Kelola Pesanan
                <?php if ($pending_count > 0): ?>
                <span class="badge bg-danger rounded-pill badge-notif"><?= $pending_count ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-link <?= ($active ?? '') == 'kamar' ? 'active' : '' ?>" href="kamar.php">
                <i class="bi bi-door-open"></i> Kelola Kamar
            </a>
            <a class="nav-link <?= ($active ?? '') == 'penghuni' ? 'active' : '' ?>" href="penghuni.php">
                <i class="bi bi-people"></i> Kelola Penghuni
            </a>
            <a class="nav-link <?= ($active ?? '') == 'pembayaran' ? 'active' : '' ?>" href="pembayaran.php">
                <i class="bi bi-cash-stack"></i> Pembayaran
            </a>
            <div class="mt-auto pt-4 border-top border-secondary mx-3">
                <a class="nav-link text-danger" href="../logout.php">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </nav>
    </div>

    <div class="navbar-admin d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold"><?= $title ?? 'Dashboard' ?></h5>
        <div class="d-flex align-items-center">
            <span class="me-3"><i class="bi bi-person-circle"></i> <?= $_SESSION['nama'] ?></span>
        </div>
    </div>

    <div class="main-content">
        <?= $content ?? '' ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>