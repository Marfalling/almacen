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

    if ($row_check['est_compra'] == 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'warning',
            'mensaje' => 'Esta orden ya está anulada'
        ];
    }

    if (!empty($row_check['id_personal_aprueba']) || 
        !empty($row_check['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular una orden que tiene aprobaciones'
        ];
    }

    $id_pedido = $row_check['id_pedido'];
    $es_servicio = ($row_check['id_producto_tipo'] == 2);

    // 🔹 NUEVO: Obtener los id_pedido_detalle de esta orden ANTES de anularla
    $sql_detalles = "SELECT id_pedido_detalle 
                      FROM compra_detalle 
                      WHERE id_compra = $id_compra 
                      AND est_compra_detalle = 1";
    
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

    // Anular la orden
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

    // 🔹 CORRECCIÓN CRÍTICA: Verificar reapertura por cada detalle específico
    foreach ($detalles_afectados as $id_pedido_detalle) {
        if ($es_servicio) {
            VerificarReaperturaItemServicioPorDetalle($id_pedido_detalle);
        } else {
            VerificarReaperturaItemPorDetalle($id_pedido_detalle);
            //VerificarEstadoItemPorDetalle
        }
    }

    mysqli_close($con);
    
    return [
        'success' => true,
        'tipo_mensaje' => 'success',
        'mensaje' => 'Orden de compra anulada exitosamente'
    ];
}

/**
 * Anula un pedido
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

    // Anular pedido
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

    // Liberar stock comprometido
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
    
    //  No anular si la orden tiene aprobaciones
    $sql_check = "SELECT id_personal_aprueba, id_personal_aprueba_financiera
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
    
    if ($row && (!empty($row['id_personal_aprueba']) || 
                 !empty($row['id_personal_aprueba_financiera']))) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'No se puede anular: la orden tiene aprobaciones'
        ];
    }
    
    // VALIDAR: Anular TODAS las órdenes del pedido
    $sql_ordenes = "SELECT id_compra FROM compra WHERE id_pedido = $id_pedido AND est_compra != 0";
    $res_ordenes = mysqli_query($con, $sql_ordenes);
    
    if (!$res_ordenes) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo_mensaje' => 'error',
            'mensaje' => 'Error al obtener órdenes: ' . mysqli_error($con)
        ];
    }
    
    $todas_anuladas = true;
    $errores = [];
    
    while ($row_orden = mysqli_fetch_assoc($res_ordenes)) {
        $resultado = AnularCompra($row_orden['id_compra'], $id_personal);
        
        if (!$resultado['success']) {
            $todas_anuladas = false;
            $errores[] = "Orden " . $row_orden['id_compra'] . ": " . $resultado['mensaje'];
        }
    }
    
    // Solo anular el pedido si todas las órdenes fueron anuladas
    if ($todas_anuladas) {
        $res_pedido = AnularPedido($id_pedido, $id_personal);
        mysqli_close($con);
        
        if ($res_pedido['success']) {
            return [
                'success' => true,
                'tipo_mensaje' => 'success',
                'mensaje' => 'Orden de compra y pedido anulados exitosamente'
            ];
        } else {
            return $res_pedido;
        }
    }
    
    mysqli_close($con);
    
    return [
        'success' => false,
        'tipo_mensaje' => 'error',
        'mensaje' => 'No se pudieron anular todas las órdenes: ' . implode(', ', $errores)
    ];
}
?>