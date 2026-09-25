<?php
// 1. Panggil config.php terlebih dahulu
include '../config.php';

// 2. Jika session belum berjalan di config.php, jalankan secara aman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek autentikasi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fungsi bantu untuk eksekusi query dengan aman jika tabel tidak ditemukan
function safe_query($conn, $sql) {
    try {
        return mysqli_query($conn, $sql);
    } catch (Exception $e) {
        return false;
    }
}

// 1. Hitung Menu
$q_menu = safe_query($conn, "SELECT COUNT(*) as total FROM menu");
$total_menu = $q_menu ? (mysqli_fetch_assoc($q_menu)['total'] ?? 0) : 0;

// 2. Hitung Pelanggan
$q_user = safe_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pelanggan'");
if (!$q_user) {
    $q_user = safe_query($conn, "SELECT COUNT(*) as total FROM users");
}
$total_pelanggan = $q_user ? (mysqli_fetch_assoc($q_user)['total'] ?? 0) : 0;

// 3. Hitung Pesanan
$q_pesanan = safe_query($conn, "SELECT COUNT(*) as total FROM pesanan");
if (!$q_pesanan) {
    $q_pesanan = safe_query($conn, "SELECT COUNT(*) as total FROM orders");
}
$total_pesanan = $q_pesanan ? (mysqli_fetch_assoc($q_pesanan)['total'] ?? 0) : 0;

// 4. Hitung Pendapatan
$q_pendapatan = safe_query($conn, "SELECT SUM(total) as total FROM pesanan WHERE LOWER(status) = 'selesai'");
if (!$q_pendapatan || mysqli_num_rows($q_pendapatan) == 0) {
    $q_pendapatan = safe_query($conn, "SELECT SUM(total_harga) as total FROM orders WHERE LOWER(status) = 'selesai'");
}
$row_pendapatan = $q_pendapatan ? mysqli_fetch_assoc($q_pendapatan) : null;
$total_pendapatan = $row_pendapatan['total'] ?? 0;

// 5. Ambil 5 Pesanan Terbaru
$q_pesanan_terbaru = safe_query($conn, "SELECT * FROM pesanan ORDER BY id DESC LIMIT 5");
if (!$q_pesanan_terbaru) {
    $q_pesanan_terbaru = safe_query($conn, "SELECT * FROM orders ORDER BY id DESC LIMIT 5");
}

// 6. Query untuk mengambil 4 Menu Favorit / Populer
$q_menu_populer = safe_query($conn, "SELECT * FROM menu LIMIT 4");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pizza Gaza</title>
    <!-- FontAwesome & Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* CARDS GRID */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .card-icon.red { background-color: #ffebee; color: #e53935; }
        .card-icon.green { background-color: #e8f5e9; color: #4caf50; }
        .card-icon.blue { background-color: #e3f2fd; color: #2196f3; }
        .card-icon.orange { background-color: #fff3e0; color: #ff9800; }

        .card-info h3 {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .card-info p {
            font-size: 20px;
            font-weight: bold;
            color: #222;
        }

        /* CONTENT GRID */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .panel {
            background: #ffffff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-header h3 {
            font-size: 16px;
            color: #222;
        }

        .panel-header a {
            font-size: 12px;
            color: #e53935;
            text-decoration: none;
            font-weight: 600;
        }

        /* TABLE STYLES */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom th,
        .table-custom td {
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-custom th {
            color: #888;
            font-weight: 600;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending { background-color: #fff8e1; color: #f57f17; }
        .badge-proses { background-color: #e3f2fd; color: #1e88e5; }
        .badge-selesai { background-color: #e8f5e9; color: #388e3c; }

        /* POPULAR GRID */
        .popular-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .popular-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f9fafb;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #f0f0f0;
        }

        .popular-card img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .popular-info h4 {
            font-size: 14px;
            color: #222;
            margin-bottom: 2px;
        }

        .popular-info p {
            font-size: 12px;
            color: #e53935;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
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
                <li class="active"><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="menu.php"><i class="fa-solid fa-pizza-slice"></i> Kelola Menu</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a></li>
                <li><a href="pesanan.php"><i class="fa-solid fa-box"></i> Pesanan</a></li>
                <li><a href="about.php"><i class="fa-solid fa-circle-info"></i> Tentang Kami</a></li>
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
                <h2>Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Admin'); ?>! 👋</h2>
                <p>Berikut ringkasan aktivitas toko pizza hari ini.</p>
            </div>
            <div class="topbar-profile">
                <div class="avatar">A</div>
                <span style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Admin'); ?></span>
            </div>
        </header>

        <!-- STATISTIC CARDS -->
        <section class="cards-grid">
            <div class="card">
                <div class="card-icon red">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="card-info">
                    <h3>Total Pesanan</h3>
                    <p><?= $total_pesanan; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon green">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="card-info">
                    <h3>Total Pendapatan</h3>
                    <p>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="card-info">
                    <h3>Pelanggan</h3>
                    <p><?= $total_pelanggan; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon orange">
                    <i class="fa-solid fa-pizza-slice"></i>
                </div>
                <div class="card-info">
                    <h3>Total Menu</h3>
                    <p><?= $total_menu; ?></p>
                </div>
            </div>
        </section>

        <!-- CONTENT GRID -->
        <section class="content-grid">
            <!-- GRAFIK PENJUALAN -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Penjualan 7 Hari Terakhir</h3>
                </div>
                <canvas id="salesChart" height="200"></canvas>
            </div>

            <!-- PESANAN TERBARU -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Pesanan Terbaru</h3>
                    <a href="pesanan.php">Lihat Semua</a>
                </div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($q_pesanan_terbaru && mysqli_num_rows($q_pesanan_terbaru) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($q_pesanan_terbaru)): ?>
                                <tr>
                                    <td><strong>#P<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan'] ?? $row['nama'] ?? 'Pelanggan'); ?></td>
                                    <td>Rp <?= number_format($row['total'] ?? $row['total_harga'] ?? 0, 0, ',', '.'); ?></td>
                                    <td>
                                        <?php 
                                            $st = $row['status'] ?? 'Proses';
                                            $badgeClass = 'badge-pending';
                                            if ($st == 'Selesai') $badgeClass = 'badge-selesai';
                                            if ($st == 'Diproses' || $st == 'Dikirim') $badgeClass = 'badge-proses';
                                        ?>
                                        <span class="badge-status <?= $badgeClass; ?>"><?= htmlspecialchars($st); ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">Belum ada pesanan terbaru.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MENU POPULER / FAVORIT -->
        <section class="panel">
            <div class="panel-header">
                <h3>Menu Populer / Favorit</h3>
                <a href="menu.php">Lihat Semua</a>
            </div>
            <div class="popular-grid">
                <?php if ($q_menu_populer && mysqli_num_rows($q_menu_populer) > 0): ?>
                    <?php while ($m = mysqli_fetch_assoc($q_menu_populer)): ?>
                        <div class="popular-card">
                            <img src="../assets/img/<?= $m['foto'] ?? $m['gambar'] ?? 'pizza.jpg'; ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3595/3595455.png'" alt="Menu">
                            <div class="popular-info">
                                <h4><?= htmlspecialchars($m['nama'] ?? $m['nama_menu'] ?? 'Pizza'); ?></h4>
                                <p>Rp <?= number_format($m['harga'] ?? 0, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #888; font-size: 13px;">Belum ada data menu favorit.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- SCRIPT CHART.JS -->
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['12/07', '13/07', '14/07', '15/07', '16/07', '17/07', '18/07'],
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: [11, 13, 12, 17, 15, 16, 23],
                    backgroundColor: '#e53935',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>