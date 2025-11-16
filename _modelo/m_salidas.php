<?php
//=======================================================================
// MODELO: m_salidas.php
//=======================================================================
require_once("m_pedidos.php");
//-----------------------------------------------------------------------
function GrabarSalida($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                     $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                     $fec_req_salida, $obs_salida, $id_personal_encargado, 
                     $id_personal_recibe, $id_personal, $materiales, $id_pedido = null) 
{
    //  VALIDACIÓN 1: Lógica de cantidades verificadas
    $errores_validacion = ValidarSalidaAntesDeProcesar(
        $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
        $id_almacen_destino, $id_ubicacion_destino, $materiales, $id_pedido
    );
    
    if (!empty($errores_validacion)) {
        $mensaje_error = "";
        
        if (is_array($errores_validacion)) {
            $mensaje_error = implode(" | ", $errores_validacion);
        } else {
            $mensaje_error = strval($errores_validacion);
        }
        
        // 🔥 DETECTAR SI ES ERROR DE STOCK
        if (stripos($mensaje_error, 'stock') !== false || 
            stripos($mensaje_error, 'disponible') !== false) {
            
            // Re-verificar automáticamente
            if ($id_pedido) {
                foreach ($materiales as $material) {
                    if (isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                        ReverificarItemAutomaticamente(intval($material['id_pedido_detalle']));
                    }
                }
            }
            
            error_log(" ERROR DE STOCK DETECTADO EN VALIDACIÓN 1");
            error_log("Mensaje: ERROR DE STOCK: " . $mensaje_error);
            
            return "ERROR DE STOCK: " . $mensaje_error . " Las cantidades del pedido han sido re-verificadas automáticamente según el stock disponible actual.";
        }
        
        return "ERROR DE VALIDACIÓN: " . $mensaje_error;
    }
    
    //  VALIDACIÓN 2: Stock físico real disponible
    require_once("../_modelo/m_pedidos.php"); 
    $errores_stock = ValidarInventarioDisponibleParaSalida(
        $materiales, 
        $id_almacen_origen, 
        $id_ubicacion_origen, 
        $id_pedido,
        null
    );
    
    if (!empty($errores_stock)) {
        // Re-verificar automáticamente
        if ($id_pedido) {
            foreach ($materiales as $material) {
                if (isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                    ReverificarItemAutomaticamente(intval($material['id_pedido_detalle']));
                }
            }
        }
        
        $mensaje_error = "";
        
        if (is_array($errores_stock)) {
            $mensaje_error = implode(" | ", $errores_stock);
        } else {
            $mensaje_error = strval($errores_stock);
        }
        
        error_log("🔴 ERROR DE STOCK DETECTADO EN VALIDACIÓN 2");
        error_log("Mensaje: ERROR DE STOCK: " . $mensaje_error);
        
        return "ERROR DE STOCK: " . $mensaje_error . " Las cantidades del pedido han sido re-verificadas automáticamente según el stock disponible actual.";
    }
        
    //  SI PASÓ VALIDACIONES, CONTINUAR
    include("../_conexion/conexion.php");

    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);
    $id_pedido_sql = ($id_pedido && $id_pedido > 0) ? intval($id_pedido) : "NULL";

    // Insertar salida principal
    $sql = "INSERT INTO salida (
                id_material_tipo, id_pedido, id_almacen_origen, id_ubicacion_origen, 
                id_almacen_destino, id_ubicacion_destino, id_personal, 
                id_personal_encargado, id_personal_recibe, ndoc_salida, 
                fec_req_salida, fec_salida, obs_salida, est_salida
            ) VALUES (
                $id_material_tipo, $id_pedido_sql, $id_almacen_origen, $id_ubicacion_origen, 
                $id_almacen_destino, $id_ubicacion_destino, $id_personal, 
                $id_personal_encargado, $id_personal_recibe, '$ndoc_salida', 
                '$fec_req_salida', NOW(), '$obs_salida', 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_salida = mysqli_insert_id($con);
        
        // ----------------------------
        // desde el foreach de materiales hasta el return
        // ----------------------------
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_pedido_detalle = isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0 
                                ? intval($material['id_pedido_detalle']) 
                                : null;
            $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
               
            // Verificación final de seguridad
            $stock_actual = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen, $id_pedido);
            if ($cantidad > $stock_actual) {
                if ($id_pedido && $id_pedido_detalle !== null) {
                    ReverificarItemAutomaticamente($id_pedido_detalle);
                }
                
                mysqli_query($con, "DELETE FROM salida WHERE id_salida = $id_salida");
                mysqli_close($con);
                
                return "ERROR DE STOCK: Stock insuficiente para '{$descripcion}'. Se requieren {$cantidad} unidades pero solo hay {$stock_actual} disponibles. Las cantidades del pedido han sido ajustadas automáticamente.";
            }
            
            // Insertar detalle
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_pedido_detalle, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_pedido_detalle_sql, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Movimiento SALIDA
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
                
                // Movimiento INGRESO
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

        // 🔹 ACTUALIZACIÓN DE ESTADOS DESPUÉS DEL COMMIT (FUERA DE LA TRANSACCIÓN)
        if ($id_pedido && $id_pedido > 0) {
            require_once("../_modelo/m_pedidos.php");
            
            usleep(200000); // 200ms - esperar a que se procesen los movimientos
            
            // 🔹 RE-VERIFICAR ITEMS AFECTADOS (igual que en AnularSalida)
            foreach ($materiales as $material) {
                if (isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                    $id_detalle = intval($material['id_pedido_detalle']);
                    error_log("🔄 Re-verificando item después de salida: $id_detalle");
                    
                    // Primero verificar estado
                    VerificarEstadoItemPorDetalle($id_detalle);
                    
                    // Luego forzar re-verificación (igual que AnularSalida)
                    ReverificarItemAutomaticamente($id_detalle);
                }
            }
            
            // Actualizar estado del pedido al final
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estados actualizados correctamente");
        }

        return array('success' => true, 'id_salida' => intval($id_salida));
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
                pe.nom_personal as nom_encargado,
                prec.nom_personal as nom_recibe,
                COALESCE(papr.nom_personal, '-') AS nom_aprueba,
                s.fec_aprueba_salida
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             LEFT JOIN {$bd_complemento}.personal papr ON s.id_personal_aprueba_salida = papr.id_personal
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
//-----------------------------------------------------------------------
function MostrarSalidasFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    // Condición por defecto → solo fecha actual
    if ($fecha_inicio && $fecha_fin) {
        $whereFecha = "AND DATE(s.fec_salida) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $whereFecha = "AND DATE(s.fec_salida) = CURDATE()";
    }

    $sqlc = "SELECT s.*, 
                mt.nom_material_tipo,
                ao.nom_almacen as nom_almacen_origen,
                ad.nom_almacen as nom_almacen_destino,
                uo.nom_ubicacion as nom_ubicacion_origen,
                ud.nom_ubicacion as nom_ubicacion_destino,
                pr.nom_personal, 
                pe.nom_personal as nom_encargado,
                prec.nom_personal as nom_recibe,
                COALESCE(papr.nom_personal, '-') AS nom_aprueba,
                s.fec_aprueba_salida
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             LEFT JOIN {$bd_complemento}.personal papr ON s.id_personal_aprueba_salida = papr.id_personal
             WHERE 1=1
             $whereFecha
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
                pe.nom_personal as nom_encargado,
                prec.nom_personal as nom_recibe,
                COALESCE(papr.nom_personal, '-') AS nom_aprueba
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             LEFT JOIN {$bd_complemento}.personal papr ON s.id_personal_aprueba_salida = papr.id_personal
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
                pd.id_pedido_detalle,
                pd.cant_pedido_detalle,
                pd.cant_os_pedido_detalle,
                
                -- 🔥 CÁLCULO CORRECTO: Cantidad máxima = cant OS verificada - otras salidas activas
                COALESCE(pd.cant_os_pedido_detalle, 0) - COALESCE(
                    (SELECT SUM(sd2.cant_salida_detalle)
                     FROM salida_detalle sd2
                     INNER JOIN salida s2 ON sd2.id_salida = s2.id_salida
                     WHERE sd2.id_pedido_detalle = pd.id_pedido_detalle
                     AND s2.est_salida = 1  -- Solo salidas activas
                     AND s2.id_salida != $id_salida  -- Excluir esta salida
                     AND sd2.est_salida_detalle = 1
                    ), 0
                ) + sd.cant_salida_detalle AS cantidad_maxima
                
             FROM salida_detalle sd 
             INNER JOIN producto p ON sd.id_producto = p.id_producto
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
             LEFT JOIN pedido_detalle pd ON sd.id_pedido_detalle = pd.id_pedido_detalle
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

    error_log("🔧 ActualizarSalida - ID Salida: $id_salida");
    
    // Obtener ID_PEDIDO (puede ser 0 para salidas manuales)
    $sql_pedido = "SELECT id_pedido FROM salida WHERE id_salida = $id_salida";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido ? intval($row_pedido['id_pedido']) : 0;
    
    //  CAMBIO: Permitir salidas sin pedido (id_pedido = 0)
    $tiene_pedido = ($id_pedido > 0);
    
    if ($tiene_pedido) {
        error_log("📋 Salida asociada a pedido: $id_pedido");
    } else {
        error_log("📋 Salida manual (sin pedido asociado)");
    }

    //  VALIDACIÓN SOLO SI TIENE PEDIDO ASOCIADO
