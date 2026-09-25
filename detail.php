<?php

include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit;
}

$id = intval($_GET['id']);

$query = mysqli_query(
    $conn,
    "SELECT * FROM menu WHERE id = $id"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Pizza tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($data['nama']); ?>
        - PizzaKu
    </title>

    <link rel="stylesheet"
          href="assets/style.css">

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🍕 PizzaKu
    </div>

    <div class="nav-menu">

        <a href="index.php">
            Home
        </a>

        <a href="menu.php">
            Menu
        </a>

        <a href="cart.php">
            🛒 Keranjang
        </a>

    </div>

</nav>


<div class="container">

    <div class="card">

        <div class="card-image">
            🍕
        </div>


        <div class="card-body">

            <h1>
                <?= htmlspecialchars($data['nama']); ?>
            </h1>


            <p>
                <?= htmlspecialchars($data['deskripsi']); ?>
            </p>


            <div class="price">
                <?= rupiah($data['harga']); ?>
            </div>


            <form action="add_cart.php"
                  method="POST">


                <input
                    type="hidden"
                    name="id"
                    value="<?= $data['id']; ?>"
                >


                <div class="form-group">

                    <label>
                        Jumlah Pizza
                    </label>

                    <input
                        type="number"
                        name="jumlah"
                        value="1"
                        min="1"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn"
                >
                    🛒 Tambah ke Keranjang
                </button>


                <a
                    href="menu.php"
                    class="btn btn-dark"
                >
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>


</body>

</html>