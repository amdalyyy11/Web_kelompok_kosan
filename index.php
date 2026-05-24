<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosan Nyaman - Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #e67e22;
            --light-bg: #f8f9fa;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .hero-section {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 3rem;
            margin-top: 5rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .btn-custom {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: #d35400;
            transform: translateY(-2px);
            color: white;
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--accent-color);
        }
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
       <div class="d-flex">
    <a href="login.php" class="btn btn-outline-primary me-2">Login Penghuni</a>
    <a href="register.php" class="btn btn-custom me-2">Daftar</a>
    <a href="admin/login.php" class="btn btn-dark"><i class="bi bi-shield-lock"></i> Admin</a>
        </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-section text-center">
                    <h1 class="display-4 fw-bold mb-4" style="color: var(--primary-color);">
                        Selamat Datang di Kosan Nyaman
                    </h1>
                    <p class="lead text-muted mb-5">
                        Hunian modern dengan fasilitas lengkap, harga terjangkau, dan lokasi strategis. 
                        Solusi terbaik untuk mahasiswa dan pekerja.
                    </p>
                    
                    <div class="row mt-5">
                        <div class="col-md-4 mb-4">
                            <i class="bi bi-wifi feature-icon"></i>
                            <h4 class="mt-3">WiFi High Speed</h4>
                            <p class="text-muted">Internet cepat untuk kerja dan belajar</p>
                        </div>
                        <div class="col-md-4 mb-4">
                            <i class="bi bi-shield-check feature-icon"></i>
                            <h4 class="mt-3">Keamanan 24 Jam</h4>
                            <p class="text-muted">CCTV dan penjagaan full time</p>
                        </div>
                        <div class="col-md-4 mb-4">
                            <i class="bi bi-droplet feature-icon"></i>
                            <h4 class="mt-3">Air & Listrik</h4>
                            <p class="text-muted">Fasilitas lengkap tanpa ribet</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="login.php" class="btn btn-custom btn-lg me-3">Cari Kamar</a>
                        <a href="register.php" class="btn btn-outline-dark btn-lg">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>