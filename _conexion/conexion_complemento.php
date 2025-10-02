<?php
$con_complemento = mysqli_connect("localhost", "root", "", "arceperucomplemento", 3307);

if (!$con_complemento) {
    die("Error de conexión (arcecomplemento): " . mysqli_connect_error());
}

if (!$con_complemento->set_charset("utf8")) {
    die("Error al establecer charset: " . $con_complemento->error);
}
?>
