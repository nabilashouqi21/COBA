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

if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit;
}

$id = intval($_GET['id']);

$result = mysqli_query(
    $conn,
    "SELECT * FROM menu WHERE id = $id"
);

$menu = mysqli_fetch_assoc($result);

if (!$menu) {
    die("Menu tidak ditemukan.");
}

$error = "";

if (isset($_POST['update'])) {

    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    if (
        empty($nama) ||
        empty($deskripsi) ||
        $harga <= 0 ||
        $stok < 0
    ) {

        $error = "Semua data harus diisi dengan benar.";

    } else {

        $nama = mysqli_real_escape_string($conn, $nama);
        $deskripsi = mysqli_real_escape_string($conn, $deskripsi);

        // Jika tidak mengganti foto
        if (
            !isset($_FILES['gambar']) ||
            $_FILES['gambar']['error'] == UPLOAD_ERR_NO_FILE
        ) {

            $query = mysqli_query(
                $conn,
                "UPDATE menu SET
                    nama = '$nama',
                    deskripsi = '$deskripsi',
                    harga = $harga,
                    stok = $stok
                 WHERE id = $id"
            );

        } else {

            $namaFile = $_FILES['gambar']['name'];
            $tmpFile = $_FILES['gambar']['tmp_name'];
            $ukuranFile = $_FILES['gambar']['size'];

            $ext = strtolower(
                pathinfo($namaFile, PATHINFO_EXTENSION)
            );

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {

                $error = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
                $query = false;

            } elseif ($ukuranFile > 5 * 1024 * 1024) {

                $error = "Ukuran foto maksimal 5 MB.";
                $query = false;

            } else {

                $namaGambar = uniqid('pizza_') . '.' . $ext;

                $folderUpload = __DIR__ . '/../assets/img/';

                if (!is_dir($folderUpload)) {
                    mkdir($folderUpload, 0777, true);
                }

                if (
                    move_uploaded_file(
                        $tmpFile,
                        $folderUpload . $namaGambar
                    )
                ) {

                    // Hapus foto lama
                    if (!empty($menu['gambar'])) {

                        $fotoLama =
                            $folderUpload . $menu['gambar'];

                        if (file_exists($fotoLama)) {
                            unlink($fotoLama);
                        }
                    }

                    $namaGambar = mysqli_real_escape_string(
                        $conn,
                        $namaGambar
                    );

                    $query = mysqli_query(
                        $conn,
                        "UPDATE menu SET
                            nama = '$nama',
                            deskripsi = '$deskripsi',
                            harga = $harga,
                            stok = $stok,
                            gambar = '$namaGambar'
                         WHERE id = $id"
                    );

                } else {

                    $error = "Gagal mengupload foto.";
                    $query = false;
                }
            }
        }

        if ($query) {

            header("Location: menu.php");
            exit;

        } elseif ($error == "") {

            $error =
                "Gagal mengubah menu: " .
                mysqli_error($conn);
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

    <title>Edit Pizza - PizzaKu</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>

<nav class="navbar">

    <div class="logo">
        🍕 PizzaKu Admin
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

    </div>

</nav>


<div
    class="container"
    style="max-width:700px"
>

    <h1>
        ✏️ Edit Pizza
    </h1>

    <br>

    <?php if ($error): ?>

        <div class="alert">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <form
        method="POST"
        enctype="multipart/form-data"
    >

        <div class="form-group">

            <label>
                Nama Pizza
            </label>

            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars($menu['nama']); ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Deskripsi
            </label>

            <textarea
                name="deskripsi"
                rows="5"
                required
            ><?= htmlspecialchars($menu['deskripsi']); ?></textarea>

        </div>


        <div class="form-group">

            <label>
                Harga
            </label>

            <input
                type="number"
                name="harga"
                value="<?= $menu['harga']; ?>"
                min="1"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Stok Produk
            </label>

            <input
                type="number"
                name="stok"
                value="<?= $menu['stok']; ?>"
                min="0"
                required
            >

        </div>


        <?php if (!empty($menu['gambar'])): ?>

            <div class="form-group">

                <label>
                    Foto Saat Ini
                </label>

                <br>

                <img
                    src="../assets/img/<?= htmlspecialchars($menu['gambar']); ?>"
                    style="
                        width:200px;
                        height:150px;
                        object-fit:cover;
                        border-radius:12px;
                        margin-top:10px;
                    "
                >

            </div>

        <?php endif; ?>


        <div class="form-group">

            <label>
                Ganti Foto
            </label>

            <input
                type="file"
                name="gambar"
                accept="image/jpeg,image/png,image/webp"
            >

            <small>
                Kosongkan jika tidak ingin mengganti foto.
            </small>

        </div>


        <button
            type="submit"
            name="update"
            class="btn btn-success"
        >
            💾 Simpan Perubahan
        </button>


        <a
            href="menu.php"
            class="btn btn-dark"
        >
            Kembali
        </a>

    </form>

</div>

</body>

</html>