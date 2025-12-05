<?php
// Llamada a funciones
require_once("_modelo/m_reporte.php");

// Obtener los datos enviados desde Android
$id_reporte = $_POST['id_reporte'];

// Grabar reporte y obtener el ID del reporte
$resp = AnularReporte($id_reporte);

if ($resp == 1) { // Datos incompletos
    $rpta = ["status" => "success", "message" => "Anulado correctamente."];
} elseif ($resp == -1) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "Error al anular. Vuelva a intentarlo."]; 
} elseif ($resp == 0) { // Error al actualizar el reporte
    $rpta = ["status" => "error", "message" => "No se puede anular.\nTiene pedido o valoración asociada."];
} 

// Respuesta al app
echo json_encode($rpta);
?>
