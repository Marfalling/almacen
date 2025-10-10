<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_pedidos.php");

header('Content-Type: application/json');

// --- Seguridad básica ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
$accion = $_POST['accion'] ?? '';

if (!$id_pedido || !$accion) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

// --------------------------------------------------------------
// 🔹 Solo permitir acciones seguras (sin actualizar estados)
// --------------------------------------------------------------
if ($accion === 'check_stock') {
    // Verificar si todos los items tienen stock suficiente
    $resultado = verificarPedidoListo($id_pedido); // Esta función NO actualiza el estado

    echo json_encode([
        'success' => $resultado['listo'] ?? false,
        'message' => $resultado['mensaje'] ?? 'Verificación completada',
        'detalles' => $resultado['items'] ?? null
    ]);
    exit;
}

// --------------------------------------------------------------
// 🚫 Deshabilitar el completado automático
// --------------------------------------------------------------
if ($accion === 'completar_automatico') {
    echo json_encode([
        'success' => false,
        'message' => 'Esta acción ha sido deshabilitada. El pedido solo se marca como FINALIZADO cuando se registra la salida en almacén.'
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida']);

?>