$errores = [];

if ($tiene_pedido) {
    foreach ($materiales as $material) {
        $id_producto = intval($material['id_producto']);
        $id_pedido_detalle = isset($material['id_pedido_detalle']) ? intval($material['id_pedido_detalle']) : 0;
        $cantidad_nueva = floatval($material['cantidad']);
        
        if ($id_pedido_detalle <= 0) continue;
        
        // 🔹 OBTENER ALMACÉN Y UBICACIÓN ORIGEN DE LA SALIDA
        $sql_ubicaciones = "SELECT id_almacen_origen, id_ubicacion_origen 
                           FROM salida 
                           WHERE id_salida = $id_salida";
        $res_ubicaciones = mysqli_query($con, $sql_ubicaciones);
        $ubicaciones = mysqli_fetch_assoc($res_ubicaciones);
        
        if (!$ubicaciones) {
            $errores[] = "No se pudo obtener la ubicación origen de la salida";
            continue;
        }
        
        $id_almacen_salida = intval($ubicaciones['id_almacen_origen']);
        $id_ubicacion_salida = intval($ubicaciones['id_ubicacion_origen']);
        
        error_log("   📍 Almacén: $id_almacen_salida | Ubicación: $id_ubicacion_salida");
        
        // 🔹 OBTENER CANTIDAD ACTUAL EN ESTA SALIDA
        $sql_cantidad_actual = "SELECT COALESCE(SUM(cant_salida_detalle), 0) as cantidad_actual
                                FROM salida_detalle 
                                WHERE id_salida = $id_salida 
                                  AND id_producto = $id_producto 
                                  AND est_salida_detalle = 1";
        
        $res_actual = mysqli_query($con, $sql_cantidad_actual);
        $row_actual = mysqli_fetch_assoc($res_actual);
        $cantidad_actual_en_salida = floatval($row_actual['cantidad_actual']);
        
        error_log("   📦 Cantidad actual en esta salida: $cantidad_actual_en_salida");
        
        // 🔹 CALCULAR STOCK FÍSICO REAL (SIN COMPROMISOS, SIN ESTA SALIDA)
        $sql_stock = "SELECT COALESCE(
                        SUM(
                            CASE
                                WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                                WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden <> 5 THEN -mov.cant_movimiento
                                ELSE 0
                            END
                        ), 0) AS stock_fisico_real
                      FROM movimiento mov
                      WHERE mov.id_producto = $id_producto
                        AND mov.id_almacen = $id_almacen_salida
                        AND mov.id_ubicacion = $id_ubicacion_salida
                        AND mov.est_movimiento = 1
                        AND NOT (mov.tipo_orden = 2 AND mov.id_orden = $id_salida)";
        
        $res_stock = mysqli_query($con, $sql_stock);
        $row_stock = mysqli_fetch_assoc($res_stock);
        $stock_fisico_real = floatval($row_stock['stock_fisico_real']);
        
        error_log("   📊 Stock físico REAL (sin esta salida): $stock_fisico_real");
        
        // 🔹 CALCULAR STOCK DISPONIBLE = físico + lo que ya está en esta salida
        $stock_disponible = $stock_fisico_real + $cantidad_actual_en_salida;
        
        error_log("   ✅ Stock disponible TOTAL: $stock_disponible (físico: $stock_fisico_real + en salida: $cantidad_actual_en_salida)");
        
        // 🔹 VALIDACIÓN: La cantidad nueva no debe exceder el stock disponible
        if ($cantidad_nueva > $stock_disponible) {
            error_log("   ❌ EXCEDE - Stock disponible: $stock_disponible | Intentas: $cantidad_nueva");
            
            // Obtener descripción del producto
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            $faltante = $cantidad_nueva - $stock_disponible;
            
            $errores[] = "$descripcion: Stock insuficiente en ubicación origen. Disponible: " . number_format($stock_disponible, 2) . 
                        ", Solicitado: " . number_format($cantidad_nueva, 2) . 
                        " (Faltante: " . number_format($faltante, 2) . ")";
        } else {
            error_log("   ✅ VÁLIDO - Dentro del stock disponible");
        }
    }
    
    if (!empty($errores)) {
        mysqli_close($con);
        return "ERROR: " . implode(" | ", $errores);
    }
}
    
    //  SI PASÓ VALIDACIONES (O NO TIENE PEDIDO), CONTINUAR
    include("../_conexion/conexion.php");

    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);

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
        
        // Obtener el ID del personal que registra
        $sql_personal = "SELECT id_personal FROM salida WHERE id_salida = $id_salida";
        $res_personal = mysqli_query($con, $sql_personal);
        $row_personal = mysqli_fetch_assoc($res_personal);
        $id_personal = $row_personal['id_personal'];
        
        error_log(" ID Personal: $id_personal");
        
        error_log(" Materiales recibidos para procesar: " . count($materiales));

        // 🔹 PROCESAR CADA MATERIAL
        foreach ($materiales as $key => $material) {
            
            error_log("🔍 Procesando material key: $key");
            error_log("   Datos: " . print_r($material, true));
            
            //  VERIFICAR SI ES NUEVO O EXISTENTE
            $es_nuevo = isset($material['es_nuevo']) && $material['es_nuevo'] == '1';
            
            error_log("   ¿Es nuevo?: " . ($es_nuevo ? 'SÍ' : 'NO'));
            
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // 🔹 MANEJO DE id_pedido_detalle (puede ser NULL)
            $id_pedido_detalle = null;
            if (isset($material['id_pedido_detalle']) && !empty($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                $id_pedido_detalle = intval($material['id_pedido_detalle']);
            }
            
            $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
            
            if ($es_nuevo) {
                // ============================================================
                //  INSERTAR NUEVO DETALLE
                // ===========================================================
                error_log("    Insertando nuevo detalle - Producto: $id_producto | Cantidad: $cantidad");
                
                $sql_detalle = "INSERT INTO salida_detalle (
                            id_salida, id_pedido_detalle, id_producto, prod_salida_detalle, 
                            cant_salida_detalle, est_salida_detalle
                        ) VALUES (
                            $id_salida, $id_pedido_detalle_sql, $id_producto, '$descripcion', 
                            $cantidad, 1
                        )";
                
                if (!mysqli_query($con, $sql_detalle)) {
                    $error = mysqli_error($con);
                    error_log("❌ ERROR al insertar detalle: $error");
                    mysqli_close($con);
                    return "ERROR en detalle: " . $error;
                }
                
                error_log("   ✅ Detalle insertado correctamente");
                
            } else {
                // ============================================================
                //  ACTUALIZAR DETALLE EXISTENTE
                // ============================================================
                
                if (!isset($material['id_salida_detalle']) || empty($material['id_salida_detalle'])) {
                    error_log(" Material sin id_salida_detalle: " . print_r($material, true));
                    continue;
                }
                
                $id_salida_detalle = intval($material['id_salida_detalle']);
                
                error_log("   🔄 Actualizando salida_detalle ID: $id_salida_detalle | Nueva cantidad: $cantidad");
                
                // 🔹 VERIFICAR QUE EXISTE
                $sql_detalle_info = "SELECT id_producto FROM salida_detalle 
                                    WHERE id_salida_detalle = $id_salida_detalle 
                                    AND id_salida = $id_salida";
                $res_detalle_info = mysqli_query($con, $sql_detalle_info);
                
                if (!$res_detalle_info || mysqli_num_rows($res_detalle_info) == 0) {
                    error_log("⚠️ No se encontró salida_detalle con ID: $id_salida_detalle para salida: $id_salida");
                    continue;
                }
                
                // 🔹 ACTUALIZAR DETALLE
                $sql_detalle = "UPDATE salida_detalle SET 
                                    id_pedido_detalle = $id_pedido_detalle_sql,
                                    id_producto = $id_producto,
                                    prod_salida_detalle = '$descripcion',
                                    cant_salida_detalle = $cantidad
                                WHERE id_salida_detalle = $id_salida_detalle 
                                AND id_salida = $id_salida";
                
                if (!mysqli_query($con, $sql_detalle)) {
                    $error = mysqli_error($con);
                    error_log("❌ ERROR al actualizar detalle: $error");
                    error_log("   SQL: $sql_detalle");
                    mysqli_close($con);
                    return "ERROR en detalle: " . $error;
                }
                
                $filas_afectadas = mysqli_affected_rows($con);
                error_log("   ✅ Detalle actualizado correctamente (Filas afectadas: $filas_afectadas)");
                
                if ($filas_afectadas == 0) {
                    error_log("   ⚠️ ADVERTENCIA: No se actualizó ninguna fila. Verificar condiciones WHERE.");
                }
            }
            
            // ============================================================
            //  VERIFICAR ESTADO DEL ITEM (SOLO SI TIENE PEDIDO_DETALLE)
            // ============================================================
            if ($tiene_pedido && $id_pedido_detalle !== null && $id_pedido_detalle > 0) {
                error_log("   🔍 Verificando estado del detalle: $id_pedido_detalle");
                VerificarEstadoItemPorDetalle($id_pedido_detalle);
            }
        }

        // ============================================================
        //  REGENERAR MOVIMIENTOS
        // ============================================================
        error_log(" Regenerando movimientos...");

        // 1. Desactivar TODOS los movimientos anteriores de esta salida
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_salida AND tipo_orden = 2";
        mysqli_query($con, $sql_del_mov);

        error_log("    Movimientos anteriores desactivados");

        // 2. Crear nuevos movimientos
        $contador = 0;
        foreach ($materiales as $key => $material) {
            
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            error_log("    Generando movimientos para producto: $id_producto | Cantidad: $cantidad");
            
            // Movimiento de SALIDA en almacén origen (resta stock)
            $sql_mov_salida = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_salida, $id_producto, $id_almacen_origen, 
                                $id_ubicacion_origen, 2, 2, 
                                $cantidad, NOW(), 1
                            )";
            
            if (mysqli_query($con, $sql_mov_salida)) {
                error_log("       Movimiento SALIDA creado (Almacén: $id_almacen_origen)");
            } else {
                error_log("       ERROR movimiento SALIDA: " . mysqli_error($con));
            }
            
            // Movimiento de INGRESO en almacén destino (suma stock)
            $sql_mov_ingreso = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_salida, $id_producto, $id_almacen_destino, 
                                $id_ubicacion_destino, 2, 1, 
                                $cantidad, NOW(), 1
                            )";
            
            if (mysqli_query($con, $sql_mov_ingreso)) {
                error_log("      ✅ Movimiento INGRESO creado (Almacén: $id_almacen_destino)");
                $contador++;
            } else {
                error_log("      ❌ ERROR movimiento INGRESO: " . mysqli_error($con));
            }
        }

        error_log("✅ Total de productos procesados: $contador");

        // ============================================================
        //  ACTUALIZAR ESTADO DEL PEDIDO (SOLO SI EXISTE)
        // ============================================================
        if ($tiene_pedido) {
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            require_once("../_modelo/m_pedidos.php");
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estado del pedido actualizado");
        } else {
            error_log("⚠️ Sin pedido asociado, omitiendo actualización de estado");
        }

        mysqli_close($con);
        error_log("✅ ActualizarSalida completado exitosamente");
        return "SI";
        
    } else {
        $error = mysqli_error($con);
        error_log("❌ ERROR al actualizar salida principal: $error");
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}

