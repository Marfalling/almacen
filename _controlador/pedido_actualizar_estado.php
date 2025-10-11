<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//-----------------------------------------------------------------------
// CONTROLADOR: pedido_actualizar_estado.php
//-----------------------------------------------------------------------
require_once(__DIR__ . "/../_modelo/m_movimientos.php");
require_once(__DIR__ . "/../_modelo/m_pedidos.php");

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

        $pedido_detalle = ConsultarPedidoDetalle($id_pedido);

        foreach ($pedido_detalle as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = floatval($item['cant_pedido_detalle']);
            $id_almacen = intval($item['id_almacen'] ?? 1);
            $id_ubicacion = intval($item['id_ubicacion'] ?? 1);

            // Verificar stock disponible antes de registrar
            $stock = ObtenerStockProducto($id_producto, $id_almacen, $id_ubicacion);
            $stock_disponible = floatval($stock['stock_disponible']);

            if ($stock_disponible >= $cantidad) {
                $cantidad_reservar = $cantidad;
            } elseif ($stock_disponible > 0 && $stock_disponible < $cantidad) {
                $cantidad_reservar = $stock_disponible;
            } else {
                $cantidad_reservar = 0;
            }

            if ($cantidad_reservar > 0) {
                RegistrarMovimientoPedido(
                    $id_pedido,
                    $id_producto,
                    $id_almacen,
                    $id_ubicacion,
                    $cantidad_reservar
                );
                
            }
        }
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