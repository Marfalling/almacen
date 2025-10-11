<?php
$bd_complemento = "arceperu"; // Nombre de la base de datos complementaria
$con = mysqli_connect("localhost", "root", "", "arcealmacen", 3306); 

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (!$con->set_charset("utf8")) {
    die("Error al establecer charset: " . $con->error);
}
?>
