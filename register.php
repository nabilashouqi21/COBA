<?php

include 'config.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password !== $konfirmasi) {

        $error = "Konfirmasi password tidak sama.";

    } else {

        $nama = mysqli_real_escape_string($conn, $nama);
        $email = mysqli_real_escape_string($conn, $email);

        $cek = mysqli_query(
            $conn,
            "SELECT id FROM users WHERE email='$email'"
        );

        if (mysqli_num_rows($cek) > 0) {

            $error = "Email sudah terdaftar.";

        } else {

            /*
             * Untuk project belajar.
             * password_hash lebih aman daripada MD5.
             */
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $query = mysqli_query(
                $conn,
                "INSERT INTO users
                (nama, email, password, role)
                VALUES
                ('$nama', '$email', '$password_hash', 'user')"
            );

            if ($query) {

                $success =
                    "Pendaftaran berhasil. Silakan login.";

            } else {

                $error =
                    "Pendaftaran gagal: " .
                    mysqli_error($conn);
            }
        }
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

    <title>Daftar - PizzaKu</title>

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

    </div>

</nav>


<div
    class="container"
    style="max-width:500px"
>

    <h2 class="title">
        Buat Akun
    </h2>


    <?php if ($error): ?>

        <div class="alert">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <?php if ($success): ?>

        <div class="alert success">
            <?= htmlspecialchars($success); ?>

            <br><br>

            <a href="login.php">
                Login sekarang
            </a>
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
                required
            >

        </div>


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                required
                minlength="6"
            >

        </div>


        <div class="form-group">

            <label>
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="konfirmasi"
                required
                minlength="6"
            >

        </div>


        <button
            type="submit"
            name="register"
            class="btn"
        >
            Daftar
        </button>


        <p style="margin-top:20px">

            Sudah punya akun?

            <a href="login.php">
                Login
            </a>

        </p>

    </form>

</div>

</body>

</html>