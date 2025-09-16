<?php
//=======================================================================
// MODELO: m_salidas.php
//=======================================================================

function GrabarSalida($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                     $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                     $fec_req_salida, $obs_salida, $id_personal_encargado, 
                     $id_personal_recibe, $id_personal, $materiales) 
{
    include("../_conexion/conexion.php");

    // Insertar salida principal
    $sql = "INSERT INTO salida (
                id_material_tipo, id_almacen_origen, id_ubicacion_origen, 
                id_almacen_destino, id_ubicacion_destino, id_personal, 
                id_personal_encargado, id_personal_recibe, ndoc_salida, 
                fec_req_salida, fec_salida, obs_salida, est_salida
            ) VALUES (
                $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                $id_almacen_destino, $id_ubicacion_destino, $id_personal, 
                $id_personal_encargado, $id_personal_recibe, '$ndoc_salida', 
                '$fec_req_salida', NOW(), '$obs_salida', 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_salida = mysqli_insert_id($con);
        
        // Insertar detalles de salida y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // Insertar detalle de salida
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Generar 2 movimientos para el traslado
                
                // 1. Movimiento de SALIDA en almacén origen (resta stock)
                $sql_mov_salida = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                  ) VALUES (
                                    $id_personal, $id_salida, $id_producto, $id_almacen_origen, 
                                    $id_ubicacion_origen, 2, 2, 
                                    $cantidad, NOW(), 1
                                  )";
                mysqli_query($con, $sql_mov_salida);
                
                // 2. Movimiento de INGRESO en almacén destino (suma stock)
                $sql_mov_ingreso = "INSERT INTO movimiento (
                                     id_personal, id_orden, id_producto, id_almacen, 
                                     id_ubicacion, tipo_orden, tipo_movimiento, 
                                     cant_movimiento, fec_movimiento, est_movimiento
                                   ) VALUES (
                                     $id_personal, $id_salida, $id_producto, $id_almacen_destino, 
                                     $id_ubicacion_destino, 2, 1, 
                                     $cantidad, NOW(), 1
                                   )";
                mysqli_query($con, $sql_mov_ingreso);
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}

//-----------------------------------------------------------------------
function MostrarSalidas()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT s.*, 
                mt.nom_material_tipo,
                ao.nom_almacen as nom_almacen_origen,
                ad.nom_almacen as nom_almacen_destino,
                uo.nom_ubicacion as nom_ubicacion_origen,
                ud.nom_ubicacion as nom_ubicacion_destino,
                pr.nom_personal, 
                pr.ape_personal,
                pe.nom_personal as nom_encargado,
                pe.ape_personal as ape_encargado,
                prec.nom_personal as nom_recibe,
                prec.ape_personal as ape_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN personal prec ON s.id_personal_recibe = prec.id_personal
             WHERE s.est_salida = 1
             ORDER BY s.fec_salida DESC";

    $resc = mysqli_query($con, $sqlc);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarSalida($id_salida)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT s.*, 
                mt.nom_material_tipo,
                ao.nom_almacen as nom_almacen_origen,
                ad.nom_almacen as nom_almacen_destino,
                uo.nom_ubicacion as nom_ubicacion_origen,
                ud.nom_ubicacion as nom_ubicacion_destino,
                pr.nom_personal, 
                pr.ape_personal,
                pe.nom_personal as nom_encargado,
                pe.ape_personal as ape_encargado,
                prec.nom_personal as nom_recibe,
                prec.ape_personal as ape_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN personal prec ON s.id_personal_recibe = prec.id_personal
             WHERE s.id_salida = $id_salida";
    
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarSalidaDetalle($id_salida)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT sd.*, 
                p.nom_producto,
                um.nom_unidad_medida,
                COALESCE(
                    (SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    INNER JOIN salida sal ON sd.id_salida = sal.id_salida
                    WHERE mov.id_producto = sd.id_producto 
                    AND mov.id_almacen = sal.id_almacen_origen 
                    AND mov.id_ubicacion = sal.id_ubicacion_origen
                    AND mov.est_movimiento = 1), 0
                ) AS cantidad_disponible_origen
             FROM salida_detalle sd 
             INNER JOIN producto p ON sd.id_producto = p.id_producto
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
             WHERE sd.id_salida = $id_salida AND sd.est_salida_detalle = 1
             ORDER BY sd.id_salida_detalle";
             
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ActualizarSalida($id_salida, $id_almacen_origen, $id_ubicacion_origen,
                         $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                         $fec_req_salida, $obs_salida, $id_personal_encargado, 
                         $id_personal_recibe, $materiales) 
{
    include("../_conexion/conexion.php");

    // Actualizar salida principal
    $sql = "UPDATE salida SET 
                id_almacen_origen = $id_almacen_origen,
                id_ubicacion_origen = $id_ubicacion_origen,
                id_almacen_destino = $id_almacen_destino,
                id_ubicacion_destino = $id_ubicacion_destino,
                ndoc_salida = '$ndoc_salida',
                fec_req_salida = '$fec_req_salida',
                obs_salida = '$obs_salida',
                id_personal_encargado = $id_personal_encargado,
                id_personal_recibe = $id_personal_recibe
            WHERE id_salida = $id_salida";

    if (mysqli_query($con, $sql)) {
        
        // Eliminar movimientos anteriores relacionados con esta salida
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_salida AND tipo_orden = 2";
        mysqli_query($con, $sql_del_mov);
        
        // Eliminar detalles anteriores
        $sql_del_det = "UPDATE salida_detalle SET est_salida_detalle = 0 
                        WHERE id_salida = $id_salida";
        mysqli_query($con, $sql_del_det);
        
        // Insertar nuevos detalles y movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // Insertar nuevo detalle
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Obtener el ID del personal que registra
                $sql_personal = "SELECT id_personal FROM salida WHERE id_salida = $id_salida";
                $res_personal = mysqli_query($con, $sql_personal);
                $row_personal = mysqli_fetch_assoc($res_personal);
                $id_personal = $row_personal['id_personal'];
                
                // Generar nuevos movimientos
                
                // 1. Movimiento de SALIDA en almacén origen (resta stock)
                $sql_mov_salida = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                  ) VALUES (
                                    $id_personal, $id_salida, $id_producto, $id_almacen_origen, 
                                    $id_ubicacion_origen, 2, 2, 
                                    $cantidad, NOW(), 1
                                  )";
                mysqli_query($con, $sql_mov_salida);
                
                // 2. Movimiento de INGRESO en almacén destino (suma stock)
                $sql_mov_ingreso = "INSERT INTO movimiento (
                                     id_personal, id_orden, id_producto, id_almacen, 
                                     id_ubicacion, tipo_orden, tipo_movimiento, 
                                     cant_movimiento, fec_movimiento, est_movimiento
                                   ) VALUES (
                                     $id_personal, $id_salida, $id_producto, $id_almacen_destino, 
                                     $id_ubicacion_destino, 2, 1, 
                                     $cantidad, NOW(), 1
                                   )";
                mysqli_query($con, $sql_mov_ingreso);
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}

//-----------------------------------------------------------------------
function ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT COALESCE(
                SUM(CASE
                    WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0
            ) AS stock_disponible
            FROM movimiento mov
            WHERE mov.id_producto = $id_producto 
            AND mov.id_almacen = $id_almacen 
            AND mov.id_ubicacion = $id_ubicacion
            AND mov.est_movimiento = 1";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return floatval($row['stock_disponible']);
}

?>