//-----------------------------------------------------------------------
function ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion, $id_pedido = null)
{
    include("../_conexion/conexion.php");

    // Asegurarse de que $id_pedido sea un entero válido o NULL
    $id_pedido = $id_pedido !== null ? intval($id_pedido) : null;

    $sql = "SELECT COALESCE(
                SUM(
                    CASE
                        WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN 
                            CASE
                                WHEN mov.tipo_orden = 5 AND ".($id_pedido !== null ? "mov.id_orden = $id_pedido" : "0")." THEN 0
                                ELSE -mov.cant_movimiento
                            END
                        ELSE 0
                    END
                ), 0) AS stock_disponible
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

function ValidarSalidaAntesDeProcesar($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                                     $id_almacen_destino, $id_ubicacion_destino, $materiales, $id_pedido = null) 
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    // 1. Validar que no sea la misma ubicación (origen = destino)
    if ($id_almacen_origen == $id_almacen_destino && $id_ubicacion_origen == $id_ubicacion_destino) {
        $errores[] = "No puede realizar una salida hacia la misma ubicación de origen. Seleccione un destino diferente.";
    }
    
    // 2. Excluir material tipo "NA" (id = 1)
    if ($id_material_tipo == 1) {
        $errores[] = "No se puede realizar salidas para el tipo de material 'NA'. Este tipo está reservado para servicios.";
    }
    
    // 3. Validar stock disponible para cada material
    foreach ($materiales as $material) {

        $id_producto = intval($material['id_producto']);
        $cantidad = floatval($material['cantidad']);

        //  Obtener descripción 
        $descripcion = '';
        if (isset($material['descripcion']) && !empty(trim($material['descripcion']))) {
            $descripcion = trim($material['descripcion']);
        } else {
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            if ($res_desc && $row_desc = mysqli_fetch_assoc($res_desc)) {
                $descripcion = $row_desc['nom_producto'];
            } else {
                $descripcion = "Producto ID $id_producto";
            }
        }
        
        //  Obtener stock
        $stock_disponible = ObtenerStockDisponible(
            $id_producto, 
            $id_almacen_origen, 
            $id_ubicacion_origen, 
            $id_pedido
        );
        
        //  Validación de stock CON PREFIJO
        if ($stock_disponible <= 0) {
            $errores[] = "ERROR DE STOCK:: El producto '{$descripcion}' no tiene stock disponible en la ubicación origen seleccionada.";
        } 
        elseif ($cantidad > $stock_disponible) {
            $errores[] = "ERROR DE STOCK:: La cantidad solicitada para '{$descripcion}' ({$cantidad}) excede el stock disponible ({$stock_disponible}).";
        }
    }
    
    mysqli_close($con);
    return $errores;
}




