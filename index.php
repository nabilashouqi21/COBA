<?php
include 'config.php';

$result = mysqli_query(
    $conn,
    "SELECT * FROM menu ORDER BY id DESC LIMIT 6"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PizzaKu - Toko Pizza</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
<nav class="navbar">

    <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
        <img src="assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" style="height: 35px; width: auto;">
        <span>Pizza Gaza</span>
    </a>

    <div class="nav-menu">

        <a href="index.php">Home</a>

        <a href="menu.php">Menu</a>

        <a href="cart.php">🛒 Keranjang</a>

        <?php if (isset($_SESSION['user'])): ?>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>

        <?php endif; ?>

    </div>

</nav>

<section class="hero">

    <div>

        <h1>Pizza Lezat Setiap Hari 🍕</h1>

        <p>
            Nikmati pizza favoritmu dengan topping melimpah
            dan harga bersahabat.
        </p>

        <a href="menu.php" class="btn">
            🍕 Lihat Menu
        </a>

    </div>

</section>

<div class="container">

    <h2 class="title">
        Menu Favorit
    </h2>

    <div class="products">

        <?php while ($row = mysqli_fetch_assoc($result)): ?>

            <div class="card">

                <div class="card-image">

    <?php
    $gambar = trim($row['gambar'] ?? '');

    $gambar_url = "assets/img/" . $gambar;
    $gambar_file = __DIR__ . "/assets/img/" . $gambar;
    ?>

    <?php if ($gambar !== "" && file_exists($gambar_file)): ?>

        <img
            src="<?= htmlspecialchars($gambar_url); ?>"
            alt="<?= htmlspecialchars($row['nama']); ?>"
        >

    <?php else: ?>

        <div class="no-image">
            🍕
        </div>

    <?php endif; ?>

</div>

                <div class="card-body">

                    <h3>
                        <?= htmlspecialchars($row['nama']); ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($row['deskripsi']); ?>
                    </p>

                    <div class="price">
                        <?= rupiah($row['harga']); ?>
                    </div>

                    <a
                        href="detail.php?id=<?= $row['id']; ?>"
                        class="btn"
                    >
                        Lihat Detail
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>

<footer class="footer">

    <p>
        © <?= date('Y'); ?> Pizza Gaza
    </p>

</footer>

</body>
</html>