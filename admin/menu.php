<?php
// 1. Panggil config.php dari folder luar (root)
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
    <title>Kelola Menu - Admin Pizza Gaza</title>
    <!-- FontAwesome Icon -->
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

        /* SIDEBAR STYLES (PERSIS SEPERTI DASHBOARD ADMIN) */
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

        /* HERO BANNER SECTION */
        .hero-banner {
            background-color: #6c6c6c;
            color: white;
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .badge-tag {
            background-color: #ffb74d;
            color: #333;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .hero-banner h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-banner p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* SEARCH BAR SECTION */
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 35px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 30px;
            padding: 6px 8px 6px 20px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .search-box i {
            color: #888;
            margin-right: 10px;
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .search-box button {
            background-color: #e53935;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-box button:hover {
            background-color: #d32f2f;
        }

        /* PIZZA GRID GALLERY */
        .pizza-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .pizza-card {
            background: #000000;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .pizza-card:hover {
            transform: translateY(-5px);
        }

        .pizza-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .pizza-info {
            padding: 15px;
            color: white;
        }

        .pizza-info h3 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .pizza-info p {
            font-size: 13px;
            color: #aaa;
            margin-bottom: 12px;
        }

        .pizza-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price {
            font-size: 16px;
            font-weight: bold;
            color: #ffb74d;
        }

        .btn-add {
            background-color: #e53935;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR ADMIN (SAMA PERSIS DENGAN HIKMAH / DASHBOARD ADMIN) -->
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
                <li class="active"><a href="menu.php"><i class="fa-solid fa-pizza-slice"></i> Kelola Menu</a></li>
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
                <h2>Kelola Menu Pizza</h2>
                <p>Daftar menu pizza yang tersedia di sistem.</p>
            </div>
            <div class="topbar-profile">
                <div class="avatar">A</div>
                <span style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Admin'); ?></span>
            </div>
        </header>

        <!-- HERO BANNER -->
        <div class="hero-banner">
            <span class="badge-tag">✨ Fresh & Delicious</span>
            <h1>Pilih Pizza Favoritmu</h1>
            <p>Pizza lezat dengan topping pilihan, dibuat fresh untuk kamu.</p>
        </div>

        <!-- SEARCH BAR -->
        <div class="search-container">
            <form action="" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="cari" placeholder="Cari pizza..." value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <!-- PIZZA GRID GALLERY -->
        <div class="pizza-grid">
            <!-- Item 1 -->
            <div class="pizza-card">
                <img src="../assets/img/ciken bulgogi.jpeg" alt="Pizza Chicken Bulgogi" class="pizza-img" onerror="this.src='https://via.placeholder.com/300x220?text=Pizza+Supreme'">
                <div class="pizza-info">
                    <h3>Pizza Chicken Bulgogi</h3>
                    <p> Pizza dengan potongan ayam panggang, bawang bombay, dan siraman saus bulgogi khas Korea yang manis gurih..</p>
                    <div class="pizza-bottom">
                        <span class="price">Rp 53.000</span>
                        <a href="#" class="btn-add"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="pizza-card">
                <img src="../assets/img/beef.jpeg" alt="Pizza Beef Smoked" class="pizza-img" onerror="this.src='https://via.placeholder.com/300x220?text=Pizza+Cheese'">
                <div class="pizza-info">
                    <h3>Pizza Beef Smoked</h3>
                    <p>Pizza dengan lembaran daging sapi asap, saus tomat, dan keju mozzarella..</p>
                    <div class="pizza-bottom">
                        <span class="price">Rp 65.000</span>
                        <a href="#" class="btn-add"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </div>
                </div>
            </div>

              <!-- Item 3 -->
            <div class="pizza-card">
                <img src="../assets/img/cheese.jpeg" alt="Pizza Double Cheese" class="pizza-img" onerror="this.src='https://via.placeholder.com/300x220?text=Pizza+Cheese'">
                <div class="pizza-info">
                    <h3>Pizza Cheese</h3>
                    <p>pizza dengan lelehan keju mozzarella dan taburan keju parmesan yang melimpah..</p>
                    <div class="pizza-bottom">
                        <span class="price">Rp 55.000</span>
                        <a href="#" class="btn-add"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </div>
                </div>
            </div>

              <!-- Item 4 -->
            <div class="pizza-card">
                <img src="../assets/img/salmon nori.jpeg" alt="Pizza Salmon Nori" class="pizza-img" onerror="this.src='https://via.placeholder.com/300x220?text=Pizza+Cheese'">
                <div class="pizza-info">
                    <h3>Pizza Salmon Nori</h3>
                    <p>pizza dengan irisan daging salmon, taburan rumput laut kering (nori), dan saus mayot.</p>
                    <div class="pizza-bottom">
                        <span class="price">Rp 50.000</span>
                        <a href="#" class="btn-add"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </div>
                </div>
            </div>

            <!-- Item 5 -->
            <div class="pizza-card">
                <img src="../assets/img/pepperoni.jpeg" alt="Pizza Pepperoni" class="pizza-img" onerror="this.src='https://via.placeholder.com/300x220?text=Pizza+Pepperoni'">
                <div class="pizza-info">
                    <h3>Pizza Pepperoni</h3>
                    <p>Sosis pepperoni sapi pilihan dengan saus khas Pizza Gaza.</p>
                    <div class="pizza-bottom">
                        <span class="price">Rp 60.000</span>
                        <a href="#" class="btn-add"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>