// Función corregida para obtener productos con stock, excluyendo "NA"
function NumeroRegistrosTotalProductosConStockParaSalida($id_almacen, $id_ubicacion, $tipoMaterial = 0)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT COUNT(DISTINCT p.id_producto) as total
            FROM producto p 
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            WHERE p.est_producto = 1 
            AND pt.est_producto_tipo = 1
            AND mt.est_material_tipo = 1
            AND um.est_unidad_medida = 1
            AND mt.id_material_tipo != 1"; // Excluir "NA"

    // Filtrar por tipo de material si se especifica y no es "NA"
    if ($tipoMaterial > 0 && $tipoMaterial != 1) {
        $sql .= " AND mt.id_material_tipo = $tipoMaterial";
    }

    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return intval($row['total']);
}

function NumeroRegistrosFiltradosProductosConStockParaSalida($searchValue, $id_almacen, $id_ubicacion, $tipoMaterial = 0)
{
    include("../_conexion/conexion.php");
    mysqli_set_charset($con, "utf8");

    $sql = "SELECT COUNT(DISTINCT p.id_producto) as total
            FROM producto p 
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            WHERE p.est_producto = 1 
            AND pt.est_producto_tipo = 1
            AND mt.est_material_tipo = 1
            AND um.est_unidad_medida = 1
            AND mt.id_material_tipo != 1"; // Excluir "NA"

    // Filtrar por tipo de material si se especifica y no es "NA"
    if ($tipoMaterial > 0 && $tipoMaterial != 1) {
        $sql .= " AND mt.id_material_tipo = $tipoMaterial";
    }

    // Aplicar filtro de búsqueda
    if (!empty($searchValue)) {
        $searchValue = mysqli_real_escape_string($con, $searchValue);
        $sql .= " AND (p.cod_material LIKE '%$searchValue%' 
                  OR p.nom_producto LIKE '%$searchValue%'
                  OR p.mar_producto LIKE '%$searchValue%'
                  OR p.mod_producto LIKE '%$searchValue%'
                  OR pt.nom_producto_tipo LIKE '%$searchValue%')";
    }

    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return intval($row['total']);
}

