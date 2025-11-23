<?php
// _modelo/m_anulaciones.php

//  IMPORTANTE: Incluir m_pedidos.php al inicio del archivo
require_once("m_pedidos.php");

/**
 * Anula una orden de compra y revierte items del pedido si es necesario
 */
function AnularCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);
    
    if ($id_compra <= 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'ID de compra inválido'
        ];
    }

    // ============================================
    // VALIDACIÓN 1: Verificar estado y aprobaciones
    // ============================================
    $sql_check = "SELECT c.est_compra, c.id_pedido,
                         c.id_personal_aprueba,
                         c.id_personal_aprueba_financiera,
                         p.id_producto_tipo
                  FROM compra c
                  INNER JOIN pedido p ON c.id_pedido = p.id_pedido
                  WHERE c.id_compra = $id_compra";
    
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error en consulta: ' . $error
        ];
    }
    
    $row_check = mysqli_fetch_assoc($res_check);

    if (!$row_check) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se encontró la orden de compra con ID: ' . $id_compra
        ];
    }

    // Validar estado
    if ($row_check['est_compra'] == 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'warning',
            'mensaje' => 'Esta orden ya está anulada'
        ];
    }

    // 🔹 VALIDACIÓN CRÍTICA: No anular si tiene aprobaciones
    if (!empty($row_check['id_personal_aprueba']) || 
        !empty($row_check['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular una orden que tiene aprobaciones'
        ];
    }

    //  VALIDACIÓN: No anular si está cerrada (estado 4)
    if ($row_check['est_compra'] == 4) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular una orden cerrada'
        ];
    }

    // ============================================
    // VALIDACIÓN 2: Verificar si tiene ingresos
    // ============================================
    $sql_ingresos = "SELECT COUNT(*) as total
                     FROM ingreso 
                     WHERE id_compra = $id_compra
                     AND est_ingreso != 0";
    
    $res_ingresos = mysqli_query($con, $sql_ingresos);
    $row_ingresos = mysqli_fetch_assoc($res_ingresos);
    
    if (intval($row_ingresos['total']) > 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular una orden que tiene ingresos registrados'
        ];
    }

    $id_pedido = $row_check['id_pedido'];
    $es_servicio = ($row_check['id_producto_tipo'] == 2);

    // ============================================
    // PASO 1: Obtener detalles afectados ANTES de anular
    // ============================================
    $sql_detalles = "SELECT cd.id_pedido_detalle
                      FROM compra_detalle cd
                      WHERE cd.id_compra = $id_compra 
                      AND cd.est_compra_detalle = 1";
    
    $res_detalles = mysqli_query($con, $sql_detalles);
    
    if (!$res_detalles) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al obtener detalles: ' . mysqli_error($con)
        ];
    }
    
    $detalles_afectados = array();
    while ($row_det = mysqli_fetch_assoc($res_detalles)) {
        $detalles_afectados[] = intval($row_det['id_pedido_detalle']);
    }

    error_log(" Detalles afectados por anulación de OC $id_compra: " . implode(', ', $detalles_afectados));

    // ============================================
    // PASO 2: Anular la orden de compra
    // ============================================
    $sql_update = "UPDATE compra 
                   SET est_compra = 0
                   WHERE id_compra = $id_compra";
    
    $res_update = mysqli_query($con, $sql_update);

    if (!$res_update) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al anular la orden: ' . mysqli_error($con)
        ];
    }

    // ============================================
    // PASO 3: Reverificar items afectados
    // ============================================
    error_log(" Reverificando " . count($detalles_afectados) . " items del pedido $id_pedido");
    
    foreach ($detalles_afectados as $id_pedido_detalle) {
        if ($es_servicio) {
            VerificarReaperturaItemServicioPorDetalle($id_pedido_detalle);
        } else {
            VerificarReaperturaItemPorDetalle($id_pedido_detalle);
        }
    }

    // ============================================
    // PASO 4: Actualizar estado del pedido
    // ============================================
    ActualizarEstadoPedidoUnificado($id_pedido, $con);

    mysqli_close($con);
    
    return [
        'success' => true,
        'tipo_mensaje' => 'success',
        'mensaje' => 'Orden de compra anulada exitosamente'
    ];
}

/**
 * Anula un pedido completo
 */
