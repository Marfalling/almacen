<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_moneda.php");

header('Content-Type: application/json');

if (!isset($_POST['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    exit;
}

$accion = $_POST['accion'];

if ($accion === 'obtener_detalle') {
    $id_compra = isset($_POST['id_compra']) ? intval($_POST['id_compra']) : 0;
    
    if ($id_compra <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de compra no válido']);
        exit;
    }
    
    try {
        // Obtener datos de la compra
        $compra_data = ConsultarCompra($id_compra);
        if (empty($compra_data)) {
            echo json_encode(['success' => false, 'message' => 'Orden de compra no encontrada']);
            exit;
        }
        
        // Obtener detalles de la compra
        $detalles = ConsultarCompraDetalle($id_compra);
        
        echo json_encode([
            'success' => true,
            'compra' => $compra_data[0],
            'detalles' => $detalles
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>