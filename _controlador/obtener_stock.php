<?php
//=======================================================================
// CONTROLADOR: obtener_stock.php
//=======================================================================
header('Content-Type: application/json');
require_once("../_modelo/m_salidas.php");

// Obtener parámetros
$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : (isset($_GET['id_producto']) ? intval($_GET['id_producto']) : 0);
$id_almacen = isset($_POST['id_almacen']) ? intval($_POST['id_almacen']) : (isset($_GET['id_almacen']) ? intval($_GET['id_almacen']) : 0);
$id_ubicacion = isset($_POST['id_ubicacion']) ? intval($_POST['id_ubicacion']) : (isset($_GET['id_ubicacion']) ? intval($_GET['id_ubicacion']) : 0);

// Validar parámetros
if ($id_producto <= 0 || $id_almacen <= 0 || $id_ubicacion <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Parámetros inválidos',
        'stock' => 0
    ]);
    exit;
}

try {
    // Obtener el stock disponible
    $stock = ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion);
    
    echo json_encode([
        'success' => true,
        'stock' => $stock,
        'id_producto' => $id_producto,
        'id_almacen' => $id_almacen,
        'id_ubicacion' => $id_ubicacion
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener stock: ' . $e->getMessage(),
        'stock' => 0
    ]);
}
?>