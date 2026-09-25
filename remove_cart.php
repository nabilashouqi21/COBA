<?php

include 'config.php';


if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    unset($_SESSION['cart'][$id]);

}


header("Location: cart.php");

exit;

?>