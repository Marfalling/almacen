<?php
//-----------------------------------------------------------------------
// CONTROLADOR: pedido_actualizar_estado.php
//-----------------------------------------------------------------------

require_once('../_modelo/m_pedidos.php');

header('Content-Type: application/json');

error_log("=== INICIO PEDIDO ACTUALIZAR ESTADO ===");

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

// ESTADOS CORRECTOS:
// 0 = Anulado
// 1 = Pendiente
// 2 = Completado (cuando se genera salida automática con stock suficiente)
// 3 = Aprobado
// 4 = Ingresado
// 5 = Finalizado

if ($accion === 'completar_automatico') {
    error_log("Ejecutando completar_automatico para pedido: $id_pedido");
    
    // Esta función actualiza el pedido a estado 2 (Completado) cuando tiene todo el stock
    $resultado = VerificarYActualizarEstadoPedido($id_pedido);
    
    error_log("Resultado de VerificarYActualizarEstadoPedido: " . print_r($resultado, true));
    
    if ($resultado === true) {
        echo json_encode([
            'success' => true,
            'message' => 'Pedido actualizado a estado Completado correctamente'
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
} else if ($accion === 'finalizar_pedido') {
    // Acción para marcar como finalizado (estado 5)
    $resultado = FinalizarPedido($id_pedido);
    
    echo json_encode($resultado);
} else if ($accion === 'check_stock') {
    // Solo verificar sin actualizar estado
    $resultado = verificarPedidoListo($id_pedido);
    
    echo json_encode([
        'success' => $resultado['listo'] ?? false,
        'message' => $resultado['mensaje'] ?? 'Verificación completada',
        'tiene_stock_completo' => $resultado['listo'] ?? false
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}

error_log("=== FIN PEDIDO ACTUALIZAR ESTADO ===");
?>