<?php

include 'config.php';

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $email = mysqli_real_escape_string(
        $conn,
        $email
    );

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users
         WHERE email='$email'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $user = mysqli_fetch_assoc($query);

        /*
         * User baru menggunakan password_hash().
         */

        if (
            password_verify(
                $password,
                $user['password']
            )
        ) {

            $_SESSION['user'] = $user;

            /*
             * Jika admin
             */

            if ($user['role'] === 'admin') {

                header(
                    "Location: admin/index.php"
                );

                exit;
            }

            /*
             * Jika pelanggan
             */

            header("Location: index.php");

            exit;

        } else {

            /*
             * Kompatibilitas dengan admin
             * lama yang masih menggunakan MD5.
             */

            if (
                md5($password) ===
                $user['password']
            ) {

                $_SESSION['user'] = $user;

                if ($user['role'] === 'admin') {

                    header(
                        "Location: admin/index.php"
                    );

                } else {

                    header(
                        "Location: index.php"
                    );
                }

                exit;

            } else {

                $error =
                    "Email atau password salah.";
            }
        }

    } else {

        $error =
            "Email atau password salah.";
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

    <title>Login - PizzaKu</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>

<nav class="navbar">

    <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
    <img src="assets/img/logo_pizza_gaza.jpg.png" alt="Logo Pizza Gaza" style="height: 35px; width: auto;" onerror="this.style.display='none'">
    <span>Pizza Gaza</span>
</a>

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
        Login Pizza Gaza
    </h2>


    <?php if ($error): ?>

        <div class="alert">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

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
            >

        </div>


        <button
            type="submit"
            name="login"
            class="btn"
        >
            Login
        </button>


        <p style="margin-top:20px">

            Belum punya akun?

            <a href="register.php">
                Daftar
            </a>

        </p>

    </form>

</div>

</body>

</html>