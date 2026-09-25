<?php
// 1. Panggil config.php terlebih dahulu
include '../config.php';

// 2. Cek session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Cek autentikasi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Admin Pizza Gaza</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR STYLES */
        .sidebar {
            width: 260px;
            background-color: #1a1d24;
            color: #a0a5b1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 25px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #2a2e39;
        }

        .sidebar-brand img {
            height: 38px;
            width: auto;
        }

        .sidebar-brand div strong {
            color: #ffffff;
            font-size: 18px;
            display: block;
        }

        .sidebar-brand div span {
            font-size: 11px;
            color: #888;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 10px;
            flex: 1;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 18px;
            color: #a0a5b1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu li.active a {
            background-color: #e53935;
            color: #ffffff;
        }

        .sidebar-logout {
            padding: 20px 10px;
            border-top: 1px solid #2a2e39;
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            color: #ef5350;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        /* MAIN CONTENT STYLES */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 30px;
        }

        /* TOPBAR STYLES */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar-title h2 {
            font-size: 24px;
            color: #111;
            margin-bottom: 4px;
        }

        .topbar-title p {
            font-size: 13px;
            color: #777;
        }

        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .topbar-profile .avatar {
            width: 32px;
            height: 32px;
            background-color: #e53935;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        /* ABOUT PAGE STYLES */
        .hero-banner {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            color: white;
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(229, 57, 53, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hero-text h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .hero-text p {
            font-size: 14px;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.6;
        }

        .hero-icon {
            font-size: 70px;
            opacity: 0.8;
        }

        .card-about {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }

        .card-about h3 {
            font-size: 18px;
            color: #111;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .card-about h3 i {
            color: #e53935;
        }

        .card-about p {
            color: #555;
            font-size: 14px;
            line-height: 1.8;
        }

        /* FEATURE GRID */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .feature-item {
            background: #f9fafb;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .feature-item:hover {
            transform: translateY(-3px);
        }

        .feature-item i {
            font-size: 30px;
            color: #e53935;
            margin-bottom: 12px;
        }

        .feature-item h4 {
            font-size: 15px;
            color: #222;
            margin-bottom: 6px;
        }

        .feature-item p {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }

        /* INFO LIST */
        .info-list {
            list-style: none;
        }

        .info-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
            color: #444;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list li i {
            color: #e53935;
            width: 20px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="../assets/logo_pizza_gaza.png" alt="Logo Pizza Gaza" onerror="this.style.display='none'">
                <div>
                    <strong>Pizza Gaza</strong>
                    <span>Admin Panel</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="menu.php"><i class="fa-solid fa-pizza-slice"></i> Kelola Menu</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a></li>
                <li><a href="pesanan.php"><i class="fa-solid fa-box"></i> Pesanan</a></li>
                <li class="active"><a href="about.php"><i class="fa-solid fa-circle-info"></i> Tentang Kami</a></li>
                <li><a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> Website</a></li>
            </ul>
        </div>

        <div class="sidebar-logout">
            <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <h2>Tentang Kami</h2>
                <p>Informasi seputar profil toko dan sistem Pizza Gaza.</p>
            </div>
            <div class="topbar-profile">
                <div class="avatar">A</div>
                <span style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Admin'); ?></span>
            </div>
        </header>

        <!-- HERO BANNER -->
        <div class="hero-banner">
            <div class="hero-text">
                <h1>Selamat Datang di Pizza Gaza 🍕</h1>
                <p>Menyajikan kelezatan pizza otentik dengan bahan-bahan segar berkualitas tinggi dan komitmen penuh terhadap kepuasan rasa setiap pelanggan.</p>
            </div>
            <div class="hero-icon">
                <i class="fa-solid fa-pizza-slice"></i>
            </div>
        </div>

        <!-- TENTANG KAMI -->
        <div class="card-about">
            <h3><i class="fa-solid fa-store"></i> Profil Restoran</h3>
            <p>
                <strong>Pizza Gaza</strong> Di tengah maraknya gerakan mendukung produk lokal, Pizza Gaza hadir membawa angin segar yang memadukan kelezatan kuliner premium dengan misi kemanusiaan. Mengusung tagline "Enjoy Delicious Pizza While Helping Gaza", unit usaha ini berkomitmen menyumbangkan sebagian hasil penjualannya untuk membantu masyarakat di Palestina. Konsep bisnis fundraising berbasis komunitas ini tidak hanya memikat hati konsumen karena aspek kepedulian sosialnya, tetapi juga berhasil mempertahankan loyalitas pelanggan berkat kualitas rasa yang bersaing ketat di pasar.
            </p>
        </div>

        <!-- KEUNGGULAN UTAMA -->
        <div class="card-about">
            <h3><i class="fa-solid fa-star"></i> Nilai & Keunggulan Kami</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <h4>Bahan Segar Berkualitas</h4>
                    <p>Adonan diproses setiap hari tanpa pengawet untuk tekstur yang renyah di luar dan lembut di dalam.</p>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-cheese"></i>
                    <h4>Keju Melimpah</h4>
                    <p>Menggunakan paduan keju mozzarella pilihan yang mulur dan gurih di setiap potongan.</p>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    <h4>Pemesanan Cepat</h4>
                    <p>Sistem pemesanan online terintegrasi langsung untuk mempercepat pengiriman ke tangan Anda.</p>
                </div>

                <div class="feature-item">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                    <h4>Harga Terjangkau</h4>
                    <p>Cita rasa rasa bintang lima dengan penawaran harga hemat bagi seluruh kalangan.</p>
                </div>
            </div>
        </div>

        <!-- KONTAK & LOKASI -->
        <div class="card-about">
            <h3><i class="fa-solid fa-address-book"></i> Informasi Kontak & Operasional</h3>
            <ul class="info-list">
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <span><strong>Alamat Toko:</strong> RT.05/RW.01, Lubang Buaya, Kec. Cipayung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13810.</span>
                </li>
                <li>
                    <i class="fa-solid fa-clock"></i>
                    <span><strong>Jam Operasional:</strong> Setiap Hari (10:00 - 21:00 WIB)</span>
                </li>
                <li>
                    <i class="fa-solid fa-phone"></i>
                    <span><strong>Telepon / WhatsApp:</strong> +62 813-9630-868</span>
                </li>
                <li>
                    <i class="fa-solid fa-envelope"></i>
                    <span><strong>Email Kontak:</strong> info@pizzagaza.com</span>
                </li>
            </ul>
        </div>

    </main>

</body>
</html>