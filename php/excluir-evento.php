<?php
    include "conexao.php";

    if ( isset($_GET["id_evento"]) ) {
        $id_evento = $_GET["id_evento"];

    $sql = "DELETE FROM evento WHERE id_evento=$id_evento";
    $conexao->query($sql);
    }

    header("location: ../agenda.php");
    exit;
?>