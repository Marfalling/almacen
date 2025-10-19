<?php
// _modelo/m_anulaciones.php

// 🔹 IMPORTANTE: Incluir m_pedidos.php al inicio del archivo
require_once("m_pedidos.php");

/**
 * Anula una orden de compra y revierte items del pedido si es necesario
 */
function AnularCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    // Verificar estado actual
    $sql_check = "SELECT c.est_compra, c.id_pedido,
                         c.id_personal_aprueba_tecnica,
                         c.id_personal_aprueba_financiera
                  FROM compra c
                  WHERE c.id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_assoc($res_check);

    if (!$row_check) {
        mysqli_close($con);
        return false;
    }

    // NO PERMITIR ANULAR SI YA ESTÁ ANULADA
    if ($row_check['est_compra'] == 0) {
        mysqli_close($con);
        return false;
    }

    // NO PERMITIR ANULAR SI TIENE APROBACIONES
    if (!empty($row_check['id_personal_aprueba_tecnica']) || 
        !empty($row_check['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return false;
    }

    $id_pedido = $row_check['id_pedido'];

    // OBTENER PRODUCTOS DE ESTA ORDEN
    $sql_productos = "SELECT cd.id_producto 
                      FROM compra_detalle cd
                      WHERE cd.id_compra = '$id_compra' 
                      AND cd.est_compra_detalle = 1";
    $res_productos = mysqli_query($con, $sql_productos);
    
    $productos_en_compra = array();
    while ($row_prod = mysqli_fetch_assoc($res_productos)) {
        $productos_en_compra[] = intval($row_prod['id_producto']);
    }

    // Anular la orden
    $sql_update = "UPDATE compra 
                   SET est_compra = 0
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        // VERIFICAR CADA PRODUCTO PARA REABRIRLO SI ES NECESARIO
        foreach ($productos_en_compra as $id_producto) {
            VerificarReaperturaItem($id_pedido, $id_producto);
        }
    }

    mysqli_close($con);
    return $res_update;
}

/**
 * Anula un pedido
 */
function AnularPedido($id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT est_pedido FROM pedido WHERE id_pedido = '$id_pedido'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_pedido'] == 0) {
        mysqli_close($con);
        return false;
    }

    // Anular pedido
    $sql_update = "UPDATE pedido 
                   SET est_pedido = 0 
                   WHERE id_pedido = '$id_pedido'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        // Liberar stock comprometido
        $sql_mov = "UPDATE movimiento 
                    SET est_movimiento = 0
                    WHERE id_orden = '$id_pedido' 
                      AND tipo_orden = 5 
                      AND est_movimiento = 1";
        mysqli_query($con, $sql_mov);
    }

    mysqli_close($con);
    return $res_update;
}

/**
 * Anula una compra y su pedido asociado
 */
function AnularCompraPedido($id_compra, $id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");
    
    // VALIDAR: No anular si la orden tiene aprobaciones
    $sql_check = "SELECT id_personal_aprueba_tecnica, id_personal_aprueba_financiera
                  FROM compra 
                  WHERE id_compra = $id_compra";
    $res = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_assoc($res);
    
    if ($row && (!empty($row['id_personal_aprueba_tecnica']) || 
                 !empty($row['id_personal_aprueba_financiera']))) {
        mysqli_close($con);
        return false;
    }
    
    // VALIDAR: Anular TODAS las órdenes del pedido
    $sql_ordenes = "SELECT id_compra FROM compra WHERE id_pedido = $id_pedido AND est_compra != 0";
    $res_ordenes = mysqli_query($con, $sql_ordenes);
    
    $todas_anuladas = true;
    while ($row_orden = mysqli_fetch_assoc($res_ordenes)) {
        if (!AnularCompra($row_orden['id_compra'], $id_personal)) {
            $todas_anuladas = false;
        }
    }
    
    // Solo anular el pedido si todas las órdenes fueron anuladas
    if ($todas_anuladas) {
        $res_pedido = AnularPedido($id_pedido, $id_personal);
        mysqli_close($con);
        return $res_pedido;
    }
    
    mysqli_close($con);
    return false;
}