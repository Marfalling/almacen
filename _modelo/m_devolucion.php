<?php
//=======================================================================
// MODELO: m_devolucion.php
//=======================================================================

/*function GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $id_cliente_destino, $obs_devolucion, $materiales) 
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
                    
                    // Movimiento 2: SUMA a ARCE BASE (id_almacen=3, id_ubicacion=1)
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, 3, 
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
}*/

function GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $obs_devolucion, $materiales, $id_cliente_destino) 
{
    include("../_conexion/conexion.php");

    // La ubicación destino SIEMPRE es BASE (id_ubicacion = 1)
    //$id_ubicacion_destino = 1;

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
                
                // LÓGICA SIMPLIFICADA:
                // Si la ubicación origen NO es BASE, genera traslado interno
                if ($id_ubicacion != 1) {
                    
                    // Movimiento 1: RESTA de la ubicación origen
                    $sql_mov_resta = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2, 
                                        $cantidad, NOW(), 2
                                    )";
                    mysqli_query($con, $sql_mov_resta);
                    
                    // Movimiento 2: SUMA a BASE del mismo almacén
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        1, 3, 1, 
                                        $cantidad, NOW(), 2
                                    )";
                    mysqli_query($con, $sql_mov_suma);
                    
                } else {
                    // Si ya está en BASE, no genera movimientos
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
        
        // ✅ ANULAR movimientos anteriores (no eliminar)
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_devolucion AND tipo_orden = 3";
        mysqli_query($con, $sql_del_mov);
        
        // Obtener el ID del personal
        $sql_personal = "SELECT id_personal FROM devolucion WHERE id_devolucion = $id_devolucion";
        $res_personal = mysqli_query($con, $sql_personal);
        $row_personal = mysqli_fetch_assoc($res_personal);
        $id_personal = $row_personal['id_personal'];
        
        // ✅ ACTUALIZAR detalles existentes (no eliminar y reinsertar)
        foreach ($materiales as $material) {
            $id_devolucion_detalle = isset($material['id_devolucion_detalle']) ? intval($material['id_devolucion_detalle']) : 0;
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $detalle = mysqli_real_escape_string($con, $material['detalle']);
            
            if ($id_devolucion_detalle > 0) {
                // ✅ ACTUALIZAR detalle existente
                $sql_detalle = "UPDATE devolucion_detalle SET
                                    id_producto = $id_producto,
                                    cant_devolucion_detalle = $cantidad,
                                    det_devolucion_detalle = '$detalle'
                                WHERE id_devolucion_detalle = $id_devolucion_detalle";
            } else {
                // ✅ INSERTAR nuevo detalle (si se agregó uno nuevo)
                $sql_detalle = "INSERT INTO devolucion_detalle (
                                    id_devolucion, id_producto, cant_devolucion_detalle, 
                                    det_devolucion_detalle, est_devolucion_detalle
                                ) VALUES (
                                    $id_devolucion, $id_producto, $cantidad, 
                                    '$detalle', 1
                                )";
            }
            
            if (mysqli_query($con, $sql_detalle)) {
                
                // ✅ LÓGICA IGUAL A GrabarDevolucion:
                // Si la ubicación origen NO es BASE, genera traslado interno
                if ($id_ubicacion != 1) {
                    
                    // Movimiento 1: RESTA de la ubicación origen
                    $sql_mov_resta = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2, 
                                        $cantidad, NOW(), 2
                                    )";
                    mysqli_query($con, $sql_mov_resta);
                    
                    // Movimiento 2: SUMA a BASE del mismo almacén
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        1, 3, 1, 
                                        $cantidad, NOW(), 2
                                    )";
                    mysqli_query($con, $sql_mov_suma);
                    
                } else {
                    // Si ya está en BASE, no genera movimientos
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
function ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                -- STOCK FÍSICO (entradas - salidas), sin contar tipo_orden = 5
                COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0) AS stock_fisico,

                -- STOCK COMPROMETIDO (pedidos activos tipo_orden = 5)
                COALESCE(SUM(CASE
                    WHEN mov.tipo_orden = 5 THEN mov.cant_movimiento
                    ELSE 0
                END), 0) AS stock_comprometido,

                -- STOCK DISPONIBLE (físico - comprometido)
                (
                    COALESCE(SUM(CASE
                        WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                        ELSE 0
                    END), 0)
                    -
                    COALESCE(SUM(CASE
                        WHEN mov.tipo_orden = 5 THEN mov.cant_movimiento
                        ELSE 0
                    END), 0)
                ) AS stock_disponible
            FROM movimiento mov
            WHERE mov.id_producto = $id_producto 
            AND mov.id_almacen = $id_almacen 
            AND mov.id_ubicacion = $id_ubicacion
            AND mov.est_movimiento != 0";
    
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
    
    // Obtener id_personal de la sesión
    $id_personal = $_SESSION['id_personal'];

    // 1. Actualizar estado de la devolución
    $sql = "UPDATE devolucion 
            SET est_devolucion = 2 
            WHERE id_devolucion = $id_devolucion";
    
    if (!mysqli_query($con, $sql)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }

    // 2. Obtener el ID_CLIENTE_DESTINO de la devolución
    $sql_get_cliente = "SELECT id_cliente_destino FROM devolucion WHERE id_devolucion = $id_devolucion";
    $result_cliente = mysqli_query($con, $sql_get_cliente);
    $row_cliente = mysqli_fetch_assoc($result_cliente);
    $id_cliente_destino = $row_cliente['id_cliente_destino'];

    // 3. Actualizar movimientos de SALIDA (tipo_movimiento = 2) → est_movimiento = 1
    $sql_salida = "UPDATE movimiento 
                   SET est_movimiento = 1 
                   WHERE id_orden = $id_devolucion 
                     AND tipo_orden = 3 
                     AND tipo_movimiento = 2 
                     AND est_movimiento = 2";
    
    if (!mysqli_query($con, $sql_salida)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR actualizando salida: " . $error;
    }

    // 4. Actualizar movimiento de ENTRADA (tipo_movimiento = 1) → SIEMPRE est_movimiento = 0
    $sql_entrada = "UPDATE movimiento 
                    SET est_movimiento = 0
                    WHERE id_orden = $id_devolucion 
                      AND tipo_orden = 3 
                      AND tipo_movimiento = 1 
                      AND est_movimiento = 2";
    
    if (!mysqli_query($con, $sql_entrada)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR actualizando entrada: " . $error;
    }

    // 5. Si el cliente destino es 9 (ARCE), insertar nuevo movimiento
    if ($id_cliente_destino == 9) {
        // Obtener los detalles de los movimientos de la devolución para replicarlos
        $sql_detalles = "SELECT id_producto, cant_movimiento 
                         FROM movimiento 
                         WHERE id_orden = $id_devolucion 
                           AND tipo_orden = 3 
                           AND tipo_movimiento = 1";
        
        $result_detalles = mysqli_query($con, $sql_detalles);
        
        while ($row = mysqli_fetch_assoc($result_detalles)) {
            $id_producto = $row['id_producto'];
            $cantidad = $row['cant_movimiento'];
            
            // Insertar nuevo movimiento para ARCE BASE
            $sql_insert = "INSERT INTO movimiento 
                          (tipo_orden, id_orden, tipo_movimiento, id_almacen, id_ubicacion, 
                           id_producto, cant_movimiento, id_personal, fec_movimiento, est_movimiento) 
                          VALUES 
                          (3, $id_devolucion, 1, 1, 1, $id_producto, $cantidad, $id_personal, NOW(), 1)";
            
            if (!mysqli_query($con, $sql_insert)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR insertando movimiento ARCE: " . $error;
            }
        }
    }

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

function ObtenerClientePorAlmacen($id_almacen) 
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT c.id_cliente, c.nom_cliente 
            FROM almacen a
            INNER JOIN {$bd_complemento}.cliente c ON a.id_cliente = c.id_cliente
            WHERE a.id_almacen = $id_almacen
            AND c.act_cliente = 1";
    
    $res = mysqli_query($con, $sql);
    $resultado = null;
    
    if ($row = mysqli_fetch_assoc($res)) {
        $resultado = $row;
    }
    
    mysqli_close($con);
    return $resultado;
}