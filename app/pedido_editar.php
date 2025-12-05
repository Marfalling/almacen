<?php
// Llamada a funciones
require_once("_modelo/m_pedido.php");

// Obtener los datos enviados desde Android
$id_pedido = $_POST['id_pedido'];
$id_usuario = $_POST['id_usuario'];
$id_vigilante = $_POST['id_vigilante'];
$num = $_POST['num'];
$ot = $_POST['ot'];
$ubi = $_POST['ubi'];
$fec = $_POST['fec'];
$det = $_POST['det'];
$nota = $_POST['nota'];
$act = 2; // Activo desde APP

// Grabar reporte y obtener el ID del pedido
$resp = ActualizarPedido($id_pedido,$id_usuario, $id_vigilante, $num, $ot, $ubi, $fec, $det, $nota, $act);

if ($resp == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Debe completar todos los datos."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al actualizar datos. Vuelva a intentarlo."];
}
else{
    $rpta = ["status" => "success", "message" => "Actualización exitosa."];
}

// Respuesta al app
echo json_encode($rpta);
?>
