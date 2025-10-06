<?php
$con_comp = mysqli_connect("localhost", "root", "", "arceperucomplemento", 3306);

if (!$con_comp) {
    die("Error de conexión (arcecomplemento): " . mysqli_connect_error());
}

if (!$con_comp->set_charset("utf8")) {
    die("Error al establecer charset: " . $con_comp->error);
}
?>
