<?php

include 'config.php';


$cart = $_SESSION['cart'] ?? [];


$total = 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Keranjang - PizzaKu
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

    </div>

</nav>


<div class="container">

    <h2 class="title">
        🛒 Keranjang Belanja
    </h2>


    <?php if (empty($cart)): ?>


        <div class="alert">

            Keranjang kamu masih kosong.

        </div>


        <a
            href="menu.php"
            class="btn"
        >
            🍕 Mulai Belanja
        </a>


    <?php else: ?>


        <form
            action="update_cart.php"
            method="POST"
        >


            <table>

                <tr>

                    <th>
                        Pizza
                    </th>

                    <th>
                        Harga
                    </th>

                    <th>
                        Jumlah
                    </th>

                    <th>
                        Subtotal
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>


                <?php foreach ($cart as $id => $jumlah): ?>


                    <?php

                    $id = intval($id);

                    $query = mysqli_query(
                        $conn,
                        "SELECT * FROM menu WHERE id = $id"
                    );

                    $item = mysqli_fetch_assoc($query);


                    if (!$item) {
                        continue;
                    }


                    $subtotal =
                        $item['harga'] * $jumlah;


                    $total += $subtotal;

                    ?>


                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $item['nama']
                            ); ?>
                        </td>


                        <td>
                            <?= rupiah(
                                $item['harga']
                            ); ?>
                        </td>


                        <td>

                            <input
                                type="number"
                                name="jumlah[<?= $id; ?>]"
                                value="<?= $jumlah; ?>"
                                min="1"
                                style="
                                    width:70px;
                                    padding:8px;
                                "
                            >

                        </td>


                        <td>
                            <?= rupiah($subtotal); ?>
                        </td>


                        <td>

                            <a
                                href="remove_cart.php?id=<?= $id; ?>"
                                class="btn btn-danger"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>


                <?php endforeach; ?>


                <tr>

                    <td colspan="3">

                        <strong>
                            TOTAL
                        </strong>

                    </td>


                    <td colspan="2">

                        <strong class="price">

                            <?= rupiah($total); ?>

                        </strong>

                    </td>

                </tr>


            </table>


            <br>


            <button
                type="submit"
                class="btn"
            >
                🔄 Update Keranjang
            </button>


            <a
                href="checkout.php"
                class="btn btn-success"
            >
                💳 Checkout
            </a>


        </form>


    <?php endif; ?>

</div>


</body>

</html>