<?php
// Llamada a funciones
require_once("_modelo/m_producto.php");

// Establecer header al inicio
header('Content-Type: application/json; charset=utf-8');

try {
    // Obtener parámetros GET
    $id_almacen = isset($_GET['id_almacen']) ? (int)$_GET['id_almacen'] : 0;
    $id_ubicacion = isset($_GET['id_ubicacion']) ? (int)$_GET['id_ubicacion'] : 0;

    // Validar parámetros
    if ($id_almacen <= 0 || $id_ubicacion <= 0) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obtener materiales con stock
    $materiales = ConsultarMaterialesConStock($id_almacen, $id_ubicacion);

    if (empty($materiales)) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($materiales, JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    error_log("❌ Error en material_mostrar_stock.php: " . $e->getMessage());
    echo json_encode([], JSON_UNESCAPED_UNICODE);
}
?>