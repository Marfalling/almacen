<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");

header('Content-Type: application/json');

// Log para debugging
error_log("=== PEDIDO ACTUALIZAR ESTADO ===");
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

error_log("ID Pedido: $id_pedido");
error_log("Acción: $accion");

if (!$id_pedido || !$accion) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

if ($accion === 'completar_automatico') {
    error_log("Llamando a VerificarYActualizarEstadoPedido para pedido: $id_pedido");
    
    $resultado = VerificarYActualizarEstadoPedido($id_pedido);
    
    error_log("Resultado de VerificarYActualizarEstadoPedido: " . print_r($resultado, true));
    
    if ($resultado === true) {
        echo json_encode([
            'success' => true,
            'message' => 'Pedido actualizado a estado Aprobado correctamente'
        ]);
    } else if (is_array($resultado)) {
        echo json_encode([
            'success' => false,
            'message' => $resultado['error'] ?? 'Error desconocido',
            'detalles' => $resultado
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el pedido'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

error_log("=== FIN PEDIDO ACTUALIZAR ESTADO ===");
?>