<?php
// Llamada a funciones
require_once("_modelo/m_ubicacion.php");

try {
    // Consultar ubicaciones activas
    $ubicaciones = MostrarUbicacionesActivas();

    if (empty($ubicaciones)) {
        $rpta = [
            "status" => "info",
            "message" => "No hay ubicaciones disponibles.",
            "data" => []
        ];
    } else {
        $rpta = [
            "status" => "success",
            "message" => "Ubicaciones obtenidas correctamente.",
            "data" => $ubicaciones
        ];
    }

} catch (Exception $e) {
    error_log("❌ Error en ubicacion_mostrar_.php: " . $e->getMessage());
    $rpta = [
        "status" => "error",
        "message" => "Error al obtener ubicaciones. Intente nuevamente.",
        "data" => []
    ];
}

// Respuesta al app
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rpta, JSON_UNESCAPED_UNICODE);
?>