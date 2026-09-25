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

$error = "";

if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    // Cek data teks
    if (
        empty($nama) ||
        empty($deskripsi) ||
        $harga <= 0
    ) {

        $error = "Semua data harus diisi dengan benar.";

    } elseif (
        !isset($_FILES['gambar']) ||
        $_FILES['gambar']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = "Foto produk wajib diupload.";

    } else {

        // Ambil informasi file
        $namaFile = $_FILES['gambar']['name'];
        $tmpFile = $_FILES['gambar']['tmp_name'];
        $ukuranFile = $_FILES['gambar']['size'];

        // Ambil ekstensi
        $ext = strtolower(
            pathinfo($namaFile, PATHINFO_EXTENSION)
        );

        // Format yang diperbolehkan
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {

            $error = "Format foto harus JPG, JPEG, PNG, atau WEBP.";

        } elseif ($ukuranFile > 5 * 1024 * 1024) {

            $error = "Ukuran foto maksimal 5 MB.";

        } else {

            // Buat nama file baru agar tidak bentrok
            $namaGambar = uniqid('pizza_') . '.' . $ext;

            // Folder tempat menyimpan gambar
            $folderUpload = __DIR__ . '/../assets/img/';

            // Buat folder jika belum ada
            if (!is_dir($folderUpload)) {
                mkdir($folderUpload, 0777, true);
            }

            // Pindahkan gambar
            if (move_uploaded_file(
                $tmpFile,
                $folderUpload . $namaGambar
            )) {

                // Escape data
                $nama = mysqli_real_escape_string(
                    $conn,
                    $nama
                );

                $deskripsi = mysqli_real_escape_string(
                    $conn,
                    $deskripsi
                );

                $namaGambar = mysqli_real_escape_string(
                    $conn,
                    $namaGambar
                );

                // Simpan ke database
                $query = mysqli_query(
                    $conn,
                    "INSERT INTO menu
                    (
                        nama,
                        deskripsi,
                        harga,
                        gambar,
                        stok
                    )
                    VALUES
                    (
                        '$nama',
                        '$deskripsi',
                        $harga,
                        '$namaGambar',
                        $stok
                    )"
                );

                if ($query) {

                    header("Location: menu.php");
                    exit;

                } else {

                    // Hapus gambar jika database gagal
                    unlink($folderUpload . $namaGambar);

                    $error =
                        "Gagal menambahkan menu: " .
                        mysqli_error($conn);
                }

            } else {

                $error = "Gagal mengupload foto.";
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

    <title>
        Tambah Pizza - PizzaKu
    </title>

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
        ➕ Tambah Pizza
    </h1>

    <br>


    <?php if ($error): ?>

        <div class="alert">

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <form method="POST" enctype="multipart/form-data">


        <div class="form-group">

            <label>
                Nama Pizza
            </label>

            <input
                type="text"
                name="nama"
                placeholder="Contoh: Pizza Sosis"
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
                placeholder="Deskripsi pizza..."
                required
            ></textarea>

        </div>


        <div class="form-group">

            <label>
                Harga
            </label>

            <input
                type="number"
                name="harga"
                placeholder="50000"
                min="1"
                required
            >

        </div>
        <div class="form-group">
    <label>Stok Produk</label>
    <input
        type="number"
        name="stok"
        placeholder="10"
        min="0"
        required
    >
</div>

        <div class="form-group">
    <label for="gambar">Foto Produk</label>

    <input
        type="file"
        id="gambar"
        name="gambar"
        accept="image/jpeg,image/png,image/webp"
        required
    >
</div>


        <button
            type="submit"
            name="simpan"
            class="btn btn-success"
        >
            💾 Simpan
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