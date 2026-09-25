<?php
// 1. Sertakan file koneksi database
include 'config.php';

// 2. Cek session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Logika Keranjang (Menggunakan Session)
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

// Hapus item dari keranjang jika ada request hapus
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_hapus = $_GET['id'];
    unset($_SESSION['keranjang'][$id_hapus]);
    header("Location: keranjang.php");
    exit();
}

// Update kuantitas produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $id => $jumlah) {
        if ($jumlah <= 0) {
            unset($_SESSION['keranjang'][$id]);
        } else {
            $_SESSION['keranjang'][$id] = $jumlah;
        }
    }
    header("Location: keranjang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - PizzaKu</title>
    
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
            background-color: #fdf8f5;
            color: #333;
        }

        /* NAVBAR HEADER */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 80px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            color: #d32f2f;
            text-decoration: none;
        }

        /* UBAH / SESUAIKAN UKURAN LOGO DI SINI */
        .navbar .logo img {
            height: 45px; /* Ketinggian logo disesuaikan */
            width: auto;  /* Mempertahankan rasio logo */
            object-fit: contain;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
            list-style: none;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: #444;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar .nav-links a:hover,
        .navbar .nav-links a.active {
            color: #d32f2f;
        }

        /* CONTAINER UTAMA */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* LAYOUT KERANJANG */
        .cart-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        /* TABEL ITEM KERANJANG */
        .cart-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th {
            text-align: left;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            color: #777;
            font-size: 14px;
        }

        .cart-table td {
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
            vertical-align: middle;
        }

        .cart-product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-product-info img {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
        }

        .cart-product-name {
            font-weight: 600;
            color: #222;
            font-size: 15px;
        }

        .cart-product-price {
            color: #888;
            font-size: 13px;
        }

        .input-qty {
            width: 60px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
        }

        .btn-delete {
            color: #e53935;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.2s;
        }

        .btn-delete:hover {
            color: #b71c1c;
        }

        .btn-update {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-top: 15px;
            transition: background 0.3s;
        }

        .btn-update:hover {
            background-color: #e0e0e0;
        }

        /* RINGKASAN BELANJA (SIDEBAR) */
        .summary-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            height: fit-content;
        }

        .summary-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: #666;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #eee;
            font-size: 16px;
            font-weight: 700;
            color: #d32f2f;
        }

        .btn-checkout {
            display: block;
            width: 100%;
            background-color: #d32f2f;
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn-checkout:hover {
            background-color: #b71c1c;
        }

        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }

        .empty-cart i {
            font-size: 50px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 20px;
        }

        .btn-shop {
            background-color: #d32f2f;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        @media (max-width: 850px) {
            .cart-wrapper {
                grid-template-columns: 1fr;
            }
            .navbar {
                padding: 18px 30px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR HEADER LENGKAP DENGAN LOGO GAMBAR -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" onerror="this.onerror=null; this.src='https://via.placeholder.com/45?text=🍕';">
            <span>Pizza Gaza</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php">Produk</a></li>
            <li><a href="keranjang.php" class="active"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a></li>
            <li><a href="pesanan.php">Pesanan Saya</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- CONTAINER UTAMA -->
    <div class="container">
        <h2 class="page-title"><i class="fa-solid fa-cart-shopping" style="color: #d32f2f;"></i> Keranjang Belanja</h2>

        <?php if (!empty($_SESSION['keranjang'])): ?>
            <form action="" method="POST">
                <div class="cart-wrapper">
                    
                    <!-- DAFTAR ITEM KERANJANG -->
                    <div class="cart-card">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_pembayaran = 0;
                                foreach ($_SESSION['keranjang'] as $id => $qty): 
                                    // Ambil detail produk dari database
                                    $query = "SELECT * FROM menu WHERE id = '$id'";
                                    $res   = mysqli_query($conn, $query);

                                    // Mengantisipasi jika nama tabel/id berbeda
                                    if (!$res || mysqli_num_rows($res) == 0) {
                                        $query = "SELECT * FROM menu WHERE id_menu = '$id'";
                                        $res   = mysqli_query($conn, $query);
                                    }

                                    if ($res && $row = mysqli_fetch_assoc($res)):
                                        $nama_pizza = $row['nama_menu'] ?? $row['nama'] ?? $row['nama_pizza'] ?? 'Pizza';
                                        $harga      = $row['harga'] ?? 0;
                                        $gambar     = $row['foto'] ?? $row['gambar'] ?? $row['image'] ?? 'default.jpg';
                                        $subtotal   = $harga * $qty;
                                        $total_pembayaran += $subtotal;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="cart-product-info">
                                                <img src="assets/img/<?= htmlspecialchars($gambar); ?>" 
                                                     alt="<?= htmlspecialchars($nama_pizza); ?>"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/70?text=Pizza';">
                                                <div>
                                                    <div class="cart-product-name"><?= htmlspecialchars($nama_pizza); ?></div>
                                                    <div class="cart-product-price">Rp <?= number_format($harga, 0, ',', '.'); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="qty[<?= $id; ?>]" value="<?= $qty; ?>" min="1" class="input-qty">
                                        </td>
                                        <td style="font-weight: 600; color: #222;">
                                            Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <a href="keranjang.php?action=hapus&id=<?= $id; ?>" class="btn-delete" title="Hapus Item" onclick="return confirm('Hapus pizza ini dari keranjang?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; endforeach; ?>
                            </tbody>
                        </table>

                        <button type="submit" name="update_qty" class="btn-update">
                            <i class="fa-solid fa-rotate"></i> Update Keranjang
                        </button>
                    </div>

                    <!-- RINGKASAN PEMBAYARAN -->
                    <div class="summary-card">
                        <h3 class="summary-title">Ringkasan Belanja</h3>
                        <div class="summary-row">
                            <span>Total Harga Item</span>
                            <span>Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Biaya Pengiriman</span>
                            <span style="color: #2e7d32; font-weight: 500;">Gratis</span>
                        </div>
                        <div class="summary-total">
                            <span>Total Pembayaran</span>
                            <span>Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></span>
                        </div>

                        <a href="checkout.php" class="btn-checkout">
                            Lanjut ke Checkout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                </div>
            </form>

        <?php else: ?>
            <!-- KERANJANG KOSONG -->
            <div class="empty-cart">
                <i class="fa-solid fa-basket-shopping"></i>
                <h3>Keranjang Belanja Kamu Masih Kosong</h3>
                <p>Yuk, pilih pizza favoritmu dan isi keranjang belanjaanmu sekarang!</p>
                <a href="menu.php" class="btn-shop">Lihat Menu Pizza</a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>