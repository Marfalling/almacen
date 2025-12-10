<?php
// Llamada a funciones
require_once("_modelo/m_ubicacion.php");

// Establecer header al inicio
header('Content-Type: application/json; charset=utf-8');

try {
    $ubicaciones = MostrarUbicacionesActivas();

    if (empty($ubicaciones)) {
        // Devolver array vacío en lugar de objeto
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    } else {
        // Devolver directamente el array de ubicaciones
        echo json_encode($ubicaciones, JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    error_log("❌ Error en ubicacion_mostrar.php: " . $e->getMessage());
    // Devolver array vacío en caso de error
    echo json_encode([], JSON_UNESCAPED_UNICODE);
}
?>