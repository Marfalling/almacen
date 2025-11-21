<?php
// ====================================================================
// MODELO: m_stock.php
// Obtiene el stock físico, comprometido y disponible por ubicación
// ====================================================================

function ObtenerStock($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");

    $id_producto = intval($id_producto);
    $id_almacen = intval($id_almacen);
    $id_ubicacion = intval($id_ubicacion);

    // Validar parámetros
    if ($id_producto <= 0 || $id_almacen <= 0 || $id_ubicacion <= 0) {
        return [
            'fisico' => 0,
            'comprometido' => 0,
            'disponible' => 0
        ];
    }

    // -----------------------------------------------------------
    // 1️⃣ STOCK FÍSICO
    // Entradas (tipo_movimiento = 1)
    // menos Salidas efectivas (tipo_movimiento = 2 y tipo_orden <> 5)
    // -----------------------------------------------------------
    $sql_fisico = "
        SELECT 
            COALESCE(SUM(CASE WHEN tipo_movimiento = 1 THEN cant_movimiento ELSE 0 END), 0)
          - COALESCE(SUM(CASE WHEN tipo_movimiento = 2 AND tipo_orden <> 5 THEN cant_movimiento ELSE 0 END), 0)
        AS stock_fisico
        FROM movimiento
        WHERE id_producto = '$id_producto'
          AND id_almacen = '$id_almacen'
          AND id_ubicacion = '$id_ubicacion'
          AND est_movimiento = 1
    ";

    $res_fisico = mysqli_query($con, $sql_fisico);
    $row_fisico = mysqli_fetch_assoc($res_fisico);
    $stock_fisico = floatval($row_fisico['stock_fisico'] ?? 0);

    // -----------------------------------------------------------
    // 2️⃣ STOCK COMPROMETIDO
    // tipo_orden = 5 (pedido), tipo_movimiento = 2 (salida), est_movimiento = 1 (activo)
    // -----------------------------------------------------------
    $sql_comprometido = "
        SELECT COALESCE(SUM(cant_movimiento), 0) AS stock_comprometido
        FROM movimiento
        WHERE id_producto = '$id_producto'
          AND id_almacen = '$id_almacen'
          AND id_ubicacion = '$id_ubicacion'
          AND tipo_movimiento = 2
          AND tipo_orden = 5
          AND est_movimiento = 1
    ";

    $res_comp = mysqli_query($con, $sql_comprometido);
    $row_comp = mysqli_fetch_assoc($res_comp);
    $stock_comprometido = floatval($row_comp['stock_comprometido'] ?? 0);

    // -----------------------------------------------------------
    // 3️⃣ STOCK DISPONIBLE
    // -----------------------------------------------------------
    $stock_disponible = $stock_fisico - $stock_comprometido;

    mysqli_close($con);

    return [
        'fisico' => $stock_fisico,
        'comprometido' => $stock_comprometido,
        'disponible' => $stock_disponible
    ];
}

function ObtenerStockActual($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");

    $id_producto = intval($id_producto);
    $id_almacen = intval($id_almacen);
    $id_ubicacion = intval($id_ubicacion);

    // Validar parámetros
    if ($id_producto <= 0 || $id_almacen <= 0 || $id_ubicacion <= 0) {
        return 0;
    }

    // -----------------------------------------------------------
    // STOCK FÍSICO
    // Entradas (tipo_movimiento = 1) EXCEPTO devoluciones (tipo_orden = 3)
    // menos Salidas (tipo_movimiento = 2) EXCEPTO devoluciones (tipo_orden = 3)
    // -----------------------------------------------------------
    $sql_fisico = "
        SELECT 
            COALESCE(SUM(CASE 
                WHEN tipo_movimiento = 1 AND tipo_orden <> 3 
                THEN cant_movimiento 
                ELSE 0 
            END), 0)
          - COALESCE(SUM(CASE 
                WHEN tipo_movimiento = 2 AND tipo_orden <> 3 
                THEN cant_movimiento 
                ELSE 0 
            END), 0)
        AS stock_fisico
        FROM movimiento
        WHERE id_producto = '$id_producto'
          AND id_almacen = '$id_almacen'
          AND id_ubicacion = '$id_ubicacion'
          AND est_movimiento = 1
    ";

    $res_fisico = mysqli_query($con, $sql_fisico);
    $row_fisico = mysqli_fetch_assoc($res_fisico);
    $stock_fisico = floatval($row_fisico['stock_fisico'] ?? 0);

    mysqli_close($con);

    return $stock_fisico;
}
?>