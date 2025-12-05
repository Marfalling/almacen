<?php
// Llamada a funciones
require_once("_modelo/m_pedido.php");

// Obtener los datos enviados desde Android
$id_usuario = $_POST['id_usuario'];
$id_vigilante = $_POST['id_vigilante'];
$num = $_POST['num'];
$ot = $_POST['ot'];
$ubi = $_POST['ubi'];
$fec = $_POST['fec'];
$det = $_POST['det'];
$nota = $_POST['nota'];
$act = 2; // Activo desde APP

// Grabar pedido y obtener el ID del pedido
$id_pedido = GrabarPedido($id_usuario, $id_vigilante, $num, $ot, $ubi, $fec, $det, $nota, $act);

if ($id_pedido == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Debe completar todos los datos."];
} elseif ($id_pedido == -1) { // Error al grabar el reporte
    $rpta = ["status" => "error", "message" => "Error al registrar datos. Vuelva a intentarlo."];
} elseif ($id_pedido == -2) { // Error al grabar el reporte
    $rpta = ["status" => "success", "message" => "El pedido ya se registro."];
} else{
    $rpta = ["status" => "success", "message" => "Registro exitoso."];
}

// Respuesta al app
echo json_encode($rpta);
?>