function MostrarProductosConStockParaSalida($limit, $offset, $searchValue, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, $tipoMaterial = 0)
{
    include("../_conexion/conexion.php");

    // Construir la consulta base
    $sql = "SELECT 
                p.id_producto, 
                p.cod_material, 
                p.nom_producto, 
                p.mar_producto, 
                p.mod_producto,
                pt.nom_producto_tipo, 
                um.nom_unidad_medida,

                -- ==============================================
                -- CÁLCULO DE STOCK FÍSICO (movimientos reales)
                -- ==============================================
                COALESCE(SUM(
                    CASE
                        WHEN mov.tipo_orden <> 5 AND mov.tipo_movimiento = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_orden <> 5 AND mov.tipo_movimiento = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
                        ELSE 0
                    END
                ), 0) AS stock_fisico,

                -- ==============================================
                -- CÁLCULO DE STOCK COMPROMETIDO (pedidos)
                -- ==============================================
                COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 5 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                    ELSE 0
                END), 0) AS stock_comprometido,

                -- ==============================================
                -- STOCK DISPONIBLE = físico - comprometido
                -- ==============================================
                COALESCE(SUM(
                    CASE
                        WHEN mov.tipo_orden <> 5 AND mov.tipo_movimiento = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_orden <> 5 AND mov.tipo_movimiento = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
                        ELSE 0
                    END
                ), 0)
                -
                COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 5 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                    ELSE 0
                END), 0)
                AS stock_disponible

            FROM producto p 
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            LEFT JOIN movimiento mov ON p.id_producto = mov.id_producto 
                      AND mov.id_almacen = $id_almacen 
                      AND mov.id_ubicacion = $id_ubicacion
            WHERE p.est_producto = 1 
            AND pt.est_producto_tipo = 1
            AND mt.est_material_tipo = 1
            AND um.est_unidad_medida = 1";

    // VALIDACIÓN: Excluir material tipo "NA" (id = 1)
    //$sql .= " AND mt.id_material_tipo != 1";

    // Filtrar por tipo de material si se especifica y no es "NA"
    if ($tipoMaterial > 0) { //if ($tipoMaterial > 0 && $tipoMaterial != 1)
        $sql .= " AND mt.id_material_tipo = $tipoMaterial";
    }

    $sql .= " GROUP BY p.id_producto, p.cod_material, p.nom_producto, p.mar_producto, p.mod_producto, 
                       pt.nom_producto_tipo, um.nom_unidad_medida";

    // Aplicar filtro de búsqueda
    if (!empty($searchValue)) {
        $searchValue = mysqli_real_escape_string($con, $searchValue);
        $sql .= " HAVING (p.cod_material LIKE '%$searchValue%' 
                  OR p.nom_producto LIKE '%$searchValue%'
                  OR p.mar_producto LIKE '%$searchValue%'
                  OR p.mod_producto LIKE '%$searchValue%'
                  OR pt.nom_producto_tipo LIKE '%$searchValue%')";
    }

    // Aplicar ordenamiento
    $sql .= " ORDER BY $orderColumn $orderDirection";

    // Aplicar paginación
    $sql .= " LIMIT $offset, $limit";

    $resultado = mysqli_query($con, $sql);
    $productos = array();

    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $productos[] = $row;
    }

    mysqli_close($con);
    return $productos;
}

function TieneSalidaActivaPedido($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT COUNT(*) as total 
            FROM salida 
            WHERE id_pedido = $id_pedido 
            AND est_salida = 1";
    
    $resultado = mysqli_query($con, $sql);
    $tiene_salida = false;
    
    if ($resultado) {
        $row = mysqli_fetch_assoc($resultado);
        $tiene_salida = ($row['total'] > 0);
    }
    
    mysqli_close($con);
    return $tiene_salida;
}

/**
 * Anula una salida: desactiva la salida, desactiva los movimientos generados por la salida
 * si la salida proviene de un pedido- reactiva los compromisos del pedido.
 */
