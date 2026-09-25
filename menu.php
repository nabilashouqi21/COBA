<?php
// 1. Sertakan file koneksi database
include 'config.php';

// 2. Cek session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Ambil kata kunci pencarian dari URL
$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';

// 4. Query data menu pizza dari database
// Kami mencakup pencarian untuk beberapa variasi nama kolom (nama_menu, nama, deskripsi)
if (!empty($keyword)) {
    $searchTerm = "%{$keyword}%";
    $query = "SELECT * FROM menu WHERE nama_menu LIKE '$searchTerm' OR nama LIKE '$searchTerm' OR deskripsi LIKE '$searchTerm' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $result = mysqli_query($conn, "SELECT * FROM menu WHERE nama LIKE '$searchTerm' ORDER BY id DESC");
    }
} else {
    $result = mysqli_query($conn, "SELECT * FROM menu ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Pizza - PizzaKu</title>
    <!-- FontAwesome Icon untuk ikon pencarian -->
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

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 80px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: bold;
            color: #d32f2f;
            text-decoration: none;
        }

        .navbar .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: #555;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s;
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

        /* JUDUL HALAMAN */
        .page-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #222;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* SEARCH BAR */
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 35px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 30px;
            padding: 6px 8px 6px 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #d32f2f;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.15);
        }

        .search-box i {
            color: #888;
            margin-right: 10px;
            font-size: 16px;
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            background: transparent;
        }

        .search-box button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background-color: #b71c1c;
        }

        /* GRID PIZZA */
        .pizza-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        @media (max-width: 900px) {
            .pizza-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .navbar {
                padding: 20px 30px;
            }
        }

        @media (max-width: 600px) {
            .pizza-grid {
                grid-template-columns: 1fr;
            }
        }

        .pizza-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .pizza-img-wrapper {
            background-color: #000000;
            height: 220px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .pizza-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pizza-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .pizza-title {
            font-size: 18px;
            font-weight: bold;
            color: #222;
            margin-bottom: 8px;
        }

        .pizza-desc {
            font-size: 13px;
            color: #777;
            line-height: 1.4;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .pizza-price {
            font-size: 16px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 15px;
        }

        .btn-detail {
            background-color: #d32f2f;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn-detail:hover {
            background-color: #b71c1c;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px 20px;
            background: #ffffff;
            border-radius: 12px;
            color: #666;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>

    <!-- NAVBAR HEADER -->
    <nav class="navbar">
        <!-- Ganti bagian logo dengan tag <img> -->
        <a href="index.php" class="logo">
            <img src="assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" style="height: 35px; width: auto;">
            Pizza Gaza
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="keranjang.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- CONTENT UTAMA -->
    <div class="container">
        
        <!-- JUDUL HALAMAN -->
        <h2 class="page-title">
            🍕 Semua Menu Pizza
        </h2>

        <!-- FORM PENCARIAN (SEARCH BAR) -->
        <div class="search-container">
            <form action="" method="GET" class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="cari" placeholder="Cari pizza favoritmu..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <!-- DAFTAR PIZZA GRID -->
        <div class="pizza-grid">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    // Menyesuaikan otomatis nama kolom database
                    $nama_pizza = $row['nama_menu'] ?? $row['nama'] ?? $row['nama_pizza'] ?? 'Menu Pizza';
                    $deskripsi  = $row['deskripsi'] ?? '';
                    $harga      = $row['harga'] ?? 0;
                    $gambar     = $row['foto'] ?? $row['gambar'] ?? $row['image'] ?? 'default.jpg';
                    $id_menu    = $row['id'] ?? $row['id_menu'] ?? 0;
                ?>
                    <div class="pizza-card">
                        <div class="pizza-img-wrapper">
                            <img src="assets/img/<?= htmlspecialchars($gambar); ?>" 
                                 alt="<?= htmlspecialchars($nama_pizza); ?>"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/300x220?text=Pizza';">
                        </div>
                        <div class="pizza-info">
                            <h3 class="pizza-title"><?= htmlspecialchars($nama_pizza); ?></h3>
                            <p class="pizza-desc"><?= htmlspecialchars($deskripsi); ?></p>
                            <div class="pizza-price">Rp <?= number_format($harga, 0, ',', '.'); ?></div>
                            <a href="detail.php?id=<?= $id_menu; ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Pizza dengan kata kunci "<strong><?= htmlspecialchars($keyword) ?></strong>" tidak ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>