<?php
// Llamada a funciones
require_once("_modelo/m_ubicacion.php");

// Obtener los datos enviados desde Android
$id_usuario = $_POST['id_usuario'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

// Grabar pedido y obtener el ID del pedido
$ubi = GrabarUbicacion($id_usuario, $lat, $lng);

if ($ubi == 0) { // Datos incompletos
    $rpta = ["status" => "error", "message" => "Datos incompletos."];
} elseif ($ubi == -1) { // Error al grabar
    $rpta = ["status" => "error", "message" => "Error al registrar datos. Vuelva a intentarlo."];
} else{
    $rpta = ["status" => "success", "message" => "Registro exitoso."];
}

// Respuesta al app
echo json_encode($rpta);
?>