/*function AnularSalida($id_salida, $id_usuario_anulacion = null){
    include("../_conexion/conexion.php");

    // Iniciar transacción para mantener consistencia ¿primordial?
    mysqli_begin_transaction($con);

    try {
        // 1) Obtener la salida (para ubicar id_pedido )
        $id_salida = intval($id_salida);
        $sql_sel = "SELECT id_salida, id_pedido FROM salida WHERE id_salida = $id_salida AND est_salida = 1 LIMIT 1";
        $res_sel = mysqli_query($con, $sql_sel);
        if (!$res_sel) throw new Exception("Error al consultar la salida: " . mysqli_error($con));
        $row = mysqli_fetch_assoc($res_sel);
        if (!$row) {
            throw new Exception("Salida no encontrada o ya anulada.");
        }
        $id_pedido = isset($row['id_pedido']) ? intval($row['id_pedido']) : 0;

        // 2) Marcar la salida como anulada (est_salida = 0). 
        $sql_up = "UPDATE salida SET est_salida = 0";
        $sql_up .= " WHERE id_salida = $id_salida"; //¿editar
        if (!mysqli_query($con, $sql_up)) throw new Exception("Error al anular la salida: " . mysqli_error($con));

        // 3) Desactivar todos los movimientos que fueron creados por esta salida
        $sql_mov_off = "UPDATE movimiento SET est_movimiento = 0 WHERE tipo_orden = 2 AND id_orden = $id_salida";
        if (!mysqli_query($con, $sql_mov_off)) throw new Exception("Error al desactivar movimientos de la salida: " . mysqli_error($con));

        // 4) Si la salida viene de un pedido, reactivar los compromisos (tipo_orden = 5) del pedido
        if ($id_pedido && $id_pedido > 0) {

            $sql_reactivar = "UPDATE movimiento SET est_movimiento = 1 WHERE tipo_orden = 5 AND id_orden = $id_pedido";
            if (!mysqli_query($con, $sql_reactivar)) throw new Exception("Error al reactivar compromisos del pedido: " . mysqli_error($con));

            // ============================================================
            // ACTUALIZAR ESTADO DEL PEDIDO
            // ============================================================
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            require_once("../_modelo/m_pedidos.php");
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estado del pedido actualizado");

        } else {
            error_log("⚠️ Sin pedido asociado, omitiendo reactivación de compromisos y actualización de estado");
        }

        mysqli_commit($con);
        mysqli_close($con);
        return "SI";
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        return "ERROR: " . $e->getMessage();
    }
}*/
function AnularSalida($id_salida, $id_usuario_anulacion = null)
{
    include("../_conexion/conexion.php");

    mysqli_begin_transaction($con);

    try {
        $id_salida = intval($id_salida);
        
        // 1️⃣ OBTENER DATOS
        $sql_sel = "SELECT id_salida, id_pedido 
                    FROM salida 
                    WHERE id_salida = $id_salida 
                    AND est_salida = 1 
                    LIMIT 1";
        
        $res_sel = mysqli_query($con, $sql_sel);
        
        if (!$res_sel) {
            throw new Exception("Error al consultar la salida: " . mysqli_error($con));
        }
        
        $row = mysqli_fetch_assoc($res_sel);
        
        if (!$row) {
            throw new Exception("Salida no encontrada o ya anulada.");
        }
        
        $id_pedido = isset($row['id_pedido']) && $row['id_pedido'] > 0 
                    ? intval($row['id_pedido']) 
                    : null;
        
        error_log("📦 Anulando salida ID: $id_salida | Pedido: " . ($id_pedido ?? 'SIN PEDIDO'));

        // 2️⃣ OBTENER ÍTEMS AFECTADOS
        $items_afectados = [];
        
        if ($id_pedido !== null) {
            $sql_detalles = "SELECT DISTINCT id_pedido_detalle 
                            FROM salida_detalle 
                            WHERE id_salida = $id_salida 
                            AND id_pedido_detalle IS NOT NULL 
                            AND id_pedido_detalle > 0";
            
            $res_detalles = mysqli_query($con, $sql_detalles);
            
            if (!$res_detalles) {
                throw new Exception("Error al obtener detalles: " . mysqli_error($con));
            }
            
            while ($row_det = mysqli_fetch_assoc($res_detalles)) {
                $items_afectados[] = intval($row_det['id_pedido_detalle']);
            }
            
            error_log("📋 Ítems afectados: " . count($items_afectados));
        }

        // 3️⃣ ANULAR SALIDA
        $sql_up = "UPDATE salida 
                   SET est_salida = 0 
                   WHERE id_salida = $id_salida";
        
        if (!mysqli_query($con, $sql_up)) {
            throw new Exception("Error al anular: " . mysqli_error($con));
        }
        
        error_log("✅ Salida anulada");

        // 4️⃣ DESACTIVAR MOVIMIENTOS
        $sql_mov_off = "UPDATE movimiento 
                       SET est_movimiento = 0 
                       WHERE tipo_orden = 2 
                       AND id_orden = $id_salida";
        
        if (!mysqli_query($con, $sql_mov_off)) {
            throw new Exception("Error al desactivar movimientos: " . mysqli_error($con));
        }
        
        error_log("✅ Movimientos desactivados");

        // 5️⃣ COMMIT
        mysqli_commit($con);
        mysqli_close($con);

        error_log("✅ Transacción completada");

        // 6️⃣ ACTUALIZAR ESTADOS (FUERA DE TRANSACCIÓN)
        if ($id_pedido > 0 && !empty($items_afectados)) {
            require_once("../_modelo/m_pedidos.php");
            
            usleep(200000); // 200ms
            
            foreach ($items_afectados as $id_detalle) {
                error_log("   🔒 Actualizando estado del item: $id_detalle");
                VerificarEstadoItemPorDetalle($id_detalle);
            }
            
            error_log("✅ Estados actualizados");
        }
        
        // 7️⃣ ACTUALIZAR ESTADO DEL PEDIDO (SIEMPRE después de anular)
        if ($id_pedido > 0) {
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            
            // 🔹 FORZAR RE-VERIFICACIÓN DESPUÉS DE ANULAR
            require_once("../_modelo/m_pedidos.php");
            
            foreach ($items_afectados as $id_detalle) {
                ReverificarItemAutomaticamente($id_detalle); // ← Siempre verificar
            }
            
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estado del pedido actualizado");
        }
        
        return [
            'success' => true,
            'message' => 'Salida anulada correctamente'
        ];

    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        error_log("❌ Error al anular salida: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


// ============================================================================
// OBTENER DATOS DE UNA SALIDA POR ID (para edición)
// ============================================================================
function ObtenerSalidaPorId($id_salida) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT s.*, 
                   ao.nom_almacen as almacen_origen_nombre,
                   uo.nom_ubicacion as ubicacion_origen_nombre,
                   ad.nom_almacen as almacen_destino_nombre,
                   ud.nom_ubicacion as ubicacion_destino_nombre,
                   CONCAT(per.ape_personal, ' ', per.nom_personal) as personal_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN personal per ON s.id_personal = per.id_personal
            WHERE s.id_salida = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_salida);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $salida_data = null;
    if ($result->num_rows > 0) {
        $salida_data = $result->fetch_assoc();
    }
    
    $stmt->close();
    mysqli_close($con);
    
    return $salida_data;
}

// ============================================================================
// OBTENER DETALLE DE UNA SALIDA (items/productos)
// ============================================================================
function ObtenerDetalleSalida($id_salida) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT sd.*, 
               p.nom_producto,
               um.nom_unidad_medida,
               pd.id_pedido_detalle,
               pd.cant_pedido_detalle
        FROM salida_detalle sd
        INNER JOIN producto p ON sd.id_producto = p.id_producto
        LEFT JOIN pedido_detalle pd ON sd.id_pedido_detalle = pd.id_pedido_detalle
        INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
        WHERE sd.id_salida = ?
        ORDER BY sd.id_salida_detalle";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_salida);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $salida_detalle = [];
    while ($row = $result->fetch_assoc()) {
        $salida_detalle[] = $row;
    }
    
    $stmt->close();
    mysqli_close($con);
    
    return $salida_detalle;
}

// ============================================================================
// VALIDAR SI UNA SALIDA EXISTE Y ESTÁ ACTIVA
// ============================================================================
function ValidarSalidaExiste($id_salida) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT id_salida, est_salida FROM salida WHERE id_salida = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_salida);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $existe = false;
    $activa = false;
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existe = true;
        $activa = ($row['est_salida'] == 1);
    }
    
    $stmt->close();
    mysqli_close($con);
    
    return [
        'existe' => $existe,
        'activa' => $activa
    ];
}

// ============================================================================
// OBTENER ID DEL PEDIDO DE UNA SALIDA
// ============================================================================
function ObtenerPedidoDeSalida($id_salida) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT id_pedido FROM salida WHERE id_salida = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_salida);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $id_pedido = 0;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_pedido = intval($row['id_pedido']);
    }
    
    $stmt->close();
    mysqli_close($con);
    
    return $id_pedido;
}

//-----------------------------------------------------------------------
function ConsultarSalidasPorPedido($id_pedido) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    $sql = "SELECT 
                s.id_salida,
                s.ndoc_salida,
                s.fec_req_salida,
                s.est_salida,
                s.obs_salida,
                s.id_personal_aprueba_salida,
                s.fec_aprueba_salida,
                CONCAT(ud.nom_ubicacion, ' (', ad.nom_almacen, ')') as nom_ubicacion_destino,
                COALESCE(papr.nom_personal, NULL) as nom_personal_recepciona
            FROM salida s
            INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN {$bd_complemento}.personal papr ON s.id_personal_aprueba_salida = papr.id_personal
            WHERE s.id_pedido = $id_pedido
            ORDER BY s.fec_req_salida ASC, s.id_salida DESC";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ConsultarSalidasPorPedido: " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }
    
    $salidas = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $salidas[] = $row;
    }
    
    mysqli_close($con);
    
    return $salidas;
}
/**
 * Validar cantidades al actualizar/crear salidas (similar a OC)
 * Verifica que no se exceda la cantidad verificada para OS
 */
