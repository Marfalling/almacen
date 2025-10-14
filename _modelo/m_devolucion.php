<?php
//=======================================================================
// MODELO: m_devolucion.php
//=======================================================================

function GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $id_cliente_destino, $obs_devolucion, $materiales) 
{
    include("../_conexion/conexion.php");

    // Insertar devolución principal
    $sql = "INSERT INTO devolucion (
                id_almacen, id_ubicacion, id_personal, id_cliente_destino,
                obs_devolucion, fec_devolucion, est_devolucion
            ) VALUES (
                $id_almacen, $id_ubicacion, $id_personal, $id_cliente_destino,
                '$obs_devolucion', NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_devolucion = mysqli_insert_id($con);
        
        // Insertar detalles y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $detalle = mysqli_real_escape_string($con, $material['detalle']);

            // Insertar detalle de devolución
            $sql_detalle = "INSERT INTO devolucion_detalle (
                                id_devolucion, id_producto, cant_devolucion_detalle, 
                                det_devolucion_detalle, est_devolucion_detalle
                            ) VALUES (
                                $id_devolucion, $id_producto, $cantidad, 
                                '$detalle', 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                
                if ($id_cliente_destino == 9) {
                    // DEVOLUCIÓN A ARCE → Genera 2 movimientos (traslado a base)
                    
                    // Movimiento 1: RESTA del almacén origen
                    $sql_mov_resta = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2, 
                                        $cantidad, NOW(), 1
                                    )";
                    mysqli_query($con, $sql_mov_resta);
                    
                    // Movimiento 2: SUMA a ARCE BASE (id_almacen=1, id_ubicacion=1)
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, 1, 
                                        1, 3, 1, 
                                        $cantidad, NOW(), 1
                                    )";
                    mysqli_query($con, $sql_mov_suma);
                    
                } else {
                    // DEVOLUCIÓN A OTRO CLIENTE → Solo resta (sale del sistema)
                    
                    $sql_mov = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                ) VALUES (
                                    $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                    $id_ubicacion, 3, 2, 
                                    $cantidad, NOW(), 1
                                )";
                    mysqli_query($con, $sql_mov);
                }
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}

//-----------------------------------------------------------------------
function MostrarDevoluciones()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT d.*, 
                   a.nom_almacen, 
                   u.nom_ubicacion, 
                   p.nom_personal,
                   c.nom_cliente as nom_cliente_destino
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            /*WHERE d.est_devolucion = 1*/
            ORDER BY d.fec_devolucion DESC";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//----------------------------------------------------------------------
function MostrarDevolucionesFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE DATE(d.fec_devolucion) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where = "WHERE DATE(d.fec_devolucion) = CURDATE()";
    }

    $sql = "SELECT d.*, 
                   a.nom_almacen, 
                   u.nom_ubicacion, 
                   p.nom_personal, 
                   c.nom_cliente as nom_cliente_destino
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            $where
            ORDER BY d.fec_devolucion DESC";

    $res = mysqli_query($con, $sql) or die("Error en consulta: " . mysqli_error($con));
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarDevolucion($id_devolucion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT d.*, 
                   a.nom_almacen, 
                   u.nom_ubicacion, 
                   p.nom_personal,
                   c.nom_cliente as nom_cliente_destino
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            WHERE d.id_devolucion = $id_devolucion";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarDevolucionDetalle($id_devolucion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT dd.*, 
               pr.nom_producto, 
               um.nom_unidad_medida
        FROM devolucion_detalle dd
        INNER JOIN producto pr ON dd.id_producto = pr.id_producto
        INNER JOIN unidad_medida um ON pr.id_unidad_medida = um.id_unidad_medida
        WHERE dd.id_devolucion = $id_devolucion
          AND dd.est_devolucion_detalle = 1
        ORDER BY dd.id_devolucion_detalle";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------

function MostrarMaterialesActivos() {
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM producto 
            WHERE est_producto = 1 
              AND id_producto_tipo = 1";
    $result = mysqli_query($con, $sql);

    $materiales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $materiales[] = $row;
    }
    mysqli_close($con);
    return $materiales;
}

