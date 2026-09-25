<?php

include 'config.php';


if (isset($_POST['jumlah'])) {

    foreach ($_POST['jumlah'] as $id => $jumlah) {

        $id = intval($id);

        $jumlah = intval($jumlah);


        if ($jumlah <= 0) {

            unset($_SESSION['cart'][$id]);

        } else {

            $_SESSION['cart'][$id] = $jumlah;

        }

    }

}


header("Location: cart.php");

exit;

?>