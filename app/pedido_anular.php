<?php
// Llamada a funciones
require_once("_modelo/m_pedido.php");

// Obtener los datos enviados desde Android
$id_pedido = $_POST['id_pedido'];

// Grabar reporte y obtener el ID del reporte
$resp = AnularPedido($id_pedido);

if ($resp == 1) { // Datos incompletos
    $rpta = ["status" => "success", "message" => "Anulado correctamente."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al anular. Vuelva a intentarlo."]; 
} elseif ($resp == 0) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "No se puede anular."];
} 

// Respuesta al app
echo json_encode($rpta);
?>
