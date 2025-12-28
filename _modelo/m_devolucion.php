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
    date_default_timezone_set('America/Lima'); 
    
    $fecha_actual = date('Y-m-d H:i:s'); 

    //  OBTENER CENTRO DE COSTO DEL REGISTRADOR
    require_once("m_centro_costo.php");
    $id_registrador_centro_costo = "NULL";
    $centro_registrador = ObtenerCentroCostoPersonal($id_personal);
    if ($centro_registrador && isset($centro_registrador['id_centro_costo'])) {
        $id_registrador_centro_costo = intval($centro_registrador['id_centro_costo']);
    }

    // Insertar devolución principal
    $sql = "INSERT INTO devolucion (
                id_almacen, 
                id_ubicacion, 
                id_personal, 
                id_registrador_centro_costo,
                id_cliente_destino,
                obs_devolucion, 
                fec_devolucion, 
                est_devolucion
            ) VALUES (
                $id_almacen, 
                $id_ubicacion, 
                $id_personal, 
                $id_registrador_centro_costo,
                $id_cliente_destino,
                '$obs_devolucion', 
                '$fecha_actual', 
                1
            )";

    if (mysqli_query($con, $sql)) {
        $id_devolucion = mysqli_insert_id($con);
        
        // Insertar detalles y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $detalle = mysqli_real_escape_string($con, $material['detalle']);

            // 🔹 OBTENER CENTROS DE COSTO DEL MATERIAL
            $centros_costo = isset($material['centros_costo']) && is_array($material['centros_costo']) 
                            ? $material['centros_costo'] 
                            : array();

            // Insertar detalle de devolución
            $sql_detalle = "INSERT INTO devolucion_detalle (
                                id_devolucion, 
                                id_producto, 
                                cant_devolucion_detalle, 
                                det_devolucion_detalle, 
                                est_devolucion_detalle
                            ) VALUES (
                                $id_devolucion, 
                                $id_producto, 
                                $cantidad, 
                                '$detalle', 
                                1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                error_log("❌ Error al insertar detalle de devolución: " . mysqli_error($con));
                continue;
            }
            
            $id_devolucion_detalle = mysqli_insert_id($con);

            // 🔹 INSERTAR CENTROS DE COSTO DEL DETALLE
            if (!empty($centros_costo)) {
                foreach ($centros_costo as $id_centro) {
                    $id_centro = intval($id_centro);
                    if ($id_centro > 0) {
                        $sql_cc = "INSERT INTO devolucion_detalle_centro_costo 
                                  (id_devolucion_detalle, id_centro_costo) 
                                  VALUES ($id_devolucion_detalle, $id_centro)";
                        mysqli_query($con, $sql_cc);
                        
                        error_log("✅ Centro de costo $id_centro asignado a devolucion_detalle $id_devolucion_detalle");
                    }
                }
            }

            // 🔹 GENERAR MOVIMIENTOS (lógica existente)
            if ($id_cliente_destino == 9) {
                // Destino SIEMPRE es almacen 1, BASE
                $id_almacen_destino = 1;
                $id_ubicacion_destino = 1;

                    // SIEMPRE MUEVE, incluso si la ubicación origen es BASE
                    // ----------------------
                // Movimiento 1: RESTA
                    // ----------------------
                $sql_mov_resta = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                ) VALUES (
                                    $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                    $id_ubicacion, 3, 2,
                                    $cantidad, '$fecha_actual', 2
                                )";
                mysqli_query($con, $sql_mov_resta);

                    // ----------------------
                // Movimiento 2: SUMA
                    // ----------------------
                $sql_mov_suma = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                ) VALUES (
                                    $id_personal, $id_devolucion, $id_producto, $id_almacen_destino, 
                                    $id_ubicacion_destino, 3, 1,
                                    $cantidad, '$fecha_actual', 2
                                )";
                mysqli_query($con, $sql_mov_suma);

                    continue; // este caso ya está resuelto
            }
            
            // Si la ubicación origen NO es BASE, genera traslado interno
            if ($id_ubicacion == 1) {
                continue;
            }
                
            // Movimiento 1: RESTA de la ubicación origen
            $sql_mov_resta = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                $id_ubicacion, 3, 2, 
                                $cantidad, '$fecha_actual', 2
                            )";
            mysqli_query($con, $sql_mov_resta);
                
            // Determinar el almacén destino según el cliente
            $id_almacen_destino = $id_almacen;
                
            // Movimiento 2: SUMA a BASE del almacén correspondiente
            $sql_mov_suma = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_devolucion, $id_producto, $id_almacen_destino, 
                                1, 3, 1, 
                                $cantidad, '$fecha_actual', 2
                            )";
            mysqli_query($con, $sql_mov_suma);
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
                   c.nom_cliente as nom_cliente_destino,
                   -- Centro de costo del registrador
                   area_reg.nom_area as nom_centro_costo_registrador
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            -- JOIN para centro de costo del registrador
            LEFT JOIN {$bd_complemento}.area area_reg ON d.id_registrador_centro_costo = area_reg.id_area
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
                   c.nom_cliente as nom_cliente_destino,
                   -- Centro de costo del registrador
                   area_reg.nom_area as nom_centro_costo_registrador
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            -- JOIN para centro de costo del registrador
            LEFT JOIN {$bd_complemento}.area area_reg ON d.id_registrador_centro_costo = area_reg.id_area
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
                   c.nom_cliente as nom_cliente_destino,
                   -- 🔹 CENTRO DE COSTO DEL REGISTRADOR
                   area_reg.nom_area as nom_centro_costo_registrador
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN {$bd_complemento}.personal p ON d.id_personal = p.id_personal
            INNER JOIN {$bd_complemento}.cliente c ON d.id_cliente_destino = c.id_cliente
            -- 🔹 JOIN PARA CENTRO DE COSTO DEL REGISTRADOR
            LEFT JOIN {$bd_complemento}.area area_reg ON d.id_registrador_centro_costo = area_reg.id_area
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
               um.cod_unidad_medida,
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
    date_default_timezone_set('America/Lima'); 
    
    $fecha_actual = date('Y-m-d H:i:s'); 

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
            $detalle = mysqli_real_escape_string($con, $material['descripcion']);
            
            // 🔹 OBTENER CENTROS DE COSTO DEL MATERIAL
            $centros_costo = isset($material['centros_costo']) && is_array($material['centros_costo']) 
                            ? $material['centros_costo'] 
                            : array();
            
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
                
                // Si es INSERT, obtener el ID
                if ($id_devolucion_detalle == 0) {
                    $id_devolucion_detalle = mysqli_insert_id($con);
                }
                
                // 🔹 ACTUALIZAR CENTROS DE COSTO
                // Eliminar centros existentes
                $sql_eliminar_cc = "DELETE FROM devolucion_detalle_centro_costo 
                                   WHERE id_devolucion_detalle = $id_devolucion_detalle";
                mysqli_query($con, $sql_eliminar_cc);
                
                // Insertar nuevos centros
                if (!empty($centros_costo)) {
                    foreach ($centros_costo as $id_centro) {
                        $id_centro = intval($id_centro);
                        if ($id_centro > 0) {
                            $sql_cc = "INSERT INTO devolucion_detalle_centro_costo 
                                      (id_devolucion_detalle, id_centro_costo) 
                                      VALUES ($id_devolucion_detalle, $id_centro)";
                            mysqli_query($con, $sql_cc);
                        }
                    }
                }

                // 🔹 GENERAR MOVIMIENTOS (lógica existente)
                if ($id_cliente_destino == 9) {

                    // Destino SIEMPRE es almacen 1, BASE
                    $id_almacen_destino = 1;
                    $id_ubicacion_destino = 1;

                    // SIEMPRE MUEVE, incluso si la ubicación origen es BASE
                    // ----------------------
                    // Movimiento 1: RESTA
                    // ----------------------
                    $sql_mov_resta = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                        $id_ubicacion, 3, 2,
                                        $cantidad, '$fecha_actual', 2
                                    )";
                    mysqli_query($con, $sql_mov_resta);

                    // ----------------------
                    // Movimiento 2: SUMA
                    // ----------------------
                    $sql_mov_suma = "INSERT INTO movimiento (
                                        id_personal, id_orden, id_producto, id_almacen, 
                                        id_ubicacion, tipo_orden, tipo_movimiento, 
                                        cant_movimiento, fec_movimiento, est_movimiento
                                    ) VALUES (
                                        $id_personal, $id_devolucion, $id_producto, $id_almacen_destino, 
                                        $id_ubicacion_destino, 3, 1,
                                        $cantidad, '$fecha_actual', 2
                                    )";
                    mysqli_query($con, $sql_mov_suma);

                    continue; // este caso ya está resuelto
                }
                
                // LÓGICA SIMPLIFICADA:
                // Si la ubicación origen NO es BASE, genera traslado interno
                if ($id_ubicacion == 1) {
                    continue;
                }
                    
                // Movimiento 1: RESTA de la ubicación origen
                $sql_mov_resta = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                ) VALUES (
                                    $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                    $id_ubicacion, 3, 2, 
                                    $cantidad, '$fecha_actual', 2
                                )";
                mysqli_query($con, $sql_mov_resta);
                    
                // Determinar el almacén destino según el cliente
                $id_almacen_destino = $id_almacen;
                    
                // Movimiento 2: SUMA a BASE del almacén correspondiente
                $sql_mov_suma = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                                ) VALUES (
                                    $id_personal, $id_devolucion, $id_producto, $id_almacen_destino, 
                                    1, 3, 1, 
                                    $cantidad, '$fecha_actual', 2
                                )";
                mysqli_query($con, $sql_mov_suma);
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

    $nuevo_estado_entrada = ($id_cliente_destino == 9) ? 1 : 0;

    // 4. Actualizar movimiento de ENTRADA (tipo_movimiento = 1 o 0) según el cliente destino
    $sql_entrada = "UPDATE movimiento 
                    SET est_movimiento = $nuevo_estado_entrada
                    WHERE id_orden = $id_devolucion 
                      AND tipo_orden = 3 
                      AND tipo_movimiento = 1 
                      AND est_movimiento = 2";
    
    if (!mysqli_query($con, $sql_entrada)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR actualizando entrada: " . $error;
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

function ObtenerCentrosCostoPorDetalleDevolucion($id_devolucion_detalle) 
{
    include("../_conexion/conexion.php");
    
    $id_devolucion_detalle = intval($id_devolucion_detalle);
    
    $sql = "SELECT id_centro_costo
            FROM devolucion_detalle_centro_costo
            WHERE id_devolucion_detalle = $id_devolucion_detalle";
    
    $resultado = mysqli_query($con, $sql);
    $centros_ids = [];
    
    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $centros_ids[] = intval($row['id_centro_costo']);
        }
    }
    
    mysqli_close($con);
    return $centros_ids;
}

function ConsultarDevolucionDetalleConCentros($id_devolucion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT dd.*, 
               pr.nom_producto, 
               um.cod_unidad_medida,
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
        // 🔹 CARGAR CENTROS DE COSTO CON NOMBRES
        $centros_sql = "SELECT 
                            ddcc.id_centro_costo,
                            a.nom_area as nom_centro_costo
                        FROM devolucion_detalle_centro_costo ddcc
                        INNER JOIN {$bd_complemento}.area a ON ddcc.id_centro_costo = a.id_area
                        WHERE ddcc.id_devolucion_detalle = {$row['id_devolucion_detalle']}
                        ORDER BY a.nom_area ASC";
        
        $centros_res = mysqli_query($con, $centros_sql);
        $centros = array();
        
        if ($centros_res) {
            while ($centro_row = mysqli_fetch_assoc($centros_res)) {
                $centros[] = $centro_row;
            }
        }
        
        $row['centros_costo'] = $centros;
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}
