<?php

function ConsultarMaterialesConStock($id_almacen, $id_ubicacion, $busqueda = '')
{
    include("../_conexion/conexion.php");

    // Construir condición de búsqueda
    $search_condition = "";
    if (!empty($busqueda)) {
        $busqueda_safe = mysqli_real_escape_string($con, $busqueda);
        $search_condition = " AND (p.cod_material LIKE '%$busqueda_safe%' 
                                OR p.nom_producto LIKE '%$busqueda_safe%' 
                                OR mt.nom_material_tipo LIKE '%$busqueda_safe%')";
    }

    $sql = "SELECT 
        p.id_producto,
        p.cod_material,
        p.nom_producto,
        mt.nom_material_tipo,
        um.nom_unidad_medida,
        COALESCE(
            SUM(CASE
                WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                ELSE 0
            END), 0
        ) AS stock_fisico,
        COALESCE(
            SUM(CASE
                WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 5 AND mov.est_movimiento = 1 
                THEN mov.cant_movimiento
                ELSE 0
            END), 0
        ) AS stock_comprometido
    FROM producto p
    INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
    INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
    LEFT JOIN movimiento mov ON p.id_producto = mov.id_producto
        AND mov.id_almacen = $id_almacen
        AND mov.id_ubicacion = $id_ubicacion
        AND mov.est_movimiento != 0
    WHERE p.est_producto = 1
      AND p.id_producto_tipo = 1  -- Solo materiales
      $search_condition
    GROUP BY p.id_producto
    HAVING (stock_fisico - stock_comprometido) > 0
    ORDER BY p.nom_producto ASC";

    $resc = mysqli_query($con, $sql);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

?>