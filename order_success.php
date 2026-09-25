<?php

include 'config.php';


if (!isset($_GET['id'])) {

    header("Location: index.php");

    exit;
}


$id = intval($_GET['id']);


$query = mysqli_query(
    $conn,
    "SELECT *
     FROM orders
     WHERE id=$id"
);


$order = mysqli_fetch_assoc($query);


if (!$order) {

    die("Pesanan tidak ditemukan.");

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Pesanan Berhasil - PizzaKu
    </title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🍕 PizzaKu
    </div>

</nav>


<div
    class="container"
    style="max-width:700px"
>


    <div
        class="alert success"
        style="text-align:center"
    >

        <h1>
            🎉
        </h1>

        <h2>
            Pesanan Berhasil!
        </h2>

        <br>


        <p>
            Terima kasih,
            <strong>
                <?= htmlspecialchars(
                    $order['nama_pelanggan']
                ); ?>
            </strong>
        </p>

        <br>


        <p>
            Nomor Pesanan
        </p>

        <h2>
            #<?= $order['id']; ?>
        </h2>

        <br>


        <p>
            Total Pembayaran
        </p>

        <div class="price">

            <?= rupiah($order['total']); ?>

        </div>

        <br>


        <p>
            Status Pesanan
        </p>

        <strong>
            <?= htmlspecialchars(
                $order['status']
            ); ?>
        </strong>

    </div>


    <div style="text-align:center">

        <a
            href="menu.php"
            class="btn"
        >
            🍕 Belanja Lagi
        </a>


        <a
            href="index.php"
            class="btn btn-dark"
        >
            Home
        </a>

    </div>

</div>

</body>

</html>