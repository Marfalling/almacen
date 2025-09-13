<?php
$con = mysqli_connect("localhost", "root", "", "arceperu", 3306); 

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (!$con->set_charset("utf8")) {
    die("Error al establecer charset: " . $con->error);
}
?>
