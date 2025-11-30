<?php
// ===============================
//  VARIABLES GLOBALES
// ===============================
$bd_complemento        = "arceperu"; // BD del hosting YACHAY
$id_cliente_arce       = 9;
$id_almacen_base_arce  = 1;


// ===============================
//  🔵 CONEXIÓN A BD PRINCIPAL
// ===============================

$host_local = "localhost";
$user_local = "root";
$pass_local = "";
$bd_local   = "arcealmacen";
$port_local = 3306;

$con = mysqli_connect($host_local, $user_local, $pass_local, $bd_local, $port_local);

if (!$con) {
    die("❌ Error de conexión (BD Principal): " . mysqli_connect_error());
}

if (!$con->set_charset("utf8")) {
    die("❌ Error al establecer charset en BD Principal: " . $con->error);
}


// ===============================
//  🟣 CONEXIÓN A BD COMPLEMENTARIA (YACHAY)
// ===============================

$host_comp = "localhost"; // O IP del servidor Yachay
$user_comp = "root";             // <-- Cambiar
$pass_comp = "";            // <-- Cambiar
$bd_comp   = $bd_complemento;                    // Usa tu variable global
$port_comp = 3306;                                // Yachay usa 3306

$con_comp = mysqli_connect($host_comp, $user_comp, $pass_comp, $bd_comp, $port_comp);

if (!$con_comp) {
    die("❌ Error de conexión (BD Complemento - Yachay): " . mysqli_connect_error());
}

if (!$con_comp->set_charset("utf8")) {
    die("❌ Error al establecer charset en BD Complemento: " . $con_comp->error);
}

?>