function ValidarCantidadesSalida($id_pedido, $items_salida, $id_salida_actual = null)
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    error_log("⚙ ValidarCantidadesSalida - Pedido: $id_pedido | Salida actual: " . ($id_salida_actual ?? 'NUEVA'));
    
    // 🔹 OBTENER DATOS DEL PEDIDO (almacén/ubicación origen)
    $sql_pedido = "SELECT id_almacen, id_ubicacion FROM pedido WHERE id_pedido = $id_pedido";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $pedido_info = mysqli_fetch_assoc($res_pedido);
    
    if (!$pedido_info) {
        mysqli_close($con);
        return ["No se encontró el pedido"];
    }
    
    $id_almacen_origen = intval($pedido_info['id_almacen']);
    $id_ubicacion_origen = intval($pedido_info['id_ubicacion']);
    
    error_log("   📍 Almacén origen: $id_almacen_origen | Ubicación origen: $id_ubicacion_origen");
    
    foreach ($items_salida as $key => $item) {
        // Obtener id_pedido_detalle
        $id_pedido_detalle = 0;
        
        if (is_numeric($key)) {
            $id_pedido_detalle = isset($item['id_pedido_detalle']) ? intval($item['id_pedido_detalle']) : 0;
        } else {
            $id_pedido_detalle = isset($item['id_pedido_detalle']) ? intval($item['id_pedido_detalle']) : 0;
        }
        
        $cantidad_nueva = floatval($item['cantidad']);
        
        error_log("    Validando detalle ID: $id_pedido_detalle | Cantidad nueva: $cantidad_nueva | Key: $key");
        
        if ($id_pedido_detalle <= 0) {
            error_log("    ADVERTENCIA: id_pedido_detalle no válido para key $key");
            continue;
        }
        
        // 🔹 OBTENER INFORMACIÓN DEL PRODUCTO
        $sql_producto = "SELECT id_producto, cant_pedido_detalle
                        FROM pedido_detalle 
                        WHERE id_pedido_detalle = $id_pedido_detalle
                        LIMIT 1";
        $res = mysqli_query($con, $sql_producto);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row) {
            error_log("    Detalle ID $id_pedido_detalle no encontrado");
            $errores[] = "El detalle ID $id_pedido_detalle no existe";
            continue;
        }
        
        $id_producto = intval($row['id_producto']);
        $cantidad_pedida = floatval($row['cant_pedido_detalle']);
        
        error_log("    Producto ID: $id_producto | Cantidad pedida: $cantidad_pedida");
        
        // 🔥 CORRECCIÓN CRÍTICA: CÁLCULO DE STOCK DISPONIBLE
        
        // PASO 1: Obtener stock físico REAL (sin compromisos)
        $sql_stock_fisico = "SELECT COALESCE(
                                SUM(
                                    CASE
                                        WHEN tipo_movimiento = 1 THEN cant_movimiento
                                        WHEN tipo_movimiento = 2 AND tipo_orden <> 5 THEN -cant_movimiento
                                        ELSE 0
                                    END
                                ), 0) AS stock_fisico
                             FROM movimiento
                             WHERE id_producto = $id_producto
                               AND id_almacen = $id_almacen_origen
                               AND id_ubicacion = $id_ubicacion_origen
                               AND est_movimiento = 1";
        
        $res_fisico = mysqli_query($con, $sql_stock_fisico);
        $row_fisico = mysqli_fetch_assoc($res_fisico);
        $stock_fisico_real = floatval($row_fisico['stock_fisico']);
        
        error_log("    📊 Stock físico REAL (sin compromisos): $stock_fisico_real");
        
        // PASO 2: Obtener cantidad actual en esta salida (desde salida_detalle)
        $cantidad_actual_en_salida = 0;
        
        if ($id_salida_actual !== null && $id_salida_actual > 0) {
            $sql_cantidad_actual = "SELECT COALESCE(SUM(cant_salida_detalle), 0) as cantidad_actual
                                    FROM salida_detalle 
                                    WHERE id_salida = $id_salida_actual 
                                      AND id_producto = $id_producto 
                                      AND est_salida_detalle = 1";
            
            $res_actual = mysqli_query($con, $sql_cantidad_actual);
            $row_actual = mysqli_fetch_assoc($res_actual);
            $cantidad_actual_en_salida = floatval($row_actual['cantidad_actual']);
            
            error_log("    📦 Cantidad actual en salida (ID $id_salida_actual): $cantidad_actual_en_salida");
        }
        
        // 🔥 CÁLCULO CORREGIDO: Stock disponible = físico + lo que ya está en esta salida
        $stock_disponible = $stock_fisico_real + $cantidad_actual_en_salida;
        
        error_log("    ✅ Stock disponible FINAL: $stock_disponible (físico: $stock_fisico_real + en salida: $cantidad_actual_en_salida)");
        
        // 🔹 VALIDACIÓN: La cantidad no debe exceder el stock disponible
        if ($cantidad_nueva > $stock_disponible) {
            error_log("    ❌ EXCEDE - Stock disponible: $stock_disponible | Intentas: $cantidad_nueva");
            
            // Obtener descripción del producto
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            $errores[] = "$descripcion: Stock insuficiente. Disponible: $stock_disponible, Intentaste ordenar: $cantidad_nueva";
        } else {
            error_log("    ✅ VÁLIDO - Dentro del stock disponible");
        }
        
        // 🔹 VALIDACIÓN ADICIONAL: No exceder la cantidad pedida
        $sql_ya_enviado = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_enviado
                           FROM salida_detalle sd
                           INNER JOIN salida s ON sd.id_salida = s.id_salida
                           WHERE sd.id_pedido_detalle = $id_pedido_detalle
                           AND s.est_salida = 1";
        
        if ($id_salida_actual) {
            $sql_ya_enviado .= " AND s.id_salida != $id_salida_actual";
        }
        
        $res_enviado = mysqli_query($con, $sql_ya_enviado);
        $row_enviado = mysqli_fetch_assoc($res_enviado);
        $ya_enviado = floatval($row_enviado['total_enviado']);
        
        $disponible_pedido = $cantidad_pedida - $ya_enviado;
        
        error_log("    📦 Validación pedido - Total: $cantidad_pedida | Ya enviado: $ya_enviado | Disponible: $disponible_pedido");
        
        if ($cantidad_nueva > $disponible_pedido) {
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            $errores[] = "$descripcion: Excede lo pendiente del pedido. Pedido: $cantidad_pedida, Ya enviado: $ya_enviado, Disponible: $disponible_pedido, Intentas: $cantidad_nueva";
        }
    }
    
    mysqli_close($con);
    return $errores;
}

/**
 * Verifica si realmente necesita re-verificación o es un ajuste temporal
 * Retorna TRUE solo si hay problema REAL de stock físico
 */
