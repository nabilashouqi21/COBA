<?php

include 'config.php';


/*
 * Pastikan keranjang tidak kosong.
 */

if (
    !isset($_SESSION['cart']) ||
    empty($_SESSION['cart'])
) {

    header("Location: cart.php");

    exit;
}


/*
 * Pastikan user sudah login.
 */

if (!isset($_SESSION['user'])) {

    $_SESSION['redirect_after_login'] =
        'checkout.php';

    header("Location: login.php");

    exit;
}


$cart = $_SESSION['cart'];

$total = 0;


/*
 * Hitung total keranjang.
 */

foreach ($cart as $id => $jumlah) {

    $id = intval($id);

    $query = mysqli_query(
        $conn,
        "SELECT harga
         FROM menu
         WHERE id=$id"
    );

    $item = mysqli_fetch_assoc($query);

    if ($item) {

        $total +=
            $item['harga'] * $jumlah;
    }
}


$error = "";


/*
 * Proses checkout.
 */

if (isset($_POST['checkout'])) {

    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);


    if (
        empty($nama) ||
        empty($no_hp) ||
        empty($alamat)
    ) {

        $error =
            "Semua data harus diisi.";

    } else {

        $nama = mysqli_real_escape_string(
            $conn,
            $nama
        );

        $no_hp = mysqli_real_escape_string(
            $conn,
            $no_hp
        );

        $alamat = mysqli_real_escape_string(
            $conn,
            $alamat
        );

        $user_id =
            intval($_SESSION['user']['id']);


        /*
         * Simpan pesanan utama.
         */

        $query = mysqli_query(
            $conn,
            "INSERT INTO orders
            (
                user_id,
                nama_pelanggan,
                alamat,
                no_hp,
                total,
                status
            )
            VALUES
            (
                $user_id,
                '$nama',
                '$alamat',
                '$no_hp',
                $total,
                'Pending'
            )"
        );


        if (!$query) {

            die(
                "Gagal membuat pesanan: " .
                mysqli_error($conn)
            );
        }


        /*
         * Ambil ID pesanan.
         */

        $order_id =
            mysqli_insert_id($conn);


        /*
         * Simpan setiap item.
         */

        foreach ($cart as $id => $jumlah) {

            $id = intval($id);

            $jumlah = intval($jumlah);


            $query = mysqli_query(
                $conn,
                "SELECT harga
                 FROM menu
                 WHERE id=$id"
            );

            $item =
                mysqli_fetch_assoc($query);


            if ($item) {

                $harga =
                    intval($item['harga']);


                mysqli_query(
                    $conn,
                    "INSERT INTO order_items
                    (
                        order_id,
                        menu_id,
                        jumlah,
                        harga
                    )
                    VALUES
                    (
                        $order_id,
                        $id,
                        $jumlah,
                        $harga
                    )"
                );
            }
        }


        /*
         * Kosongkan keranjang.
         */

        unset($_SESSION['cart']);


        /*
         * Arahkan ke halaman berhasil.
         */

        header(
            "Location: order_success.php?id=$order_id"
        );

        exit;
    }
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
        Checkout - PizzaKu
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

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>


<div
    class="container"
    style="max-width:700px"
>

    <h2 class="title">
        🧾 Checkout
    </h2>


    <?php if ($error): ?>

        <div class="alert">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <form method="POST">


        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars(
                    $_SESSION['user']['nama']
                ); ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Nomor HP
            </label>

            <input
                type="text"
                name="no_hp"
                placeholder="08123456789"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Alamat Pengiriman
            </label>

            <textarea
                name="alamat"
                rows="5"
                placeholder="Masukkan alamat lengkap..."
                required
            ></textarea>

        </div>


        <div
            style="
                background:#fff3cd;
                padding:20px;
                border-radius:10px;
                margin-bottom:20px;
            "
        >

            <h3>
                Total Pembayaran
            </h3>

            <div
                class="price"
                style="font-size:28px"
            >
                <?= rupiah($total); ?>
            </div>

        </div>


        <button
            type="submit"
            name="checkout"
            class="btn btn-success"
        >
            ✅ Buat Pesanan
        </button>


        <a
            href="cart.php"
            class="btn btn-dark"
        >
            Kembali

        </a>


    </form>

</div>

</body>

</html>