<?php

include '../config.php';


if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION['user']['role'] !== 'admin') {

    header("Location: ../index.php");
    exit;

}


/*
 * Update status pesanan
 */

if (isset($_POST['update_status'])) {

    $id = intval($_POST['id']);

    $status = $_POST['status'];


    $status_valid = [
        'Pending',
        'Diproses',
        'Dikirim',
        'Selesai'
    ];


    if (in_array($status, $status_valid)) {

        $status = mysqli_real_escape_string(
            $conn,
            $status
        );


        mysqli_query(
            $conn,
            "UPDATE orders
             SET status='$status'
             WHERE id=$id"
        );

    }

}


/*
 * Ambil semua pesanan
 */

$result = mysqli_query(
    $conn,
    "SELECT *
     FROM orders
     ORDER BY id DESC"
);

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
        Pesanan - PizzaKu
    </title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


<nav class="navbar">

    <div class="logo">
        <!-- KODE BARU -->
<div class="logo" style="display: flex; align-items: center;">
    <img src="../assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" style="height: 40px; margin-right: 10px;">
    <span style="font-size: 20px; font-weight: bold; color: #d32f2f;">Pizza Gaza Admin</span>
</div>
    </div>

    <div class="nav-menu">

        <a href="index.php">
            Dashboard
        </a>

        <a href="menu.php">
            Menu
        </a>

        <a href="pesanan.php">
            Pesanan
        </a>

        <a href="../logout.php">
            Logout
        </a>

    </div>

</nav>


<div class="container">

    <h1>
        📦 Pesanan Pelanggan
    </h1>

    <br>


    <table>

        <tr>

            <th>
                ID
            </th>

            <th>
                Pelanggan
            </th>

            <th>
                No. HP
            </th>

            <th>
                Alamat
            </th>

            <th>
                Total
            </th>

            <th>
                Status
            </th>

            <th>
                Tanggal
            </th>

        </tr>


        <?php while (
            $row = mysqli_fetch_assoc($result)
        ): ?>


        <tr>

            <td>

                <strong>
                    #<?= $row['id']; ?>
                </strong>

            </td>


            <td>

                <?= htmlspecialchars(
                    $row['nama_pelanggan']
                ); ?>

            </td>


            <td>

                <?= htmlspecialchars(
                    $row['no_hp']
                ); ?>

            </td>


            <td>

                <?= nl2br(
                    htmlspecialchars(
                        $row['alamat']
                    )
                ); ?>

            </td>


            <td>

                <strong>

                    <?= rupiah(
                        $row['total']
                    ); ?>

                </strong>

            </td>


            <td>

                <form
                    method="POST"
                    style="
                        padding:0;
                        box-shadow:none;
                    "
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $row['id']; ?>"
                    >


                    <select
                        name="status"
                        onchange="this.form.submit()"
                    >

                        <option
                            value="Pending"
                            <?= $row['status']
                                === 'Pending'
                                ? 'selected'
                                : ''; ?>
                        >
                            Pending
                        </option>


                        <option
                            value="Diproses"
                            <?= $row['status']
                                === 'Diproses'
                                ? 'selected'
                                : ''; ?>
                        >
                            Diproses
                        </option>


                        <option
                            value="Dikirim"
                            <?= $row['status']
                                === 'Dikirim'
                                ? 'selected'
                                : ''; ?>
                        >
                            Dikirim
                        </option>


                        <option
                            value="Selesai"
                            <?= $row['status']
                                === 'Selesai'
                                ? 'selected'
                                : ''; ?>
                        >
                            Selesai
                        </option>

                    </select>


                    <input
                        type="hidden"
                        name="update_status"
                        value="1"
                    >

                </form>

            </td>


            <td>

                <?= $row['created_at']; ?>

            </td>

        </tr>


        <?php endwhile; ?>


    </table>

</div>


</body>

</html>