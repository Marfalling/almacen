<?php
// Llamada a funciones
require_once("_modelo/m_almacen.php");

// MOVER EL HEADER AL INICIO
header('Content-Type: application/json; charset=utf-8');

try {
    $almacenes = MostrarAlmacenesActivos();

    if (empty($almacenes)) {
        // Devolver array vacío en lugar de objeto
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    } else {
        // Devolver directamente el array de almacenes
        echo json_encode($almacenes, JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    error_log("❌ Error en almacen_mostrar.php: " . $e->getMessage());
    // Devolver array vacío en caso de error
    echo json_encode([], JSON_UNESCAPED_UNICODE);
}
?>