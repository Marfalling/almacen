<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");
require_once("../_modelo/m_salidas.php");

header('Content-Type: application/json');

if (!isset($_POST['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    exit;
}

$accion = $_POST['accion'];

if ($accion === 'obtener_detalle') {
    $id_salida = isset($_POST['id_salida']) ? intval($_POST['id_salida']) : 0;
    
    if ($id_salida <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de salida no válido']);
        exit;
    }
    
    try {
        // Obtener datos de la salida
        $salida_data = ConsultarSalidaPorId($id_salida);
        
        if (empty($salida_data)) {
            echo json_encode(['success' => false, 'message' => 'Salida no encontrada']);
            exit;
        }
        
        // Obtener detalles de la salida
        $detalles = ConsultarSalidaDetalle($id_salida);
        
        echo json_encode([
            'success' => true,
            'salida' => $salida_data[0],
            'detalles' => $detalles
        ]);
        
    } catch (Exception $e) {
        error_log("Error en salida_detalles.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>