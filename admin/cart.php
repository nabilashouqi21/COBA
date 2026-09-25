<?php
include '../config.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Mengambil data keranjang dari Session
$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Pelanggan - Pizza Gaza Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .table-cart {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .table-cart th, .table-cart td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .table-cart th {
            background-color: #d32f2f;
            color: #ffffff;
            font-weight: bold;
        }
        .table-cart tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <img src="../assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" style="height: 40px; vertical-align: middle; margin-right: 8px;" onerror="this.style.display='none'">
        <span>Pizza Gaza Admin</span>
    </div>
    <div class="nav-menu">
        <a href="index.php">Dashboard</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php" style="color: #d32f2f; font-weight: bold;">Keranjang</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="../index.php">Website</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container" style="margin-top: 30px;">
    <h1>🛒 Keranjang Belanja Pelanggan</h1>
    <p>Daftar item pizza yang saat ini ada di dalam keranjang aktif.</p>

    <table class="table-cart">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pizza</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_seluruh = 0;

            if (!empty($cart)) {
                foreach ($cart as $id => $jumlah) {
                    $id = intval($id);
                    $query = mysqli_query($conn, "SELECT * FROM menu WHERE id = $id");
                    $item = mysqli_fetch_assoc($query);

                    if ($item) {
                        $subtotal = $item['harga'] * $jumlah;
                        $total_seluruh += $subtotal;
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($item['nama']); ?></strong></td>
                            <td><?= function_exists('rupiah') ? rupiah($item['harga']) : 'Rp ' . number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td><?= $jumlah; ?> pcs</td>
                            <td><strong><?= function_exists('rupiah') ? rupiah($subtotal) : 'Rp ' . number_format($subtotal, 0, ',', '.'); ?></strong></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr style="background-color: #fff3e0;">
                    <td colspan="4" style="text-align: right; font-weight: bold;">Total Keseluruhan:</td>
                    <td><strong style="color: #d32f2f; font-size: 16px;"><?= function_exists('rupiah') ? rupiah($total_seluruh) : 'Rp ' . number_format($total_seluruh, 0, ',', '.'); ?></strong></td>
                </tr>
                <?php
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Saat ini belum ada item di dalam keranjang belanja.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>