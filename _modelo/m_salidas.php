<?php
//=======================================================================
// MODELO: m_salidas.php
//=======================================================================

function GrabarSalida($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                     $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                     $fec_req_salida, $obs_salida, $id_personal_encargado, 
                     $id_personal_recibe, $id_personal, $materiales, $id_pedido = null) 
{
    // ✅ VALIDACIÓN 1: Lógica de cantidades verificadas
    $errores_validacion = ValidarSalidaAntesDeProcesar(
        $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
        $id_almacen_destino, $id_ubicacion_destino, $materiales, $id_pedido
    );
    
    if (!empty($errores_validacion)) {
        return "ERROR DE VALIDACIÓN: " . implode(" | ", $errores_validacion);
    }
    
    // ✅ VALIDACIÓN 2: Stock físico real disponible
    require_once("../_modelo/m_pedidos.php"); 
    $errores_stock = ValidarInventarioDisponibleParaSalida(
        $materiales, 
        $id_almacen_origen, 
        $id_ubicacion_origen, 
        $id_pedido,
        null
    );
    
    if (!empty($errores_stock)) {
        // 🔥 SOLO RE-VERIFICAR CUANDO HAY ERROR DE STOCK
        if ($id_pedido) {
            foreach ($materiales as $material) {
                if (isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                    ReverificarItemAutomaticamente(intval($material['id_pedido_detalle']));
                }
            }
        }
        
        return "ERROR DE STOCK: " . implode(" | ", $errores_stock) . " | Los items han sido re-verificados automáticamente.";
    }
    
    // ✅ SI PASÓ VALIDACIONES, CONTINUAR SIN RE-VERIFICAR
    include("../_conexion/conexion.php");

    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);

    // ✅ NUEVO: Preparar el ID del pedido
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
        
        // Insertar detalles de salida y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);

            // 🔹 Obtener id_pedido_detalle si existe, sino NULL
            $id_pedido_detalle = isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0 
                                ? intval($material['id_pedido_detalle']) 
                                : null;
            
            // 🔹 Preparar valor SQL (NULL o número)
            $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
               
            // Verificar stock una vez más antes de cada inserción (por seguridad)
            $stock_actual = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen, $id_pedido);
            if ($cantidad > $stock_actual) {
                // Rollback: eliminar la salida creada
                mysqli_query($con, "DELETE FROM salida WHERE id_salida = $id_salida");
                mysqli_close($con);
                return "ERROR: Stock insuficiente para '{$descripcion}'. Stock disponible: {$stock_actual}, solicitado: {$cantidad}";
            }
            
            // Insertar detalle de salida
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_pedido_detalle, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_pedido_detalle_sql, $id_producto, '$descripcion', 
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

                if ($id_pedido_detalle !== null && $id_pedido_detalle > 0) {
                    VerificarEstadoItemPorDetalle($id_pedido_detalle);
                }
                
            }
        }

         // Si proviene de un pedido, liberar compromisos de stock
        if ($id_pedido && $id_pedido > 0) {
            /*$sql_liberar = "UPDATE movimiento 
                            SET est_movimiento = 0 
                            WHERE tipo_orden = 5 
                              AND id_orden = $id_pedido 
                              AND est_movimiento = 1";
            mysqli_query($con, $sql_liberar);*/

            require_once("../_modelo/m_pedidos.php");
            ActualizarEstadoPedido($id_pedido);
        }
        
        mysqli_close($con);

        return array(
            'success' => true,
            'id_salida' => intval($id_salida)
        );
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
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             
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
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             
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
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
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

    error_log("🔧 ActualizarSalida - ID Salida: $id_salida");
    
    // Obtener ID_PEDIDO de la salida
    $sql_pedido = "SELECT id_pedido FROM salida WHERE id_salida = $id_salida";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido ? intval($row_pedido['id_pedido']) : 0;
    
    if ($id_pedido <= 0) {
        mysqli_close($con);
        return "ERROR: No se encontró el pedido asociado a esta salida";
    }
    
    // ✅ VALIDACIÓN 1: Cantidades lógicas
    $errores = ValidarCantidadesSalida($id_pedido, $materiales, $id_salida);
    
    if (!empty($errores)) {
        error_log("❌ Errores de validación: " . implode(", ", $errores));
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }
    
    // ✅ VALIDACIÓN 2: Stock físico real
    mysqli_close($con);
    require_once("../_modelo/m_pedidos.php"); 
    $errores_stock = ValidarInventarioDisponibleParaSalida(
        $materiales,
        $id_almacen_origen,
        $id_ubicacion_origen,
        $id_pedido,
        $id_salida  // ← CRÍTICO: Pasar id_salida para excluir sus movimientos
    );
    
    if (!empty($errores_stock)) {
        error_log("❌ Errores de stock físico: " . implode(", ", $errores_stock));
        
        // 🔥 CRÍTICO: Solo re-verificar si es un problema REAL, no temporal
        $necesita_reverificacion = verificarSiNecesitaReverificacion($materiales, $id_salida, $id_pedido);
        
        error_log("🔍 ¿Necesita re-verificación? " . ($necesita_reverificacion ? "SÍ" : "NO"));
        
        if ($necesita_reverificacion) {
            error_log("⚠️ Stock REAL insuficiente, re-verificando items...");
            
            foreach ($materiales as $material) {
                if (isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                    ReverificarItemAutomaticamente(intval($material['id_pedido_detalle']));
                }
            }
            
            return "ERROR DE STOCK: " . implode(" | ", $errores_stock) . " | Los items han sido re-verificados automáticamente.";
        } else {
            error_log("ℹ️ Es ajuste temporal, NO re-verificando");
            return "ERROR DE STOCK: " . implode(" | ", $errores_stock);
        }
    }
    
    // ✅ SI PASÓ VALIDACIONES, CONTINUAR SIN RE-VERIFICAR
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
        
        error_log("👤 ID Personal: $id_personal");
        
        // 🔹 RASTREAR DETALLES AFECTADOS (para regenerar movimientos)
        $detalles_afectados = array();

        error_log("📦 Materiales recibidos para procesar: " . count($materiales));

        // 🔹 PROCESAR CADA MATERIAL
        foreach ($materiales as $key => $material) {
            
            error_log("🔍 Procesando material key: $key");
            error_log("   Datos: " . print_r($material, true));
            
            // 🔹 VERIFICAR SI ES NUEVO O EXISTENTE
            $es_nuevo = isset($material['es_nuevo']) && $material['es_nuevo'] == '1';
            
            error_log("   ¿Es nuevo?: " . ($es_nuevo ? 'SÍ' : 'NO'));
            
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // 🔹 MANEJO DE id_pedido_detalle
            $id_pedido_detalle = null;
            if (isset($material['id_pedido_detalle']) && !empty($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
                $id_pedido_detalle = intval($material['id_pedido_detalle']);
            }
            
            $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
            
            if ($es_nuevo) {
                // ============================================================
                // ✅ INSERTAR NUEVO DETALLE
                // ============================================================
                error_log("   🆕 Insertando nuevo detalle - Producto: $id_producto | Cantidad: $cantidad");
                
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
                // ✅ ACTUALIZAR DETALLE EXISTENTE
                // ============================================================
                
                // 🔥 CAMBIO CRÍTICO: Obtener el ID desde $material, NO desde $key
                if (!isset($material['id_salida_detalle']) || empty($material['id_salida_detalle'])) {
                    error_log("⚠️ Material sin id_salida_detalle: " . print_r($material, true));
                    continue;
                }
                
                $id_salida_detalle = intval($material['id_salida_detalle']); // ← AQUÍ ESTÁ EL CAMBIO
                
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
            // ✅ VERIFICAR ESTADO DEL ITEM (SOLO SI TIENE PEDIDO_DETALLE)
            // ============================================================
            if ($id_pedido_detalle !== null && $id_pedido_detalle > 0) {
                error_log("   🔍 Verificando estado del detalle: $id_pedido_detalle");
                VerificarEstadoItemPorDetalle($id_pedido_detalle);
            }
        }

        error_log("📊 Total de detalles afectados para regenerar movimientos: " . count($detalles_afectados));
        
        // ============================================================
        // 🔹 REGENERAR MOVIMIENTOS DIRECTAMENTE DESDE $materiales
        // ============================================================
        error_log("🔄 Regenerando movimientos...");

        // 1. Desactivar TODOS los movimientos anteriores de esta salida
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_salida AND tipo_orden = 2";
        mysqli_query($con, $sql_del_mov);

        error_log("   🗑️ Movimientos anteriores desactivados");

        // 2. Crear nuevos movimientos directamente desde $materiales
        $contador = 0;
        foreach ($materiales as $key => $material) {
            
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            error_log("   📦 Generando movimientos para producto: $id_producto | Cantidad: $cantidad");
            
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
                error_log("      ✅ Movimiento SALIDA creado (Almacén: $id_almacen_origen)");
            } else {
                error_log("      ❌ ERROR movimiento SALIDA: " . mysqli_error($con));
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
        // 🔹 ACTUALIZAR ESTADO DEL PEDIDO (SOLO SI EXISTE)
        // ============================================================
        if ($id_pedido !== null && $id_pedido > 0) {
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
        $descripcion = $material['descripcion'];
        
        // Obtener stock actual del producto en la ubicación origen
        $stock_disponible = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen, $id_pedido);
        
         // Validar stock
        
        if ($stock_disponible <= 0) {
            $errores[] = "El producto '{$descripcion}' no tiene stock disponible en la ubicación origen seleccionada.";
        } elseif ($cantidad > $stock_disponible) {
            $errores[] = "La cantidad solicitada para '{$descripcion}' ({$cantidad}) excede el stock disponible ({$stock_disponible}).";
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
        // ============================================================
        // 1️⃣ OBTENER DATOS DE LA SALIDA
        // ============================================================
        $id_salida = intval($id_salida);
        
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

        // ============================================================
        // 2️⃣ OBTENER ÍTEMS AFECTADOS (ANTES DE ANULAR)
        // ============================================================
        $items_afectados = [];
        
        if ($id_pedido !== null) {
            $sql_detalles = "SELECT DISTINCT id_pedido_detalle 
                            FROM salida_detalle 
                            WHERE id_salida = $id_salida 
                            AND id_pedido_detalle IS NOT NULL 
                            AND id_pedido_detalle > 0";
            
            $res_detalles = mysqli_query($con, $sql_detalles);
            
            if (!$res_detalles) {
                throw new Exception("Error al obtener detalles de la salida: " . mysqli_error($con));
            }
            
            while ($row_det = mysqli_fetch_assoc($res_detalles)) {
                $items_afectados[] = intval($row_det['id_pedido_detalle']);
            }
            
            error_log("📋 Ítems afectados: " . count($items_afectados));
        }

        // ============================================================
        // 3️⃣ ANULAR LA SALIDA
        // ============================================================
        $sql_up = "UPDATE salida 
                   SET est_salida = 0 
                   WHERE id_salida = $id_salida";
        
        if (!mysqli_query($con, $sql_up)) {
            throw new Exception("Error al anular la salida: " . mysqli_error($con));
        }
        
        error_log("✅ Salida anulada");

        // ============================================================
        // 4️⃣ DESACTIVAR MOVIMIENTOS DE LA SALIDA
        // ============================================================
        $sql_mov_off = "UPDATE movimiento 
                       SET est_movimiento = 0 
                       WHERE tipo_orden = 2 
                       AND id_orden = $id_salida";
        
        if (!mysqli_query($con, $sql_mov_off)) {
            throw new Exception("Error al desactivar movimientos de la salida: " . mysqli_error($con));
        }
        
        error_log("✅ Movimientos de salida desactivados");

        // ============================================================
        // 5️⃣ SI HAY PEDIDO: REACTIVAR COMPROMISOS
        // ============================================================
        if ($id_pedido !== null) {
            
            $sql_reactivar = "UPDATE movimiento 
                            SET est_movimiento = 1 
                            WHERE tipo_orden = 5 
                            AND id_orden = $id_pedido";
            
            if (!mysqli_query($con, $sql_reactivar)) {
                throw new Exception("Error al reactivar compromisos del pedido: " . mysqli_error($con));
            }
            
            error_log("✅ Compromisos del pedido reactivados");
        }

        // ============================================================
        // 6️⃣ COMMIT **ANTES** DE ACTUALIZAR ESTADOS
        // ============================================================
        mysqli_commit($con);
        mysqli_close($con);

        error_log("✅ Transacción completada, movimientos confirmados");

        // ============================================================
        // 7️⃣ ACTUALIZAR SOLO ESTADOS (SIN RECALCULAR CANTIDADES)
        // ============================================================
        if ($id_pedido > 0 && !empty($items_afectados)) {
            require_once("../_modelo/m_pedidos.php");
            
            error_log("🔄 Esperando estabilización de BD...");
            usleep(200000); // 200ms para asegurar consistencia
            
            // ✅ SOLO actualizar estados (abierto/cerrado)
            // NO recalcular cantidades verificadas (OS/OC)
            foreach ($items_afectados as $id_detalle) {
                error_log("   🔒 Actualizando estado del item: $id_detalle");
                VerificarEstadoItemPorDetalle($id_detalle);
            }
            
            error_log("✅ Estados actualizados para " . count($items_afectados) . " items");
        }
        
        // ============================================================
        // 8️⃣ ACTUALIZAR ESTADO DEL PEDIDO
        // ============================================================
        if ($id_pedido > 0) {
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estado del pedido actualizado");
        }
        
        // ============================================================
        // 9️⃣ RETORNAR ÉXITO
        // ============================================================
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

function ConsultarSalidasPorPedido($id_pedido) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    $sql = "SELECT 
                s.id_salida,
                s.ndoc_salida,
                s.fec_req_salida,
                s.est_salida,
                s.obs_salida,
                CONCAT(ud.nom_ubicacion, ' (', ad.nom_almacen, ')') as nom_ubicacion_destino
            FROM salida s
            INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            WHERE s.id_pedido = $id_pedido
            ORDER BY s.fec_req_salida ASC, s.id_salida DESC";
    
    $resultado = mysqli_query($con, $sql);
    
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
    
    error_log(" ValidarCantidadesSalida - Pedido: $id_pedido | Salida actual: " . ($id_salida_actual ?? 'NUEVA'));
    
    foreach ($items_salida as $key => $item) {
        //  OBTENER id_pedido_detalle
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
        
        // 🔹 Obtener cantidad verificada para OS de ESTE detalle específico
        $sql_verificada = "SELECT cant_os_pedido_detalle, id_producto
                           FROM pedido_detalle 
                           WHERE id_pedido_detalle = $id_pedido_detalle
                           LIMIT 1";
        $res = mysqli_query($con, $sql_verificada);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row || $row['cant_os_pedido_detalle'] === null) {
            error_log("    Detalle ID $id_pedido_detalle no está verificado para OS");
            $errores[] = "El detalle ID $id_pedido_detalle no está verificado para orden de salida";
            continue;
        }
        
        $cant_verificada_os = floatval($row['cant_os_pedido_detalle']);
        $id_producto = intval($row['id_producto']);
        
        error_log("    Cantidad verificada OS: $cant_verificada_os | Producto ID: $id_producto");
        
        //  CALCULAR CANTIDAD YA ORDENADA EN SALIDAS PARA ESTE DETALLE
        $where_salida = "";
        if ($id_salida_actual) {
            $where_salida = "AND s.id_salida != $id_salida_actual";
        }
        
        $sql_ordenada = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_ordenado
                         FROM salida_detalle sd
                         INNER JOIN salida s ON sd.id_salida = s.id_salida
                         WHERE sd.id_pedido_detalle = $id_pedido_detalle
                         AND s.est_salida != 0
                         AND sd.est_salida_detalle = 1
                         $where_salida";
        
        error_log("    SQL Ordenada OS: $sql_ordenada");
        
        $res_ord = mysqli_query($con, $sql_ordenada);
        $row_ord = mysqli_fetch_assoc($res_ord);
        $ya_ordenado_os = floatval($row_ord['total_ordenado']);
        
        error_log("    Ya ordenado OS (sin esta salida): $ya_ordenado_os");
        
        // 🔹 Validación: la cantidad nueva + ya ordenado NO debe exceder lo verificado para OS
        $disponible = $cant_verificada_os - $ya_ordenado_os;
        $nuevo_total = $ya_ordenado_os + $cantidad_nueva;
        
        error_log("    Disponible OS: $disponible | Nuevo total: $nuevo_total");
        
        if ($cantidad_nueva > $disponible) {
            error_log("    EXCEDE - Disponible OS: $disponible | Intentas: $cantidad_nueva");
            
            //  OBTENER DESCRIPCIÓN DEL PRODUCTO
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            $errores[] = "$descripcion (Detalle $id_pedido_detalle): Cantidad excede lo verificado para OS. Verificado OS: $cant_verificada_os, Ya ordenado: $ya_ordenado_os, Disponible: $disponible, Intentaste ordenar: $cantidad_nueva";
        } else {
            error_log("    VÁLIDO");
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