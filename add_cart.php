<?php

include 'config.php';


$id = intval($_POST['id']);

$jumlah = intval($_POST['jumlah']);


if ($jumlah < 1) {
    $jumlah = 1;
}


if (!isset($_SESSION['cart'])) {

    $_SESSION['cart'] = [];

}


if (isset($_SESSION['cart'][$id])) {

    $_SESSION['cart'][$id] += $jumlah;

} else {

    $_SESSION['cart'][$id] = $jumlah;

}


header("Location: cart.php");

exit;

?>