function verificarSiNecesitaReverificacion($materiales, $id_salida, $id_pedido) {
    include("../_conexion/conexion.php");
    
    error_log("🔍 verificarSiNecesitaReverificacion - Salida: $id_salida | Pedido: $id_pedido");
    
    // Obtener ubicación origen de la salida
    $sql_salida = "SELECT id_almacen_origen, id_ubicacion_origen 
                   FROM salida 
                   WHERE id_salida = $id_salida";
    $res = mysqli_query($con, $sql_salida);
    $salida_info = mysqli_fetch_assoc($res);
    
    if (!$salida_info) {
        error_log("⚠️ No se encontró info de la salida");
        mysqli_close($con);
        return false;
    }
    
    $id_almacen = intval($salida_info['id_almacen_origen']);
    $id_ubicacion = intval($salida_info['id_ubicacion_origen']);
    
    foreach ($materiales as $material) {
        $id_producto = intval($material['id_producto']);
        $cantidad_solicitada = floatval($material['cantidad']);
        
        error_log("   📦 Validando producto $id_producto | Cantidad: $cantidad_solicitada");
        
        // 🔹 PASO 1: Obtener cantidad actual en la salida que se está editando
        $sql_cantidad_actual = "SELECT COALESCE(SUM(cant_salida_detalle), 0) as cantidad_actual
                                FROM salida_detalle 
                                WHERE id_salida = $id_salida 
                                  AND id_producto = $id_producto 
                                  AND est_salida_detalle = 1";
        $res_actual = mysqli_query($con, $sql_cantidad_actual);
        $row_actual = mysqli_fetch_assoc($res_actual);
        $cantidad_actual_en_salida = floatval($row_actual['cantidad_actual']);
        
        error_log("   📊 Cantidad actual en esta salida: $cantidad_actual_en_salida");
        
        // 🔹 PASO 2: Calcular stock físico REAL (SIN compromisos)
        $sql_stock = "SELECT COALESCE(
                        SUM(
                            CASE
                                WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                                WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden <> 5 THEN -mov.cant_movimiento
                                ELSE 0
                            END
                        ), 0) AS stock_fisico_real
                      FROM movimiento mov
                      WHERE mov.id_producto = $id_producto
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento = 1";
        
        $res_stock = mysqli_query($con, $sql_stock);
        $row_stock = mysqli_fetch_assoc($res_stock);
        $stock_fisico_real = floatval($row_stock['stock_fisico_real']);
        
        error_log("   📊 Stock físico REAL: $stock_fisico_real");
        
        // 🔹 PASO 3: Calcular diferencia de cantidad (aumento/disminución)
        $diferencia = $cantidad_solicitada - $cantidad_actual_en_salida;
        
        error_log("   📊 Diferencia: $diferencia (nueva: $cantidad_solicitada - actual: $cantidad_actual_en_salida)");
        
        // 🔹 PASO 4: Verificar si hay stock físico suficiente para el CAMBIO
        if ($diferencia > 0) {
            // Usuario quiere AUMENTAR la cantidad
            if ($diferencia > $stock_fisico_real) {
                error_log("   ❌ Stock REAL insuficiente para aumento. Diferencia: $diferencia > Stock: $stock_fisico_real");
                mysqli_close($con);
                return true; // SÍ necesita re-verificación
            } else {
                error_log("   ✅ Stock REAL suficiente para aumento");
            }
        } else {
            // Usuario quiere DISMINUIR o mantener igual
            error_log("   ✅ Es disminución o sin cambio, no necesita re-verificar");
        }
    }
    
    error_log("   ✅ NO necesita re-verificación (ajuste temporal válido)");
    mysqli_close($con);
    return false;
}

function ObtenerCantidadEnSalidasAnuladasPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    // ✅ VALIDACIÓN: Asegurar que sea un entero válido
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    if ($id_pedido_detalle <= 0) {
        mysqli_close($con);
        return 0.0;
    }
    
    $sql = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_anulado
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
            AND s.est_salida = 0  /* SOLO SALIDAS ANULADAS */
            AND sd.est_salida_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    
    // ✅ VALIDACIÓN: Verificar si la consulta fue exitosa
    if (!$resultado) {
        error_log("❌ ERROR en ObtenerCantidadEnSalidasAnuladasPorDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return 0.0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    
    // ✅ VALIDACIÓN: Verificar que se obtuvo una fila
    if (!$row) {
        error_log("⚠️ No se obtuvo resultado en ObtenerCantidadEnSalidasAnuladasPorDetalle para detalle $id_pedido_detalle");
        mysqli_close($con);
        return 0.0;
    }
    
    mysqli_close($con);
    return floatval($row['total_anulado']);
}


/**
 * Aprobar/Recepcionar una salida
 * Registra quién aprueba y la fecha/hora de recepción
 * Actualiza el estado del pedido si es necesario
 */
function AprobarSalida($id_salida, $id_personal)
{
    include("../_conexion/conexion.php");

    // ✅ Verificar que la salida existe y está activa
    $sql_check = "SELECT est_salida, id_personal_aprueba_salida, id_pedido 
                  FROM salida 
                  WHERE id_salida = '$id_salida'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    // Validar que existe y está activa
    if (!$row_check || $row_check['est_salida'] == 0) {
        mysqli_close($con);
        return false;
    }

    // Validar que no esté ya aprobada
    if (!empty($row_check['id_personal_aprueba_salida'])) {
        mysqli_close($con);
        return false;
    }

    $id_pedido = intval($row_check['id_pedido']);

    // ✅ Validar que el personal existe
    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = '$id_personal'";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        mysqli_close($con);
        error_log("Error: id_personal $id_personal no existe en la tabla personal");
        return false;
    }

    // ✅ Actualizar la salida con los datos de aprobación
    $sql_update = "UPDATE salida 
                   SET id_personal_aprueba_salida = '$id_personal',
                       fec_aprueba_salida = NOW(),
                       est_salida = 2
                   WHERE id_salida = '$id_salida'";

    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        // 🔄 ACTUALIZAR ESTADO DEL PEDIDO SI EXISTE
        if ($id_pedido > 0) {
            verificarYAtenderPedido($id_pedido, $con);
        }
    }

    mysqli_close($con);
    return $res_update;
}

/**
 * Verificar si todas las salidas de un pedido están recepcionadas
 * y actualizar el estado del pedido a ATENDIDO (2) si corresponde
 */
function verificarYAtenderPedido($id_pedido, $con = null)
{
    $cerrar_conexion = false;
    
    if ($con === null) {
        include("../_conexion/conexion.php");
        $cerrar_conexion = true;
    }

    //  unificada maneja TODO
    ActualizarEstadoPedidoUnificado($id_pedido, $con);
    
    if ($cerrar_conexion) {
        mysqli_close($con);
    }
}