function AnularPedido($id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");

    $id_pedido = intval($id_pedido);
    
    if ($id_pedido <= 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'ID de pedido inválido'
        ];
    }

    // ============================================
    // VALIDACIÓN 1: Verificar estado del pedido
    // ============================================
    $sql_check = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al verificar pedido: ' . mysqli_error($con)
        ];
    }
    
    $row_check = mysqli_fetch_assoc($res_check);

    if (!$row_check) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se encontró el pedido'
        ];
    }

    if ($row_check['est_pedido'] == 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'warning',
            'mensaje' => 'El pedido ya está anulado'
        ];
    }

    // ============================================
    // VALIDACIÓN 2: Verificar que no tenga órdenes activas
    // ============================================
    $sql_ordenes_activas = "SELECT COUNT(*) as total 
                            FROM compra 
                            WHERE id_pedido = $id_pedido 
                            AND est_compra != 0";
    $res_ordenes = mysqli_query($con, $sql_ordenes_activas);
    $row_ordenes = mysqli_fetch_assoc($res_ordenes);
    
    if (intval($row_ordenes['total']) > 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular el pedido: tiene órdenes de compra activas'
        ];
    }

    // ============================================
    // VALIDACIÓN 3: Verificar que no tenga salidas activas
    // ============================================
    $sql_salidas_activas = "SELECT COUNT(*) as total 
                            FROM salida 
                            WHERE id_pedido = $id_pedido 
                            AND est_salida != 0";
    $res_salidas = mysqli_query($con, $sql_salidas_activas);
    $row_salidas = mysqli_fetch_assoc($res_salidas);
    
    if (intval($row_salidas['total']) > 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular el pedido: tiene órdenes de salida activas'
        ];
    }

    // ============================================
    // PASO 1: Anular el pedido
    // ============================================
    $sql_update = "UPDATE pedido 
                   SET est_pedido = 0 
                   WHERE id_pedido = $id_pedido";
    
    $res_update = mysqli_query($con, $sql_update);

    if (!$res_update) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al anular pedido: ' . mysqli_error($con)
        ];
    }

    // ============================================
    // PASO 2: Liberar stock comprometido
    // ============================================
    $sql_mov = "UPDATE movimiento 
                SET est_movimiento = 0
                WHERE id_orden = $id_pedido 
                  AND tipo_orden = 5 
                  AND est_movimiento = 1";
    
    mysqli_query($con, $sql_mov);

    mysqli_close($con);
    
    return [
        'success' => true,
        'tipo_mensaje' => 'success',
        'mensaje' => 'Pedido anulado exitosamente'
    ];
}

/**
 * Anula una compra y su pedido asociado
 * (Solo si es la única orden y no hay salidas)
 */
function AnularCompraPedido($id_compra, $id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra);
    $id_pedido = intval($id_pedido);
    
    if ($id_compra <= 0 || $id_pedido <= 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'IDs inválidos'
        ];
    }
    
    // ============================================
    // VALIDACIÓN 1: Verificar aprobaciones de esta orden
    // ============================================
    $sql_check = "SELECT id_personal_aprueba, id_personal_aprueba_financiera, est_compra
                  FROM compra 
                  WHERE id_compra = $id_compra";
    
    $res = mysqli_query($con, $sql_check);
    
    if (!$res) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al verificar orden: ' . mysqli_error($con)
        ];
    }
    
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Orden de compra no encontrada'
        ];
    }

    // No anular si tiene aprobaciones
    if (!empty($row['id_personal_aprueba']) || 
        !empty($row['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular: la orden tiene aprobaciones'
        ];
    }

    // No anular si está cerrada
    if ($row['est_compra'] == 4) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular: la orden está cerrada'
        ];
    }

    // ============================================
    // VALIDACIÓN 2: Verificar que no haya otras órdenes activas
    // ============================================
    $sql_otras_ordenes = "SELECT COUNT(*) as total
                          FROM compra 
                          WHERE id_pedido = $id_pedido 
                          AND id_compra != $id_compra
                          AND est_compra != 0";
    
    $res_otras = mysqli_query($con, $sql_otras_ordenes);
    $row_otras = mysqli_fetch_assoc($res_otras);
    
    if (intval($row_otras['total']) > 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular el pedido: existen otras órdenes de compra activas'
        ];
    }

    // ============================================
    // VALIDACIÓN 3: Verificar que no haya salidas activas
    // ============================================
    $sql_salidas = "SELECT COUNT(*) as total
                    FROM salida 
                    WHERE id_pedido = $id_pedido 
                    AND est_salida != 0";
    
    $res_salidas = mysqli_query($con, $sql_salidas);
    $row_salidas = mysqli_fetch_assoc($res_salidas);
    
    if (intval($row_salidas['total']) > 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular el pedido: tiene órdenes de salida activas'
        ];
    }

    // ============================================
    // PASO 1: Anular la orden de compra
    // ============================================
    $resultado_compra = AnularCompra($id_compra, $id_personal);
    
    if (!$resultado_compra['success']) {
        mysqli_close($con);
        return $resultado_compra;
    }

    // ============================================
    // PASO 2: Anular el pedido
    // ============================================
    $resultado_pedido = AnularPedido($id_pedido, $id_personal);
    
    mysqli_close($con);
    
    if ($resultado_pedido['success']) {
        return [
            'success' => true,
            'tipo_mensaje' => 'success',
            'mensaje' => 'Orden de compra y pedido anulados exitosamente'
        ];
    } else {
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'La orden se anuló pero hubo un error al anular el pedido: ' . $resultado_pedido['mensaje']
        ];
    }
}
?>