//-----------------------------------------------------------------------
function ActualizarDevolucion($id_devolucion, $id_almacen, $id_ubicacion, $id_cliente_destino,
                                $obs_devolucion, $materiales) 
{
    include("../_conexion/conexion.php");

    // Actualizar devolución principal
    $sql = "UPDATE devolucion SET 
                id_almacen = $id_almacen,
                id_ubicacion = $id_ubicacion,
                id_cliente_destino = $id_cliente_destino,
                obs_devolucion = '$obs_devolucion'
            WHERE id_devolucion = $id_devolucion";

    if (mysqli_query($con, $sql)) {
        
        // Eliminar movimientos anteriores
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_devolucion AND tipo_orden = 3";
        mysqli_query($con, $sql_del_mov);
        
        // Eliminar detalles anteriores
        $sql_del_det = "DELETE FROM devolucion_detalle 
                WHERE id_devolucion = $id_devolucion";
        mysqli_query($con, $sql_del_det);
        
        // Obtener el ID del personal
        $sql_personal = "SELECT id_personal FROM devolucion WHERE id_devolucion = $id_devolucion";
        $res_personal = mysqli_query($con, $sql_personal);
        $row_personal = mysqli_fetch_assoc($res_personal);
        $id_personal = $row_personal['id_personal'];
        
        // Insertar nuevos detalles y movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            
            $sql_detalle = "INSERT INTO devolucion_detalle (
                                id_devolucion, id_producto,  
                                det_devolucion_detalle, cant_devolucion_detalle, est_devolucion_detalle
                            ) VALUES (
                                $id_devolucion, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                
                if ($id_cliente_destino == 1) {
                    // DEVOLUCIÓN A ARCE
                    
                    // Resta origen
                    $sql_mov_resta = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                      ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2, 
                                        $cantidad, NOW(), 1
                                      )";
                    mysqli_query($con, $sql_mov_resta);
                    
                    // Suma ARCE BASE
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                      ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, 1, 
                                        1, 3, 1, 
                                        $cantidad, NOW(), 1
                                      )";
                    mysqli_query($con, $sql_mov_suma);
                    
                } else {
                    // DEVOLUCIÓN A OTRO CLIENTE
                    
                    $sql_mov_devolucion = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                      ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2, 
                                        $cantidad, NOW(), 1
                                      )";
                    mysqli_query($con, $sql_mov_devolucion);
                }
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


//=======================================================================
// VALIDACIONES PARA SALIDAS - Modelo actualizado m_salidas.php
//=======================================================================

function ValidarDevolucionAntesDeProcesar($id_material_tipo, $id_almacen, $id_ubicacion, $materiales) 
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    // 3. Validar stock disponible para cada material
    foreach ($materiales as $material) {
        $id_producto = intval($material['id_producto']);
        $cantidad = floatval($material['cantidad']);
        $descripcion = $material['descripcion'];
        
        // Obtener stock actual del producto en la ubicación origen
        $stock_disponible = ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion);
        
        if ($stock_disponible <= 0) {
            $errores[] = "El producto '{$descripcion}' no tiene stock disponible en la ubicación origen seleccionada.";
        } elseif ($cantidad > $stock_disponible) {
            $errores[] = "La cantidad solicitada para '{$descripcion}' ({$cantidad}) excede el stock disponible ({$stock_disponible}).";
        }
    }
    
    mysqli_close($con);
    return $errores;
}

////////////////////funciones para anular y confirmar///////////////////////////////////

function AnularDevolucion($id_devolucion) {
    include("../_conexion/conexion.php");

    // 1. Marcar devolución como anulada
    $sql = "UPDATE devolucion 
            SET est_devolucion = 0 
            WHERE id_devolucion = $id_devolucion";
    mysqli_query($con, $sql);

    // 3. Invalidar movimientos de SALIDA
    $sql_mov = "UPDATE movimiento 
                SET est_movimiento = 0 
                WHERE id_orden = $id_devolucion 
                  AND tipo_orden = 3";
    mysqli_query($con, $sql_mov);

    mysqli_close($con);
    return "SI";
}

function ConfirmarDevolucion($id_devolucion) {
    include("../_conexion/conexion.php");

    // Solo cambiamos el estado, no tocamos stock
    $sql = "UPDATE devolucion 
            SET est_devolucion = 2 
            WHERE id_devolucion = $id_devolucion";
    mysqli_query($con, $sql);

    mysqli_close($con);
    return "SI";
}

//-----------------------------------------------------------------------
// NUEVA: Obtener lista de clientes activos
function ObtenerClientes() 
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT id_cliente, nom_cliente 
            FROM {$bd_complemento}.cliente 
            WHERE act_cliente = 1 
            ORDER BY 
                CASE WHEN id_cliente = 1 THEN 0 ELSE 1 END,
                nom_cliente";
    
    $res = mysqli_query($con, $sql);
    $resultado = array();
    
    while ($row = mysqli_fetch_assoc($res)) {
        $resultado[] = $row;
    }
    
    mysqli_close($con);
    return $resultado;
}