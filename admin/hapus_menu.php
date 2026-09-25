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


if (isset($_GET['id'])) {

    $id = intval($_GET['id']);


    mysqli_query(
        $conn,
        "DELETE FROM menu WHERE id=$id"
    );

}


header("Location: menu.php");

exit;

?>