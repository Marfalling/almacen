<?php
// _modelo/m_anulaciones.php
/**
 * Anula una orden de compra y revierte items del pedido si es necesario
 */
function AnularCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 0){
        mysqli_close($con);
        return false;
    }

    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = '$id_compra'";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_array($res_pedido, MYSQLI_ASSOC);
    $id_pedido = $row_pedido['id_pedido'];

    $sql_productos = "SELECT id_producto FROM compra_detalle 
                      WHERE id_compra = '$id_compra' AND est_compra_detalle = 1";
    $res_productos = mysqli_query($con, $sql_productos);
    
    $productos_en_compra = array();
    while ($row_prod = mysqli_fetch_array($res_productos, MYSQLI_ASSOC)) {
        $productos_en_compra[] = $row_prod['id_producto'];
    }

    if (!empty($productos_en_compra)) {
        foreach ($productos_en_compra as $id_producto) {
            $sql_check_otras = "SELECT COUNT(*) as total 
                               FROM compra_detalle cd
                               INNER JOIN compra c ON cd.id_compra = c.id_compra
                               WHERE cd.id_producto = '$id_producto' 
                               AND c.id_pedido = '$id_pedido'
                               AND c.id_compra != '$id_compra'
                               AND c.est_compra != 0
                               AND cd.est_compra_detalle = 1";
            
            $res_check_otras = mysqli_query($con, $sql_check_otras);
            $row_check_otras = mysqli_fetch_array($res_check_otras, MYSQLI_ASSOC);
            
            if ($row_check_otras['total'] == 0) {
                $sql_revertir = "UPDATE pedido_detalle 
                                SET est_pedido_detalle = 1 
                                WHERE id_pedido = '$id_pedido' 
                                AND id_producto = '$id_producto'
                                AND est_pedido_detalle = 2";
                mysqli_query($con, $sql_revertir);
            }
        }
    }

    $sql_update = "UPDATE compra 
                   SET est_compra = 0
                   WHERE id_compra = '$id_compra'";

    $res_update = mysqli_query($con, $sql_update);

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
    $res1 = AnularCompra($id_compra, $id_personal);
    $res2 = AnularPedido($id_pedido, $id_personal);
    
    return ($res1 && $res2);
}