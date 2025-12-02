<?php
//=======================================================================
// MODELO: m_salidas.php
//=======================================================================
require_once("m_pedidos.php");
// Ahora crea la salida en estado PENDIENTE (1) sin generar movimientos
//=======================================================================
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
        $mensaje_error = is_array($errores_validacion) 
            ? implode(" | ", $errores_validacion) 
            : strval($errores_validacion);
        
        //  DETECTAR SI ES ERROR DE STOCK
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
            
            return "ERROR DE STOCK: " . $mensaje_error . " Las cantidades del pedido han sido re-verificadas automáticamente.";
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
        
        $mensaje_error = is_array($errores_stock) 
            ? implode(" | ", $errores_stock) 
            : strval($errores_stock);
        
        return "ERROR DE STOCK: " . $mensaje_error . " Las cantidades del pedido han sido re-verificadas automáticamente.";
    }
        
    //  SI PASÓ VALIDACIONES, CONTINUAR
    include("../_conexion/conexion.php");

    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);
    $id_pedido_sql = ($id_pedido && $id_pedido > 0) ? intval($id_pedido) : "NULL";

    //  CAMBIO CRÍTICO: Crear en estado PENDIENTE (1)
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
        
        //  INSERTAR DETALLES (sin movimientos)
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_pedido_detalle = isset($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0 
                                ? intval($material['id_pedido_detalle']) 
                                : null;
            $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
               
            //  VALIDACIÓN FINAL DE SEGURIDAD
            $stock_actual = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen, $id_pedido);
            if ($cantidad > $stock_actual) {
                if ($id_pedido && $id_pedido_detalle !== null) {
                    ReverificarItemAutomaticamente($id_pedido_detalle);
                }
                
                mysqli_query($con, "DELETE FROM salida WHERE id_salida = $id_salida");
                mysqli_close($con);
                
                return "ERROR DE STOCK: Stock insuficiente para '{$descripcion}'. Se requieren {$cantidad} unidades pero solo hay {$stock_actual} disponibles.";
            }
            
            //  Insertar detalle
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_pedido_detalle, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_pedido_detalle_sql, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                error_log("❌ Error al insertar detalle: " . mysqli_error($con));
            }
        }

        mysqli_close($con);

        // NO actualizar estados hasta aprobar
        error_log(" Salida creada en estado PENDIENTE (ID: $id_salida)");
        
        return array('success' => true, 'id_salida' => intval($id_salida));
    } else {
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}

function AprobarSalidaConMovimientos($id_salida, $id_personal_aprueba)
{
    include("../_conexion/conexion.php");

    mysqli_begin_transaction($con);

    try {
        $id_salida = intval($id_salida);
        
        //  VALIDAR QUE LA SALIDA EXISTE Y ESTÁ PENDIENTE
        $sql_check = "SELECT 
                        s.id_salida, 
                        s.est_salida, 
                        s.id_pedido,
                        s.id_almacen_origen,
                        s.id_ubicacion_origen,
                        s.id_almacen_destino,
                        s.id_ubicacion_destino,
                        s.id_personal,
                        s.id_personal_aprueba_salida
                      FROM salida s
                      WHERE s.id_salida = $id_salida";
        
        $res_check = mysqli_query($con, $sql_check);
        
        if (!$res_check) {
            throw new Exception("Error al consultar salida: " . mysqli_error($con));
        }
        
        $row_check = mysqli_fetch_assoc($res_check);
        
        if (!$row_check) {
            throw new Exception("Salida no encontrada");
        }
        
        //  VALIDAR ESTADO
        if ($row_check['est_salida'] != 1) {
            switch ($row_check['est_salida']) {
                case 0:
                    $estado_texto = 'anulada';
                    break;
                case 2:
                    $estado_texto = 'recepcionada';
                    break;
                case 3:
                    $estado_texto = 'aprobada';
                    break;
                default:
                    $estado_texto = 'en estado desconocido';
            }

            throw new Exception("Esta salida ya está {$estado_texto}");
        }

        
        //  VALIDAR QUE NO TENGA APROBACIÓN PREVIA
        if (!empty($row_check['id_personal_aprueba_salida'])) {
            throw new Exception("Esta salida ya fue aprobada anteriormente");
        }
        
        $id_pedido = intval($row_check['id_pedido']);
        $id_almacen_origen = intval($row_check['id_almacen_origen']);
        $id_ubicacion_origen = intval($row_check['id_ubicacion_origen']);
        $id_almacen_destino = intval($row_check['id_almacen_destino']);
        $id_ubicacion_destino = intval($row_check['id_ubicacion_destino']);
        $id_personal_registro = intval($row_check['id_personal']);
        
        error_log("📦 Aprobando salida ID: $id_salida | Pedido: $id_pedido");
        
        //  OBTENER DETALLES DE LA SALIDA
        $sql_detalles = "SELECT 
                            sd.id_salida_detalle,
                            sd.id_producto,
                            sd.id_pedido_detalle,
                            sd.cant_salida_detalle,
                            p.nom_producto
                         FROM salida_detalle sd
                         INNER JOIN producto p ON sd.id_producto = p.id_producto
                         WHERE sd.id_salida = $id_salida
                         AND sd.est_salida_detalle = 1";
        
        $res_detalles = mysqli_query($con, $sql_detalles);
        
        if (!$res_detalles) {
            throw new Exception("Error al obtener detalles: " . mysqli_error($con));
        }
        
        $detalles = [];
        while ($row = mysqli_fetch_assoc($res_detalles)) {
            $detalles[] = $row;
        }
        
        if (empty($detalles)) {
            throw new Exception("No hay detalles en esta salida");
        }
        
        error_log("📋 Detalles a procesar: " . count($detalles));
        
        //  VALIDAR STOCK DISPONIBLE PARA CADA PRODUCTO
        foreach ($detalles as $detalle) {
            $id_producto = intval($detalle['id_producto']);
            $cantidad = floatval($detalle['cant_salida_detalle']);
            $nombre_producto = $detalle['nom_producto'];
            
            //  CALCULAR STOCK FÍSICO REAL
           $sql_stock = "SELECT COALESCE(
                            SUM(
                                CASE
                                    -- INGRESOS
                                    WHEN mov.tipo_movimiento = 1 THEN
                                        CASE
                                            --  Devoluciones confirmadas SÍ cuentan
                                            WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                            --  Ingresos normales SÍ cuentan
                                            WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                            --  Devoluciones pendientes NO cuentan
                                            ELSE 0
                                        END
                                    -- SALIDAS: Siempre restan
                                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                                    ELSE 0
                                END
                            ), 0) AS stock_fisico_real
                        FROM movimiento mov
                        WHERE mov.id_producto = $id_producto
                            AND mov.id_almacen = $id_almacen_origen
                            AND mov.id_ubicacion = $id_ubicacion_origen
                            AND mov.est_movimiento != 0";
            
            $res_stock = mysqli_query($con, $sql_stock);
            $row_stock = mysqli_fetch_assoc($res_stock);
            $stock_disponible = floatval($row_stock['stock_fisico_real']);
            
            error_log("    '{$nombre_producto}' | Stock: $stock_disponible | Necesita: $cantidad");
            
            //  VALIDACIÓN: Si NO hay stock suficiente → ANULAR Y SALIR
            if ($stock_disponible < $cantidad) {
                error_log("   ❌ STOCK INSUFICIENTE - Anulando salida");
                
                //  ANULAR LA SALIDA
                $sql_anular = "UPDATE salida SET est_salida = 0 WHERE id_salida = $id_salida";
                mysqli_query($con, $sql_anular);
                
                //  RE-VERIFICAR ITEMS AFECTADOS
                if ($id_pedido > 0) {
                    foreach ($detalles as $det) {
                        if (!empty($det['id_pedido_detalle']) && $det['id_pedido_detalle'] > 0) {
                            require_once("../_modelo/m_pedidos.php");
                            ReverificarItemAutomaticamente(intval($det['id_pedido_detalle']));
                        }
                    }
                }
                
                mysqli_commit($con);
                mysqli_close($con);
                
                return [
                    'success' => false,
                    'anulada' => true,
                    'message' => "❌ Stock insuficiente para '{$nombre_producto}'. Disponible: {$stock_disponible}, Necesario: {$cantidad}. La salida ha sido ANULADA automáticamente y las cantidades del pedido se han ajustado."
                ];
            }
        }
        
        error_log(" Stock validado - Generando movimientos");
        
        // GENERAR MOVIMIENTOS
        foreach ($detalles as $detalle) {
            $id_producto = intval($detalle['id_producto']);
            $cantidad = floatval($detalle['cant_salida_detalle']);
            
            //  Movimiento SALIDA (resta stock en origen)
            $sql_mov_salida = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                              ) VALUES (
                                $id_personal_registro, $id_salida, $id_producto, $id_almacen_origen, 
                                $id_ubicacion_origen, 2, 2, 
                                $cantidad, NOW(), 1
                              )";
            
            if (!mysqli_query($con, $sql_mov_salida)) {
                throw new Exception("Error al crear movimiento de salida: " . mysqli_error($con));
            }
            
            error_log("    Movimiento SALIDA creado");
            
            //  Movimiento INGRESO (suma stock en destino)
            $sql_mov_ingreso = "INSERT INTO movimiento (
                                 id_personal, id_orden, id_producto, id_almacen, 
                                 id_ubicacion, tipo_orden, tipo_movimiento, 
                                 cant_movimiento, fec_movimiento, est_movimiento
                               ) VALUES (
                                 $id_personal_registro, $id_salida, $id_producto, $id_almacen_destino, 
                                 $id_ubicacion_destino, 2, 1, 
                                 $cantidad, NOW(), 1
                               )";
            
            if (!mysqli_query($con, $sql_mov_ingreso)) {
                throw new Exception("Error al crear movimiento de ingreso: " . mysqli_error($con));
            }
            
            error_log("   ✅ Movimiento INGRESO creado");
        }
        
        //  ACTUALIZAR ESTADO A APROBADO (3)
        $sql_aprobar = "UPDATE salida 
                        SET est_salida = 3,
                            id_personal_aprueba_salida = $id_personal_aprueba,
                            fec_aprueba_salida = NOW()
                        WHERE id_salida = $id_salida";
        
        if (!mysqli_query($con, $sql_aprobar)) {
            throw new Exception("Error al aprobar salida: " . mysqli_error($con));
        }
        
        error_log("✅ Salida aprobada - Estado: 3");
        
        //  COMMIT
        mysqli_commit($con);

        //  ENVIAR CORREO AL RECEPTOR
        EnviarCorreoSalidaAprobada($id_salida);

        mysqli_close($con);

        // ACTUALIZAR ESTADOS (FUERA DE TRANSACCIÓN)
        if ($id_pedido > 0) {
            require_once("../_modelo/m_pedidos.php");
            
            usleep(200000); // 200ms
            
            foreach ($detalles as $detalle) {
                if (!empty($detalle['id_pedido_detalle']) && $detalle['id_pedido_detalle'] > 0) {
                    $id_detalle = intval($detalle['id_pedido_detalle']);
                    error_log("   🔄 Actualizando estados del item: $id_detalle");
                    
                    VerificarEstadoItemPorDetalle($id_detalle);
                    ReverificarItemAutomaticamente($id_detalle);
                }
            }
            
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            ActualizarEstadoPedido($id_pedido);
        }
        
        error_log(" Aprobación completada exitosamente");
        
        return [
            'success' => true,
            'message' => ' Salida aprobada correctamente. Los movimientos de stock han sido registrados.'
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        error_log(" Error al aprobar salida: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
function DenegarSalida($id_salida, $id_personal_deniega_salida)
{
    include("../_conexion/conexion.php");

    mysqli_begin_transaction($con);

    try {
        $id_salida = intval($id_salida);
        $id_personal_deniega_salida = intval($id_personal_deniega_salida);
        
        // PASO 1: OBTENER DATOS DE LA SALIDA
        $sql_sel = "SELECT id_salida, id_pedido, est_salida 
                    FROM salida 
                    WHERE id_salida = $id_salida 
                    LIMIT 1";
        
        $res_sel = mysqli_query($con, $sql_sel);
        
        if (!$res_sel) {
            throw new Exception("Error al consultar la salida: " . mysqli_error($con));
        }
        
        $row = mysqli_fetch_assoc($res_sel);
        
        if (!$row) {
            throw new Exception("Salida no encontrada.");
        }
        
        $estado_actual = intval($row['est_salida']);
        $id_pedido = isset($row['id_pedido']) && $row['id_pedido'] > 0 
                    ? intval($row['id_pedido']) 
                    : null;
        
        error_log("🚫 Denegando salida ID: $id_salida | Estado: $estado_actual | Pedido: " . ($id_pedido ?? 'SIN PEDIDO'));

        // VALIDAR QUE ESTÉ PENDIENTE
        if ($estado_actual != 1) {
            $estados = [
                0 => 'ANULADA',
                2 => 'RECEPCIONADA',
                3 => 'APROBADA',
                4 => 'DENEGADA'
            ];
            $nombre_estado = $estados[$estado_actual] ?? 'DESCONOCIDO';
            throw new Exception("No se puede denegar. La salida está en estado $nombre_estado");
        }

        // PASO 2: OBTENER ÍTEMS AFECTADOS (ANTES DE DENEGAR)
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

        // PASO 3: DENEGAR LA SALIDA
        $sql_denegar = "UPDATE salida 
                       SET est_salida = 4,
                           id_personal_deniega_salida = $id_personal_deniega_salida,
                           fec_deniega_salida = NOW()
                       WHERE id_salida = $id_salida";
        
        if (!mysqli_query($con, $sql_denegar)) {
            throw new Exception("Error al denegar: " . mysqli_error($con));
        }
        
        error_log("✅ Salida denegada - Estado cambiado a 4");

        // PASO 4: COMMIT
        mysqli_commit($con);

        //  ENVIAR CORREO
        EnviarCorreoSalidaDenegada($id_salida);

        mysqli_close($con);

        error_log("✅ Transacción completada");

        // ═══════════════════════════════════════════════════════
        // 🔥 CRÍTICO: ACTUALIZAR ESTADOS (FUERA DE TRANSACCIÓN)
        // ═══════════════════════════════════════════════════════
        if ($id_pedido > 0) {
            require_once("../_modelo/m_pedidos.php");
            
            usleep(200000); // 200ms
            
            // Verificar estados de items
            if (!empty($items_afectados)) {
                error_log("🔄 Verificando estados de " . count($items_afectados) . " items");
                foreach ($items_afectados as $id_detalle) {
                    error_log("   🔍 Item: $id_detalle");
                    VerificarEstadoItemPorDetalle($id_detalle);
                }
            }
            
            // 🔥 LLAMAR A LA FUNCIÓN UNIFICADA
            error_log("📋 Llamando a ActualizarEstadoPedidoUnificado($id_pedido)");
            ActualizarEstadoPedidoUnificado($id_pedido);
            
            error_log("✅ Estados actualizados");
        }
        
        return [
            'success' => true,
            'message' => '✅ Salida denegada correctamente.'
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        error_log("❌ Error al denegar: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
function RecepcionarSalida($id_salida, $id_personal_recepciona)
{
    include("../_conexion/conexion.php");

    $id_salida = intval($id_salida);
    $id_personal_recepciona = intval($id_personal_recepciona);

    // Verificar que la salida existe y está APROBADA
    $sql_check = "SELECT est_salida, id_personal_recepciona_salida, id_pedido 
                  FROM salida 
                  WHERE id_salida = $id_salida";
    
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check) {
        error_log("RecepcionarSalida: Error en consulta: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $row_check = mysqli_fetch_assoc($res_check);

    if (!$row_check) {
        error_log("RecepcionarSalida: Salida ID $id_salida no encontrada");
        mysqli_close($con);
        return false;
    }

    // Solo permitir recepcionar si está APROBADA (estado 3)
    if ($row_check['est_salida'] != 3) {
        $estado_actual = $row_check['est_salida'];
        $estados = [
            0 => 'ANULADA',
            1 => 'PENDIENTE',
            2 => 'RECEPCIONADA',
            3 => 'APROBADA'
        ];
        $nombre_estado = $estados[$estado_actual] ?? 'DESCONOCIDO';
        error_log("RecepcionarSalida: Salida ID $id_salida está en estado $nombre_estado ($estado_actual)");
        mysqli_close($con);
        return false;
    }

    if (!empty($row_check['id_personal_recepciona_salida'])) {
        error_log("RecepcionarSalida: Salida ID $id_salida ya recepcionada");
        mysqli_close($con);
        return false;
    }

    $id_pedido = isset($row_check['id_pedido']) && $row_check['id_pedido'] > 0 
                ? intval($row_check['id_pedido']) 
                : 0;

    // Validar que el personal existe
    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = $id_personal_recepciona";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        error_log("RecepcionarSalida: Personal ID $id_personal_recepciona no existe");
        mysqli_close($con);
        return false;
    }

    //  Actualizar estado a RECEPCIONADA (2)
    $sql_update = "UPDATE salida 
                   SET est_salida = 2,
                       id_personal_recepciona_salida = $id_personal_recepciona,
                       fec_recepciona_salida = NOW()
                   WHERE id_salida = $id_salida";

    $res_update = mysqli_query($con, $sql_update);

    if (!$res_update) {
        error_log("RecepcionarSalida: Error en UPDATE: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }

    $filas_afectadas = mysqli_affected_rows($con);
    
    if ($filas_afectadas == 0) {
        error_log("RecepcionarSalida: No se actualizó ninguna fila");
        mysqli_close($con);
        return false;
    }

    error_log(" Salida ID $id_salida recepcionada por personal ID $id_personal_recepciona");
    
    //  ENVIAR CORREO
    EnviarCorreoSalidaRecepcionada($id_salida);

    mysqli_close($con);

    //  ACTUALIZAR ESTADO DEL PEDIDO
    if ($id_pedido > 0) {
        error_log("📋 Actualizando estado del pedido: $id_pedido");
        require_once("../_modelo/m_pedidos.php");
        ActualizarEstadoPedidoUnificado($id_pedido); // ✅ Usar función unificada
    }

    return true;
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
                s.fec_aprueba_salida,
                COALESCE(precep.nom_personal, '-') AS nom_recepciona,
                s.fec_recepciona_salida
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
             LEFT JOIN {$bd_complemento}.personal precep ON s.id_personal_recepciona_salida = precep.id_personal
             ORDER BY s.fec_salida DESC";

    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("❌ Error en MostrarSalidas: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
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

    if ($fecha_inicio && $fecha_fin) {
        $fecha_inicio = mysqli_real_escape_string($con, $fecha_inicio);
        $fecha_fin = mysqli_real_escape_string($con, $fecha_fin);
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
                s.fec_aprueba_salida,
                COALESCE(precep.nom_personal, '-') AS nom_recepciona,
                s.fec_recepciona_salida
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
             LEFT JOIN {$bd_complemento}.personal precep ON s.id_personal_recepciona_salida = precep.id_personal
             WHERE 1=1
             $whereFecha
             ORDER BY s.fec_salida DESC";

    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("❌ Error en MostrarSalidasFecha: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
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

    $id_salida = intval($id_salida);

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
                COALESCE(precep.nom_personal, '-') AS nom_recepciona
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
             LEFT JOIN {$bd_complemento}.personal precep ON s.id_personal_recepciona_salida = precep.id_personal
             WHERE s.id_salida = $id_salida";
    
    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("❌ Error en ConsultarSalida: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
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
                p.cod_material,
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
/**
 *  FUNCIÓN CORREGIDA: ActualizarSalida()
 * 
 * CAMBIOS PRINCIPALES:
 * 1. Re-verificación movida FUERA del bucle
 * 2. Array para evitar re-verificaciones duplicadas
 * 3. Validación de stock corregida (por detalle, no por producto global)
 * 4. Exclusión de movimientos de la salida actual en cálculos
 */
function ActualizarSalida($id_salida, $id_almacen_origen, $id_ubicacion_origen,
                         $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                         $fec_req_salida, $obs_salida, $id_personal_encargado, 
                         $id_personal_recibe, $materiales) 
{
    include("../_conexion/conexion.php");

    error_log("🔧 ActualizarSalida - ID Salida: $id_salida");
    
    // ============================================================
    // 🔍 PASO 1: VERIFICAR ESTADO DE LA SALIDA
    // ============================================================
    $sql_estado = "SELECT est_salida, id_pedido FROM salida WHERE id_salida = $id_salida";
    $res_estado = mysqli_query($con, $sql_estado);
    $row_estado = mysqli_fetch_assoc($res_estado);
    
    if (!$row_estado) {
        mysqli_close($con);
        return "ERROR: Salida no encontrada";
    }
    
    $estado_actual = intval($row_estado['est_salida']);
    $id_pedido = intval($row_estado['id_pedido']);
    $tiene_pedido = ($id_pedido > 0);
    
    error_log("📊 Estado actual: $estado_actual (0=ANULADA, 1=PENDIENTE, 2=RECEPCIONADA, 3=APROBADA)");
    
    // ❌ NO PERMITIR EDITAR SI NO ESTÁ PENDIENTE
    if ($estado_actual != 1) {
        mysqli_close($con);
        $estados = [
            0 => 'anulada',
            2 => 'recepcionada', 
            3 => 'aprobada'
        ];
        $estado_texto = $estados[$estado_actual] ?? 'en estado desconocido';
        return "ERROR: No se puede editar una salida $estado_texto";
    }
    
    // ============================================================
    // ✅ VALIDACIÓN DE STOCK (SOLO SI HAY PEDIDO)
    // ============================================================
    $errores = [];

    if ($tiene_pedido) {
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $id_pedido_detalle = isset($material['id_pedido_detalle']) ? intval($material['id_pedido_detalle']) : 0;
            $cantidad_nueva = floatval($material['cantidad']);
            
            if ($id_pedido_detalle <= 0) continue;
            
            // Obtener cantidad actual en esta salida
            $sql_cantidad_actual = "SELECT COALESCE(cant_salida_detalle, 0) as cantidad_actual
                                    FROM salida_detalle 
                                    WHERE id_salida = $id_salida 
                                      AND id_pedido_detalle = $id_pedido_detalle
                                      AND est_salida_detalle = 1
                                    LIMIT 1";
            
            $res_actual = mysqli_query($con, $sql_cantidad_actual);
            $row_actual = mysqli_fetch_assoc($res_actual);
            $cantidad_actual_en_salida = floatval($row_actual['cantidad_actual']);
            
            error_log("   📦 Cantidad actual en esta salida (detalle $id_pedido_detalle): $cantidad_actual_en_salida");
            
            // Calcular stock físico REAL (excluyendo esta salida)
            $sql_stock = "SELECT COALESCE(
                            SUM(
                                CASE
                                    -- INGRESOS
                                    WHEN mov.tipo_movimiento = 1 THEN
                                        CASE
                                            --  Devoluciones confirmadas SÍ cuentan
                                            WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                            --  Ingresos normales SÍ cuentan
                                            WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                            -- Devoluciones pendientes NO cuentan
                                            ELSE 0
                                        END
                                    -- SALIDAS: Siempre restan
                                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                                    ELSE 0
                                END
                            ), 0) AS stock_fisico_real
                        FROM movimiento mov
                        WHERE mov.id_producto = $id_producto
                            AND mov.id_almacen = $id_almacen_origen
                            AND mov.id_ubicacion = $id_ubicacion_origen
                            AND mov.est_movimiento != 0";
            
            $res_stock = mysqli_query($con, $sql_stock);
            $row_stock = mysqli_fetch_assoc($res_stock);
            $stock_fisico_real = floatval($row_stock['stock_fisico_real']);
            
            error_log("   📊 Stock físico REAL: $stock_fisico_real");
            
            // Stock disponible = físico + lo que ya está en esta salida
            $stock_disponible = $stock_fisico_real + $cantidad_actual_en_salida;
            
            error_log("   ✅ Stock disponible TOTAL: $stock_disponible");
            
            // VALIDACIÓN
            if ($cantidad_nueva > $stock_disponible) {
                error_log("   ❌ EXCEDE - Stock disponible: $stock_disponible | Intentas: $cantidad_nueva");
                
                $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
                $res_desc = mysqli_query($con, $sql_desc);
                $row_desc = mysqli_fetch_assoc($res_desc);
                $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
                
                $faltante = $cantidad_nueva - $stock_disponible;
                
                $errores[] = "$descripcion: Stock insuficiente. Disponible: " . number_format($stock_disponible, 2) . 
                            ", Solicitado: " . number_format($cantidad_nueva, 2) . 
                            " (Faltante: " . number_format($faltante, 2) . ")";
            }
        }
        
        if (!empty($errores)) {
            mysqli_close($con);
            return "ERROR: " . implode(" | ", $errores);
        }
    }
    
    // ============================================================
    // ✅ ACTUALIZAR ENCABEZADO DE LA SALIDA
    // ============================================================
    
    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);

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

    if (!mysqli_query($con, $sql)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }
    
    // ============================================================
    // ✅ ACTUALIZAR DETALLES
    // ============================================================
    
    $detalles_para_reverificar = array();
    
    foreach ($materiales as $key => $material) {
        
        $es_nuevo = isset($material['es_nuevo']) && $material['es_nuevo'] == '1';
        
        $id_producto = intval($material['id_producto']);
        $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
        $cantidad = floatval($material['cantidad']);
        
        $id_pedido_detalle = null;
        if (isset($material['id_pedido_detalle']) && !empty($material['id_pedido_detalle']) && $material['id_pedido_detalle'] > 0) {
            $id_pedido_detalle = intval($material['id_pedido_detalle']);
        }
        
        $id_pedido_detalle_sql = ($id_pedido_detalle !== null) ? $id_pedido_detalle : "NULL";
        
        if ($es_nuevo) {
            // INSERTAR NUEVO DETALLE
            $sql_detalle = "INSERT INTO salida_detalle (
                        id_salida, id_pedido_detalle, id_producto, prod_salida_detalle, 
                        cant_salida_detalle, est_salida_detalle
                    ) VALUES (
                        $id_salida, $id_pedido_detalle_sql, $id_producto, '$descripcion', 
                        $cantidad, 1
                    )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                error_log("❌ Error al insertar detalle: " . mysqli_error($con));
            } else {
                if ($id_pedido_detalle !== null && !in_array($id_pedido_detalle, $detalles_para_reverificar)) {
                    $detalles_para_reverificar[] = $id_pedido_detalle;
                }
            }
            
        } else {
            // ACTUALIZAR DETALLE EXISTENTE
            
            if (!isset($material['id_salida_detalle']) || empty($material['id_salida_detalle'])) {
                continue;
            }
            
            $id_salida_detalle = intval($material['id_salida_detalle']);
            
            $sql_detalle_info = "SELECT id_producto FROM salida_detalle 
                                WHERE id_salida_detalle = $id_salida_detalle 
                                AND id_salida = $id_salida";
            $res_detalle_info = mysqli_query($con, $sql_detalle_info);
            
            if (!$res_detalle_info || mysqli_num_rows($res_detalle_info) == 0) {
                continue;
            }
            
            $sql_detalle = "UPDATE salida_detalle SET 
                                id_pedido_detalle = $id_pedido_detalle_sql,
                                id_producto = $id_producto,
                                prod_salida_detalle = '$descripcion',
                                cant_salida_detalle = $cantidad
                            WHERE id_salida_detalle = $id_salida_detalle 
                            AND id_salida = $id_salida";
            
            if (mysqli_query($con, $sql_detalle)) {
                if ($id_pedido_detalle !== null && !in_array($id_pedido_detalle, $detalles_para_reverificar)) {
                    $detalles_para_reverificar[] = $id_pedido_detalle;
                }
            }
        }
    }

    // ============================================================
    // 🔥 CAMBIO CRÍTICO: NO REGENERAR MOVIMIENTOS EN ESTADO PENDIENTE
    // ============================================================
    
    // ❌ CÓDIGO ANTERIOR (INCORRECTO):
    // $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 WHERE...";
    // foreach ($materiales as $material) { /* Crear movimientos */ }
    
    // ✅ CÓDIGO CORRECTO: Solo regenerar si está APROBADA (3)
    if ($estado_actual == 3) {
        error_log("🔄 Regenerando movimientos (salida aprobada)");
        
        $sql_personal = "SELECT id_personal FROM salida WHERE id_salida = $id_salida";
        $res_personal = mysqli_query($con, $sql_personal);
        $row_personal = mysqli_fetch_assoc($res_personal);
        $id_personal = $row_personal['id_personal'];
        
        // Desactivar movimientos anteriores
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_salida AND tipo_orden = 2";
        mysqli_query($con, $sql_del_mov);

        // Crear nuevos movimientos
        foreach ($materiales as $material) {
            
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            // Movimiento SALIDA (resta stock)
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
            
            // Movimiento INGRESO (suma stock)
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
    } else {
        error_log("⏸️ Salida en estado PENDIENTE - NO se generan movimientos");
    }

    // ============================================================
    // ✅ RE-VERIFICAR ITEMS DEL PEDIDO
    // ============================================================
    if ($tiene_pedido && !empty($detalles_para_reverificar)) {
        error_log("🔄 Re-verificando " . count($detalles_para_reverificar) . " detalles únicos");
        
        require_once("../_modelo/m_pedidos.php");
        
        foreach ($detalles_para_reverificar as $id_detalle) {
            error_log("   🔍 Re-verificando detalle: $id_detalle");
            ReverificarItemAutomaticamente($id_detalle);
        }
        
        // Actualizar estado del pedido
        error_log("📋 Actualizando estado del pedido: $id_pedido");
        ActualizarEstadoPedido($id_pedido);
    }

    mysqli_close($con);
    error_log("✅ ActualizarSalida completado exitosamente");
    return "SI";
}

//-----------------------------------------------------------------------
function ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion, $id_pedido = null)
{
    include("../_conexion/conexion.php");

    $id_pedido = $id_pedido !== null ? intval($id_pedido) : null;

    $sql = "SELECT COALESCE(
            SUM(CASE
                -- INGRESOS
                WHEN mov.tipo_movimiento = 1 THEN
                    CASE
                        --  Devoluciones confirmadas (tipo_orden=3, est_movimiento=1) SÍ cuentan
                        WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        --  Ingresos normales (tipo_orden != 3) SÍ cuentan
                        WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                        --  Devoluciones pendientes (tipo_orden=3, est_movimiento=2) NO cuentan
                        ELSE 0
                    END
                -- SALIDAS: Siempre restan
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                ELSE 0
            END), 0
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
                        -- INGRESOS
                        WHEN mov.tipo_movimiento = 1 AND mov.est_movimiento != 0 THEN
                            CASE
                                --  Devoluciones confirmadas SÍ cuentan
                                WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                --  Ingresos normales SÍ cuentan
                                WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                --  Devoluciones pendientes NO cuentan
                                ELSE 0
                            END
                        -- SALIDAS: Siempre restan
                        WHEN mov.tipo_movimiento = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
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
                        WHEN mov.tipo_movimiento = 1 AND mov.est_movimiento != 0 THEN
                            CASE
                                WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                ELSE 0
                            END
                        WHEN mov.tipo_movimiento = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
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
function TieneSalidasPendientesPorPedido($id_pedido) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT pd.id_pedido_detalle,
                   pd.cant_os_pedido_detalle,
                   COALESCE(SUM(sd.cant_salida_detalle), 0) as total_trasladado
            FROM pedido_detalle pd
            LEFT JOIN salida_detalle sd ON pd.id_pedido_detalle = sd.id_pedido_detalle
            LEFT JOIN salida s ON sd.id_salida = s.id_salida 
                AND s.est_salida IN (1, 2) -- Solo salidas ACTIVAS (1) o APROBADAS (2)
            WHERE pd.id_pedido = ?
              AND pd.est_pedido_detalle = 1 -- Solo items ABIERTOS
              AND pd.cant_os_pedido_detalle > 0 -- Solo items verificados para OS
            GROUP BY pd.id_pedido_detalle
            HAVING (pd.cant_os_pedido_detalle - COALESCE(SUM(sd.cant_salida_detalle), 0)) > 0.01";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tiene_pendientes = ($result->num_rows > 0);
    
    $stmt->close();
    mysqli_close($con);
    
    if ($tiene_pendientes) {
        error_log("✅ Pedido $id_pedido tiene items con salidas pendientes");
    } else {
        error_log("ℹ️ Pedido $id_pedido NO tiene items pendientes para salida");
    }
    
    return $tiene_pendientes;
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
        
        // 1️⃣ OBTENER DATOS DE LA SALIDA
        $sql_sel = "SELECT id_salida, id_pedido, est_salida 
                    FROM salida 
                    WHERE id_salida = $id_salida 
                    LIMIT 1";
        
        $res_sel = mysqli_query($con, $sql_sel);
        
        if (!$res_sel) {
            throw new Exception("Error al consultar la salida: " . mysqli_error($con));
        }
        
        $row = mysqli_fetch_assoc($res_sel);
        
        if (!$row) {
            throw new Exception("Salida no encontrada.");
        }
        
        $estado_actual = intval($row['est_salida']);
        $id_pedido = isset($row['id_pedido']) && $row['id_pedido'] > 0 
                    ? intval($row['id_pedido']) 
                    : null;
        
        error_log("📦 Anulando salida ID: $id_salida | Estado: $estado_actual | Pedido: " . ($id_pedido ?? 'SIN PEDIDO'));

        // ✅ VALIDAR ESTADOS PERMITIDOS
        if ($estado_actual == 0) {
            throw new Exception("La salida ya está anulada.");
        }
        
        if ($estado_actual == 2) {
            throw new Exception("No se puede anular una salida recepcionada.");
        }

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

        // 4️⃣ DESACTIVAR MOVIMIENTOS (SOLO si estaba APROBADA)
        if ($estado_actual == 3) {
            $sql_mov_off = "UPDATE movimiento 
                           SET est_movimiento = 0 
                           WHERE tipo_orden = 2 
                           AND id_orden = $id_salida";
            
            if (!mysqli_query($con, $sql_mov_off)) {
                throw new Exception("Error al desactivar movimientos: " . mysqli_error($con));
            }
            
            error_log("✅ Movimientos desactivados");
        } else {
            error_log("ℹ️ No había movimientos (estaba en estado PENDIENTE)");
        }

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
        
        // 7️⃣ ACTUALIZAR ESTADO DEL PEDIDO
        if ($id_pedido > 0) {
            error_log("📋 Actualizando estado del pedido: $id_pedido");
            
            require_once("../_modelo/m_pedidos.php");
            
            foreach ($items_afectados as $id_detalle) {
                ReverificarItemAutomaticamente($id_detalle);
            }
            
            ActualizarEstadoPedido($id_pedido);
            error_log("✅ Estado del pedido actualizado");
        }
        
        return [
            'success' => true,
            'message' => '✅ Salida anulada correctamente'
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
// OBTENER DATOS DE UNA SALIDA POR ID (para edición) - CORREGIDO
// ============================================================================
function ObtenerSalidaPorId($id_salida) {
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT s.*, 
                   ao.nom_almacen as almacen_origen_nombre,
                   uo.nom_ubicacion as ubicacion_origen_nombre,
                   ad.nom_almacen as almacen_destino_nombre,
                   ud.nom_ubicacion as ubicacion_destino_nombre,
                   per.nom_personal as personal_nombre,
                   pe.nom_personal as personal_encargado_nombre,
                   pr.nom_personal as personal_recibe_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN {$bd_complemento}.personal per ON s.id_personal = per.id_personal
            LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
            LEFT JOIN {$bd_complemento}.personal pr ON s.id_personal_recibe = pr.id_personal
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
               p.cod_material,           
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
        
        //  CORRECCIÓN CRÍTICA: CÁLCULO DE STOCK DISPONIBLE
        
        // PASO 1: Obtener stock físico REAL (sin compromisos)
        $sql_stock_fisico = "SELECT COALESCE(
                                    SUM(
                                        CASE
                                            -- INGRESOS
                                            WHEN mov.tipo_movimiento = 1 THEN
                                                CASE
                                                    --  Devoluciones confirmadas SÍ cuentan
                                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                                    --  Ingresos normales SÍ cuentan
                                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                                    --  Devoluciones pendientes NO cuentan
                                                    ELSE 0
                                                END
                                            -- SALIDAS: Siempre restan
                                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                                            ELSE 0
                                        END
                                    ), 0) AS stock_fisico
                                FROM movimiento
                                WHERE id_producto = $id_producto
                                AND id_almacen = $id_almacen_origen
                                AND id_ubicacion = $id_ubicacion_origen
                                AND est_movimiento != 0";
        
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
        
        //  PASO 2: Calcular stock físico REAL (SIN compromisos)
        $sql_stock = "SELECT COALESCE(
                        SUM(
                            CASE
                                -- INGRESOS
                                WHEN mov.tipo_movimiento = 1 THEN
                                    CASE
                                        --  Devoluciones confirmadas SÍ cuentan
                                        WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                        --  Ingresos normales SÍ cuentan
                                        WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                        --  Devoluciones pendientes NO cuentan
                                        ELSE 0
                                    END
                                -- SALIDAS: Siempre restan
                                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                                ELSE 0
                            END
                        ), 0) AS stock_fisico_real
                    FROM movimiento mov
                    WHERE mov.id_producto = $id_producto
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0";
        
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

    $sql_check = "SELECT est_salida, id_personal_aprueba_salida, id_pedido 
                  FROM salida 
                  WHERE id_salida = '$id_salida'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row_check || $row_check['est_salida'] == 0) {
        mysqli_close($con);
        return false;
    }

    if (!empty($row_check['id_personal_aprueba_salida'])) {
        mysqli_close($con);
        return false;
    }

    $id_pedido = intval($row_check['id_pedido']);

    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = '$id_personal'";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        mysqli_close($con);
        error_log("Error: id_personal $id_personal no existe");
        return false;
    }

    //  Actualizar a estado RECEPCIONADA (2) - YA NO HAY ESTADO 3
    $sql_update = "UPDATE salida 
                   SET id_personal_aprueba_salida = '$id_personal',
                       fec_aprueba_salida = NOW(),
                       est_salida = 2
                   WHERE id_salida = '$id_salida'";

    $res_update = mysqli_query($con, $sql_update);

    if ($res_update && $id_pedido > 0) {
        //  Usar función unificada
        require_once("../_modelo/m_pedidos.php");
        ActualizarEstadoPedidoUnificado($id_pedido, $con);
    }

    mysqli_close($con);
    return $res_update;
}

/**
 * ============================================
 * PROCESAR FLUJO DESPUÉS DE DENEGAR SALIDA
 * ============================================
 * Esta función se ejecuta DESPUÉS de denegar una salida
 * y maneja la lógica completa:
 * 1. Verifica si hay más ubicaciones disponibles
 * 2. Si NO hay → convierte OS a OC
 * 3. Actualiza estados del pedido
 * 
 * @param int $id_salida - ID de la salida denegada
 * @return array - Resultado del procesamiento
 */
function ProcesarFlujoDespuesDeDenegar($id_salida)
{
    include("../_conexion/conexion.php");
    
    try {
        $id_salida = intval($id_salida);
        
        error_log(" ProcesarFlujoDespuesDeDenegar - Salida ID: $id_salida");
        
        //  Obtener información de la salida denegada
        $sql_salida = "
            SELECT 
                s.id_pedido,
                s.id_almacen_destino,
                s.id_ubicacion_destino
            FROM salida s
            WHERE s.id_salida = $id_salida
              AND s.est_salida = 4
        ";
        
        $res_salida = mysqli_query($con, $sql_salida);
        
        if (!$res_salida || mysqli_num_rows($res_salida) == 0) {
            throw new Exception("Salida no encontrada o no está denegada");
        }
        
        $salida = mysqli_fetch_assoc($res_salida);
        $id_pedido = intval($salida['id_pedido']);
        $id_almacen_destino = intval($salida['id_almacen_destino']);
        $id_ubicacion_destino = intval($salida['id_ubicacion_destino']);
        
        error_log(" Pedido: $id_pedido | Destino: Almacén $id_almacen_destino, Ubicación $id_ubicacion_destino");
        
        //  Obtener detalles de la salida denegada
        $sql_detalles = "
            SELECT 
                sd.id_salida_detalle,
                sd.id_pedido_detalle,
                sd.id_producto,
                sd.cant_salida_detalle,
                p.nom_producto
            FROM salida_detalle sd
            INNER JOIN producto p ON sd.id_producto = p.id_producto
            WHERE sd.id_salida = $id_salida
              AND sd.est_salida_detalle = 1
        ";
        
        $res_detalles = mysqli_query($con, $sql_detalles);
        
        if (!$res_detalles) {
            throw new Exception("Error al obtener detalles: " . mysqli_error($con));
        }
        
        $items_procesados = [];
        $items_convertidos = [];
        
        //  Procesar cada detalle
        while ($detalle = mysqli_fetch_assoc($res_detalles)) {
            $id_producto = intval($detalle['id_producto']);
            $id_pedido_detalle = intval($detalle['id_pedido_detalle']);
            $cantidad = floatval($detalle['cant_salida_detalle']);
            $nombre_producto = $detalle['nom_producto'];
            
            error_log("    Procesando: $nombre_producto (Cantidad: $cantidad)");
            
            // Verificar si hay más ubicaciones disponibles
            require_once("../_modelo/m_pedidos.php");
            
            $hay_mas_ubicaciones = HayMasUbicacionesDisponibles(
                $id_producto,
                $id_almacen_destino,
                $id_ubicacion_destino,
                $id_pedido_detalle
            );
            
            if ($hay_mas_ubicaciones) {
                //  HAY MÁS UBICACIONES - Botón OS sigue habilitado
                error_log("    Hay más ubicaciones disponibles - OS sigue habilitado");
                
                $items_procesados[] = [
                    'producto' => $nombre_producto,
                    'accion' => 'os_disponible',
                    'mensaje' => "Hay otras ubicaciones con stock disponible"
                ];
                
            } else {
                //  NO HAY MÁS UBICACIONES - Convertir OS a OC
                error_log("    NO hay más ubicaciones - Convirtiendo OS a OC");
                
                $resultado_conversion = ConvertirCantidadOSaOC($id_pedido_detalle, $cantidad);
                
                if ($resultado_conversion['success']) {
                    $items_convertidos[] = [
                        'producto' => $nombre_producto,
                        'cantidad' => $cantidad,
                        'cant_os_nueva' => $resultado_conversion['cant_os_nueva'],
                        'cant_oc_nueva' => $resultado_conversion['cant_oc_nueva']
                    ];
                    
                    error_log("    Convertido: $cantidad unidades de OS a OC");
                } else {
                    error_log("    Error al convertir: " . $resultado_conversion['message']);
                }
                
                $items_procesados[] = [
                    'producto' => $nombre_producto,
                    'accion' => 'convertido_a_oc',
                    'mensaje' => "Cantidad convertida a OC: $cantidad",
                    'resultado' => $resultado_conversion
                ];
            }
        }
        
        //  Actualizar estados del pedido
        if ($id_pedido > 0) {
            require_once("../_modelo/m_pedidos.php");
            
            // Re-verificar items afectados
            foreach ($items_procesados as $item_proc) {
                $sql_det = "SELECT id_pedido_detalle 
                           FROM salida_detalle 
                           WHERE id_salida = $id_salida 
                           LIMIT 1";
                $res_det = mysqli_query($con, $sql_det);
                if ($row_det = mysqli_fetch_assoc($res_det)) {
                    ReverificarItemAutomaticamente(intval($row_det['id_pedido_detalle']));
                }
            }
            
            // Actualizar estado del pedido
            ActualizarEstadoPedidoUnificado($id_pedido);
            
            error_log(" Estados del pedido actualizados");
        }
        
        mysqli_close($con);
        
        //  Preparar respuesta
        $mensaje_resumen = " Salida denegada correctamente.\n\n";
        
        if (count($items_convertidos) > 0) {
            $mensaje_resumen .= " Cantidades convertidas de OS a OC:\n";
            foreach ($items_convertidos as $item_conv) {
                $mensaje_resumen .= "• {$item_conv['producto']}: {$item_conv['cantidad']} unidades\n";
            }
        }
        
        if (count($items_procesados) - count($items_convertidos) > 0) {
            $mensaje_resumen .= "\n Items con ubicaciones alternativas disponibles:\n";
            foreach ($items_procesados as $item_proc) {
                if ($item_proc['accion'] == 'os_disponible') {
                    $mensaje_resumen .= "• {$item_proc['producto']}\n";
                }
            }
        }
        
        return [
            'success' => true,
            'message' => $mensaje_resumen,
            'items_procesados' => $items_procesados,
            'items_convertidos' => $items_convertidos,
            'total_convertidos' => count($items_convertidos)
        ];
        
    } catch (Exception $e) {
        if (isset($con)) {
            mysqli_close($con);
        }
        
        error_log(" Error en ProcesarFlujoDespuesDeDenegar: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => "Error al procesar: " . $e->getMessage()
        ];
    }
}

/**
 * ============================================
 * DENEGAR SALIDA CON FLUJO COMPLETO
 * ============================================
 * Versión mejorada que incluye el procesamiento automático
 * después de la denegación
 * 
 * @param int $id_salida - ID de la salida a denegar
 * @param int $id_personal_deniega - ID del personal que deniega
 * @param string $motivo - Motivo de la denegación (opcional)
 * @return array - Resultado completo
 */
function DenegarSalidaConFlujoCompleto($id_salida, $id_personal_deniega, $motivo = '')
{
    //  Denegar la salida (función existente)
    $resultado_denegacion = DenegarSalida($id_salida, $id_personal_deniega);
    
    if (!$resultado_denegacion['success']) {
        return $resultado_denegacion;
    }
    
    error_log("✅ Salida denegada - Iniciando flujo de procesamiento");
    
    //  Procesar flujo post-denegación
    $resultado_flujo = ProcesarFlujoDespuesDeDenegar($id_salida);
    
    if (!$resultado_flujo['success']) {
        // Aunque falle el flujo, la denegación ya está hecha
        return [
            'success' => true,
            'message' => $resultado_denegacion['message'] . "\n⚠️ Advertencia: " . $resultado_flujo['message'],
            'denegacion_exitosa' => true,
            'flujo_completo' => false
        ];
    }
    
    //  Combinar resultados
    return [
        'success' => true,
        'message' => $resultado_flujo['message'],
        'denegacion_exitosa' => true,
        'flujo_completo' => true,
        'items_procesados' => $resultado_flujo['items_procesados'] ?? [],
        'items_convertidos' => $resultado_flujo['items_convertidos'] ?? [],
        'total_convertidos' => $resultado_flujo['total_convertidos'] ?? 0
    ];
}

/**
 * ============================================
 * OBTENER HISTORIAL DE DENEGACIONES PARA UN PEDIDO DETALLE
 * ============================================
 * Retorna el historial de salidas denegadas para un detalle específico
 * 
 * @param int $id_pedido_detalle - ID del detalle del pedido
 * @return array - Array de denegaciones
 */
function ObtenerHistorialDenegacionesPorDetalle($id_pedido_detalle)
{
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    $sql = "
        SELECT 
            s.id_salida,
            s.id_almacen_origen,
            s.id_ubicacion_origen,
            s.fec_deniega_salida,
            a.nom_almacen,
            u.nom_ubicacion,
            CONCAT(p.nom_personal, ' ', p.ape_personal) as personal_que_denego,
            sd.cant_salida_detalle as cantidad_denegada
        FROM salida s
        INNER JOIN salida_detalle sd ON s.id_salida = sd.id_salida
        INNER JOIN almacen a ON s.id_almacen_origen = a.id_almacen
        INNER JOIN ubicacion u ON s.id_ubicacion_origen = u.id_ubicacion
        LEFT JOIN {$bd_complemento}.personal p ON s.id_personal_deniega_salida = p.id_personal
        WHERE sd.id_pedido_detalle = $id_pedido_detalle
          AND s.est_salida = 4
        ORDER BY s.fec_deniega_salida DESC
    ";
    
    $resultado = mysqli_query($con, $sql);
    $historial = [];
    
    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $historial[] = $row;
        }
    }
    
    mysqli_close($con);
    
    return $historial;
}
//=======================================================================
// ENVÍO DE CORREOS PARA SALIDAS
//=======================================================================

/**
 * Enviar correo cuando se crea una nueva salida 
 */
function EnviarCorreoSalidaCreada($id_salida)
{
    include("../_conexion/conexion.php");

    
    $id_salida = intval($id_salida);
    
    $sql = "SELECT 
              s.id_salida,
              s.ndoc_salida,
              s.fec_req_salida,
              s.obs_salida,
              ao.nom_almacen AS almacen_origen,
              uo.nom_ubicacion AS ubicacion_origen,
              ad.nom_almacen AS almacen_destino,
              ud.nom_ubicacion AS ubicacion_destino,
              mt.nom_material_tipo,
              pe.nom_personal AS encargado_nombre,
              pe.email_personal AS encargado_email,
              pr.nom_personal AS receptor_nombre,
              pr.email_personal AS receptor_email,  
              pc.nom_personal AS creador_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
            LEFT JOIN $bd_complemento.personal pe ON s.id_personal_encargado = pe.id_personal
            LEFT JOIN $bd_complemento.personal pr ON s.id_personal_recibe = pr.id_personal AND s.id_personal_recibe > 0
            LEFT JOIN $bd_complemento.personal pc ON s.id_personal = pc.id_personal
            WHERE s.id_salida = $id_salida
            LIMIT 1";
    
    $res = mysqli_query($con, $sql);
    
    if (!$res) {
        error_log("❌ Error en consulta SQL: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    if (mysqli_num_rows($res) == 0) {
        error_log("❌ No se encontró la salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    $salida = mysqli_fetch_assoc($res);
    
    //  VALIDAR que al menos UNO tenga email
    if (empty($salida['encargado_email']) && empty($salida['receptor_email'])) {
        error_log("⚠️ No hay emails para enviar (ni encargado ni receptor) - Salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    // Obtener detalle de materiales
    $sql_detalle = "SELECT 
                      sd.prod_salida_detalle,
                      sd.cant_salida_detalle,
                      p.nom_producto,
                      um.nom_unidad_medida
                    FROM salida_detalle sd
                    LEFT JOIN producto p ON sd.id_producto = p.id_producto
                    LEFT JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
                    WHERE sd.id_salida = $id_salida
                    AND sd.est_salida_detalle = 1";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    
    if (!$res_detalle || mysqli_num_rows($res_detalle) == 0) {
        error_log("⚠️ No hay detalles para la salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    // Construcción del HTML del correo
    $materiales_html = '';
    while ($detalle = mysqli_fetch_assoc($res_detalle)) {
        $producto = !empty($detalle['prod_salida_detalle']) ? $detalle['prod_salida_detalle'] : $detalle['nom_producto'];
        $unidad = !empty($detalle['nom_unidad_medida']) ? $detalle['nom_unidad_medida'] : 'UND';
        
        $materiales_html .= "
        <tr>
            <td style='padding: 12px; border-bottom: 1px solid #e0e0e0;'>{$producto}</td>
            <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$detalle['cant_salida_detalle']}</td>
            <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$unidad}</td>
        </tr>";
    }
    
    
    $subject = "Nueva Solicitud de Salida #{$salida['ndoc_salida']} - Requiere Aprobación";
    
    $obs_html = '';
    if (!empty($salida['obs_salida'])) {
        $obs_html = "
        <div style='margin: 20px 0; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;'>
            <p style='margin: 0; color: #856404;'><strong>Observaciones:</strong></p>
            <p style='margin: 5px 0 0 0; color: #856404;'>{$salida['obs_salida']}</p>
        </div>";
    }
    
    $receptor_html = '';
    if (!empty($salida['receptor_nombre'])) {
        $receptor_html = "<p style='margin: 5px 0;'><strong>Receptor:</strong> {$salida['receptor_nombre']}</p>";
    }
    
    $encargado_html = '';
    if (!empty($salida['encargado_nombre'])) {
        $encargado_html = "<p style='margin: 5px 0;'><strong>Encargado:</strong> {$salida['encargado_nombre']}</p>";
    }
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <!-- Header -->
                        <tr>
                            <td style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Nueva Solicitud de Salida</h1>
                                <p style='color: #ffffff; margin: 10px 0 0 0; font-size: 16px;'>Documento #{$salida['ndoc_salida']}</p>
                            </td>
                        </tr>
                        
                        <!-- Alerta de acción requerida -->
                        <tr>
                            <td style='padding: 20px; background-color: #fff3cd; border-left: 4px solid #ffc107;'>
                                <p style='margin: 0; color: #856404; font-weight: bold;'>⚠️ Acción requerida: Esta salida requiere aprobación</p>
                            </td>
                        </tr>
                        
                        <!-- Contenido -->
                        <tr>
                            <td style='padding: 30px;'>
                                <h2 style='color: #333333; margin-top: 0;'>Detalles de la Solicitud</h2>
                                
                                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;'>
                                    <p style='margin: 5px 0;'><strong>Documento:</strong> {$salida['ndoc_salida']}</p>
                                    <p style='margin: 5px 0;'><strong>Fecha requerida:</strong> {$salida['fec_req_salida']}</p>
                                    <p style='margin: 5px 0;'><strong>Tipo de material:</strong> {$salida['nom_material_tipo']}</p>
                                </div>
                                
                                <h3 style='color: #333333; margin-top: 25px;'>Origen y Destino</h3>
                                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 15px 0;'>
                                    <p style='margin: 5px 0;'><strong>Origen:</strong> {$salida['almacen_origen']} - {$salida['ubicacion_origen']}</p>
                                    <p style='margin: 5px 0;'><strong>Destino:</strong> {$salida['almacen_destino']} - {$salida['ubicacion_destino']}</p>
                                </div>
                                
                                <h3 style='color: #333333; margin-top: 25px;'>Personal</h3>
                                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 15px 0;'>
                                    {$receptor_html}
                                    {$encargado_html}
                                </div>
                                
                                {$obs_html}
                                
                                <h3 style='color: #333333; margin-top: 25px;'>Materiales Solicitados</h3>
                                <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; margin: 15px 0; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;'>
                                    <thead>
                                        <tr style='background-color: #667eea;'>
                                            <th style='padding: 12px; text-align: left; color: #ffffff; font-weight: bold;'>Producto</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Cantidad</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$materiales_html}
                                    </tbody>
                                </table>
                                
                                <div style='text-align: center; margin-top: 30px;'>
                                    <a href='https://arceperusac.com/_controlador/salidas_mostrar.php' style='display: inline-block; padding: 12px 30px; background-color: #667eea; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Ver Listado de Salidas</a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                <p style='margin: 0; color: #666666; font-size: 12px;'>Este es un correo automático del Sistema de Almacén</p>
                                <p style='margin: 5px 0 0 0; color: #666666; font-size: 12px;'>Montajes e Ingeniería ARCE PERÚ SAC</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ARCE PERÚ <notificaciones@arceperusac.com>" . "\r\n";
    $headers .= "Bcc: notificaciones@arceperusac.com" . "\r\n";
    
    //  ENVIAR AL ENCARGADO
    $enviado_encargado = false;
    if (!empty($salida['encargado_email'])) {
        $mail_encargado = mail($salida['encargado_email'], $subject, $message, $headers);
        
        if ($mail_encargado) {
            error_log("✅ MAIL OK → Salida creada enviado a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_encargado = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    //  ENVIAR AL RECEPTOR
    $enviado_receptor = false;
    if (!empty($salida['receptor_email'])) {
        $mail_receptor = mail($salida['receptor_email'], $subject, $message, $headers);
        
        if ($mail_receptor) {
            error_log("✅ MAIL OK → Salida creada enviado a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_receptor = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    mysqli_close($con);
    
    //  Retornar true si al menos uno se envió exitosamente
    return ($enviado_encargado || $enviado_receptor);
}

/**
 * Enviar correo cuando se aprueba una salida (al receptor)
 */
function EnviarCorreoSalidaAprobada($id_salida)
{
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);

    $sql = "SELECT 
              s.id_salida,
              s.ndoc_salida,
              s.fec_req_salida,
              s.fec_aprueba_salida,
              s.obs_salida,
              ao.nom_almacen AS almacen_origen,
              uo.nom_ubicacion AS ubicacion_origen,
              ad.nom_almacen AS almacen_destino,
              ud.nom_ubicacion AS ubicacion_destino,
              mt.nom_material_tipo,
              pe.nom_personal AS encargado_nombre,
              pe.email_personal AS encargado_email,  
              pr.nom_personal AS receptor_nombre,
              pr.email_personal AS receptor_email,
              pa.nom_personal AS aprobador_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
            LEFT JOIN $bd_complemento.personal pe ON s.id_personal_encargado = pe.id_personal
            LEFT JOIN $bd_complemento.personal pr ON s.id_personal_recibe = pr.id_personal AND s.id_personal_recibe > 0
            LEFT JOIN $bd_complemento.personal pa ON s.id_personal_aprueba_salida = pa.id_personal
            WHERE s.id_salida = $id_salida
            AND s.est_salida = 3
            LIMIT 1";
    
    $res = mysqli_query($con, $sql);
    
    if (!$res || mysqli_num_rows($res) == 0) {
        error_log("❌ No se encontró la salida aprobada ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    $salida = mysqli_fetch_assoc($res);
    
    //  Validar que al menos UNO tenga email
    if (empty($salida['encargado_email']) && empty($salida['receptor_email'])) {
        error_log("⚠️ No hay emails para enviar - Salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    

    $sql_detalle = "SELECT 
                      sd.cant_salida_detalle,
                      p.nom_producto,
                      um.nom_unidad_medida
                    FROM salida_detalle sd
                    INNER JOIN producto p ON sd.id_producto = p.id_producto
                    INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
                    WHERE sd.id_salida = $id_salida
                    AND sd.est_salida_detalle = 1
                    ORDER BY p.nom_producto";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    $items = [];
    while ($row = mysqli_fetch_assoc($res_detalle)) {
        $items[] = $row;
    }
    
    mysqli_close($con);
    
    // VALIDAR DESTINATARIO
    $destinatario = trim($salida['receptor_email']);
    
    if (empty($destinatario)) {
        error_log(" No hay destinatarios para la salida ID: $id_salida");
        return false;
    }
    
    // CONSTRUIR CORREO
    $asunto = "Salida Aprobada #{$salida['ndoc_salida']} - Pendiente de Recepción";
    
    // Construir tabla de materiales
    $tabla_items = '';
    foreach ($items as $item) {
        $tabla_items .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0;'>{$item['nom_producto']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>" . 
                    number_format($item['cant_salida_detalle'], 2) . "</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['nom_unidad_medida']}</td>
            </tr>";
    }
    
    $fecha_aprobacion = !empty($salida['fec_aprueba_salida']) 
        ? date('d/m/Y H:i', strtotime($salida['fec_aprueba_salida'])) 
        : 'No disponible';
    
    $aprobador = !empty($salida['aprobador_nombre']) ? $salida['aprobador_nombre'] : 'No disponible';
    
    $observaciones_html = !empty($salida['obs_salida']) 
        ? "<div style='background-color: #fff8e1; padding: 15px; border-left: 4px solid #ffc107; border-radius: 6px; margin: 20px 0;'>
            <strong>📝 Observaciones:</strong>
            <p style='margin: 5px 0 0 0;'>{$salida['obs_salida']}</p>
          </div>"
        : "";
    
    $mensaje = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); padding: 30px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>✅ Salida Aprobada</h1>
                                <p style='color: #f0f0f0; margin: 10px 0 0 0; font-size: 14px;'>Documento #{$salida['ndoc_salida']}</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 20px; background-color: #d4edda; border-left: 4px solid #28a745;'>
                                <p style='margin: 0; color: #155724; font-weight: bold;'>✅ SALIDA APROBADA</p>
                                <p style='margin: 5px 0 0 0; color: #155724;'>Esta salida ha sido aprobada y está lista para su recepción.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 30px;'>
                                <h2 style='color: #333; margin-top: 0; font-size: 18px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;'>Información de Aprobación</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Fecha de Aprobación:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$fecha_aprobacion}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Aprobado por:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$aprobador}</td>
                                    </tr>
                                </table>
                                
                                <h2 style='color: #333; margin-top: 25px; font-size: 18px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;'>Detalles de la Salida</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0;'>
                                    <tr>
                                        <td style='color: #666; width: 40%;'><strong>Número de Documento:</strong></td>
                                        <td style='color: #333;'>{$salida['ndoc_salida']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Fecha Requerida:</strong></td>
                                        <td style='color: #333;'>" . date('d/m/Y', strtotime($salida['fec_req_salida'])) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Tipo de Material:</strong></td>
                                        <td style='color: #333;'>{$salida['nom_material_tipo']}</td>
                                    </tr>
                                </table>
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Origen y Destino</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 15px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Almacén Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Almacén Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_destino']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_destino']}</td>
                                    </tr>
                                </table>
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Personal</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 15px 0;'>
                                    <tr>
                                        <td style='color: #666; width: 40%;'><strong>Encargado:</strong></td>
                                        <td style='color: #333;'>{$salida['encargado_nombre']}</td>
                                    </tr>
                                </table>
                                
                                {$observaciones_html}
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Materiales a Recibir</h3>
                                <table width='100%' cellpadding='0' cellspacing='0' style='margin: 15px 0; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;'>
                                    <thead>
                                        <tr style='background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);'>
                                            <th style='padding: 12px; text-align: left; color: #ffffff; font-weight: bold;'>Producto</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Cantidad</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$tabla_items}
                                    </tbody>
                                </table>
                                
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='https://arceperusac.com/_controlador/salidas_mostrar.php' 
                                       style='background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); 
                                              color: #ffffff; 
                                              padding: 14px 30px; 
                                              text-decoration: none; 
                                              border-radius: 25px; 
                                              font-weight: bold; 
                                              display: inline-block;
                                              box-shadow: 0 4px 6px rgba(76, 175, 80, 0.3);'>
                                        📦 Ver y Recepcionar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                <p style='margin: 0; color: #666; font-size: 12px;'>
                                    Este es un correo automático del Sistema de Gestión de Almacén
                                </p>
                                <p style='margin: 5px 0 0 0; color: #999; font-size: 11px;'>
                                    Montajes e Ingeniería Arce Perú SAC © " . date('Y') . "
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    // ENVIAR CORREO
    $cabeceras = "MIME-Version: 1.0\r\n";
    $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
    $cabeceras .= "From: ARCE PERÚ <notificaciones@arceperusac.com>\r\n";
    $cabeceras .= "Bcc: notificaciones@arceperusac.com\r\n";
    
    //  ENVIAR AL ENCARGADO
    $enviado_encargado = false;
    if (!empty($salida['encargado_email'])) {
        $mail_encargado = @mail($salida['encargado_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_encargado) {
            error_log("✅ MAIL OK → Salida aprobada enviado a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_encargado = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    //  ENVIAR AL RECEPTOR
    $enviado_receptor = false;
    if (!empty($salida['receptor_email'])) {
        $mail_receptor = @mail($salida['receptor_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_receptor) {
            error_log("✅ MAIL OK → Salida aprobada enviado a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_receptor = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    return ($enviado_encargado || $enviado_receptor);
}

/**
 * Enviar correo cuando se deniega una salida (al encargado Y receptor)
 */
function EnviarCorreoSalidaDenegada($id_salida)
{
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);

    $sql = "SELECT 
              s.id_salida,
              s.ndoc_salida,
              s.fec_req_salida,
              s.fec_deniega_salida,
              s.obs_salida,
              ao.nom_almacen AS almacen_origen,
              uo.nom_ubicacion AS ubicacion_origen,
              ad.nom_almacen AS almacen_destino,
              ud.nom_ubicacion AS ubicacion_destino,
              mt.nom_material_tipo,
              pe.nom_personal AS encargado_nombre,
              pe.email_personal AS encargado_email,
              pr.nom_personal AS receptor_nombre,
              pr.email_personal AS receptor_email,
              pd.nom_personal AS denegador_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
            LEFT JOIN $bd_complemento.personal pe ON s.id_personal_encargado = pe.id_personal
            LEFT JOIN $bd_complemento.personal pr ON s.id_personal_recibe = pr.id_personal
            LEFT JOIN $bd_complemento.personal pd ON s.id_personal_deniega_salida = pd.id_personal
            WHERE s.id_salida = $id_salida
            AND s.est_salida = 4
            LIMIT 1";
    
    $res = mysqli_query($con, $sql);
    
    if (!$res || mysqli_num_rows($res) == 0) {
        error_log("❌ No se encontró la salida denegada ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    $salida = mysqli_fetch_assoc($res);
    
    // Validar que al menos UNO tenga email
    if (empty($salida['encargado_email']) && empty($salida['receptor_email'])) {
        error_log("⚠️ No hay emails para enviar - Salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }

    $sql_detalle = "SELECT 
                      sd.cant_salida_detalle,
                      p.nom_producto,
                      um.nom_unidad_medida
                    FROM salida_detalle sd
                    INNER JOIN producto p ON sd.id_producto = p.id_producto
                    INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
                    WHERE sd.id_salida = $id_salida
                    AND sd.est_salida_detalle = 1
                    ORDER BY p.nom_producto";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    $items = [];
    while ($row = mysqli_fetch_assoc($res_detalle)) {
        $items[] = $row;
    }
    
    mysqli_close($con);
    
    $asunto = "Salida DENEGADA #{$salida['ndoc_salida']}";
    
    // Construir tabla de materiales
    $tabla_items = '';
    foreach ($items as $item) {
        $tabla_items .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0;'>{$item['nom_producto']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>" . 
                    number_format($item['cant_salida_detalle'], 2) . "</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['nom_unidad_medida']}</td>
            </tr>";
    }
    
    $fecha_denegacion = !empty($salida['fec_deniega_salida']) 
        ? date('d/m/Y H:i', strtotime($salida['fec_deniega_salida'])) 
        : 'No disponible';
    
    $denegador = !empty($salida['denegador_nombre']) ? $salida['denegador_nombre'] : 'No disponible';
    
    $observaciones_html = !empty($salida['obs_salida']) 
        ? "<div style='background-color: #fff8e1; padding: 15px; border-left: 4px solid #ffc107; border-radius: 6px; margin: 20px 0;'>
            <strong>📝 Observaciones:</strong>
            <p style='margin: 5px 0 0 0;'>{$salida['obs_salida']}</p>
          </div>"
        : "";
    
    $mensaje = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 30px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>❌ Salida Denegada</h1>
                                <p style='color: #f0f0f0; margin: 10px 0 0 0; font-size: 14px;'>Documento #{$salida['ndoc_salida']}</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 20px; background-color: #f8d7da; border-left: 4px solid #dc3545;'>
                                <p style='margin: 0; color: #721c24; font-weight: bold;'>❌ SALIDA DENEGADA</p>
                                <p style='margin: 5px 0 0 0; color: #721c24;'>Esta salida ha sido denegada y no procederá.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 30px;'>
                                <h2 style='color: #333; margin-top: 0; font-size: 18px; border-bottom: 2px solid #dc3545; padding-bottom: 10px;'>Información de Denegación</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Fecha de Denegación:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$fecha_denegacion}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Denegado por:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$denegador}</td>
                                    </tr>
                                </table>
                                
                                <h2 style='color: #333; margin-top: 25px; font-size: 18px; border-bottom: 2px solid #dc3545; padding-bottom: 10px;'>Detalles de la Salida</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0;'>
                                    <tr>
                                        <td style='color: #666; width: 40%;'><strong>Número de Documento:</strong></td>
                                        <td style='color: #333;'>{$salida['ndoc_salida']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Fecha Requerida:</strong></td>
                                        <td style='color: #333;'>" . date('d/m/Y', strtotime($salida['fec_req_salida'])) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Tipo de Material:</strong></td>
                                        <td style='color: #333;'>{$salida['nom_material_tipo']}</td>
                                    </tr>
                                </table>
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Origen y Destino</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 15px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Almacén Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Almacén Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_destino']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_destino']}</td>
                                    </tr>
                                </table>
                                
                                {$observaciones_html}
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Materiales Solicitados</h3>
                                <table width='100%' cellpadding='0' cellspacing='0' style='margin: 15px 0; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;'>
                                    <thead>
                                        <tr style='background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);'>
                                            <th style='padding: 12px; text-align: left; color: #ffffff; font-weight: bold;'>Producto</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Cantidad</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$tabla_items}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                <p style='margin: 0; color: #666; font-size: 12px;'>
                                    Este es un correo automático del Sistema de Gestión de Almacén
                                </p>
                                <p style='margin: 5px 0 0 0; color: #999; font-size: 11px;'>
                                    Montajes e Ingeniería Arce Perú SAC © " . date('Y') . "
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    $cabeceras = "MIME-Version: 1.0\r\n";
    $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
    $cabeceras .= "From: ARCE PERÚ <notificaciones@arceperusac.com>\r\n";
    $cabeceras .= "Bcc: notificaciones@arceperusac.com\r\n";

    //  ENVIAR AL ENCARGADO
    $enviado_encargado = false;
    if (!empty($salida['encargado_email'])) {
        $mail_encargado = @mail($salida['encargado_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_encargado) {
            error_log("✅ MAIL OK → Salida denegada enviado a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_encargado = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    //  ENVIAR AL RECEPTOR
    $enviado_receptor = false;
    if (!empty($salida['receptor_email'])) {
        $mail_receptor = @mail($salida['receptor_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_receptor) {
            error_log("✅ MAIL OK → Salida denegada enviado a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_receptor = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    return ($enviado_encargado || $enviado_receptor);
}

/**
 * Enviar correo cuando se recepciona una salida (al encargado Y receptor confirmando)
 */
function EnviarCorreoSalidaRecepcionada($id_salida)
{
    include("../_conexion/conexion.php");
    
    $id_salida = intval($id_salida);

    $sql = "SELECT 
              s.id_salida,
              s.ndoc_salida,
              s.fec_req_salida,
              s.fec_recepciona_salida,
              s.obs_salida,
              ao.nom_almacen AS almacen_origen,
              uo.nom_ubicacion AS ubicacion_origen,
              ad.nom_almacen AS almacen_destino,
              ud.nom_ubicacion AS ubicacion_destino,
              mt.nom_material_tipo,
              pe.nom_personal AS encargado_nombre,
              pe.email_personal AS encargado_email,
              pr.nom_personal AS receptor_nombre,
              pr.email_personal AS receptor_email,
              prec.nom_personal AS recepcionador_nombre
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
            LEFT JOIN $bd_complemento.personal pe ON s.id_personal_encargado = pe.id_personal
            LEFT JOIN $bd_complemento.personal pr ON s.id_personal_recibe = pr.id_personal
            LEFT JOIN $bd_complemento.personal prec ON s.id_personal_recepciona_salida = prec.id_personal
            WHERE s.id_salida = $id_salida
            AND s.est_salida = 2
            LIMIT 1";
    
    $res = mysqli_query($con, $sql);
    
    if (!$res || mysqli_num_rows($res) == 0) {
        error_log("❌ No se encontró la salida recepcionada ID: $id_salida");
        mysqli_close($con);
        return false;
    }
    
    $salida = mysqli_fetch_assoc($res);
    
    // Validar que al menos UNO tenga email
    if (empty($salida['encargado_email']) && empty($salida['receptor_email'])) {
        error_log("⚠️ No hay emails para enviar - Salida ID: $id_salida");
        mysqli_close($con);
        return false;
    }

    $sql_detalle = "SELECT 
                      sd.cant_salida_detalle,
                      p.nom_producto,
                      um.nom_unidad_medida
                    FROM salida_detalle sd
                    INNER JOIN producto p ON sd.id_producto = p.id_producto
                    INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
                    WHERE sd.id_salida = $id_salida
                    AND sd.est_salida_detalle = 1
                    ORDER BY p.nom_producto";
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    $items = [];
    while ($row = mysqli_fetch_assoc($res_detalle)) {
        $items[] = $row;
    }
    
    mysqli_close($con);
    
    $asunto = "Salida RECEPCIONADA #{$salida['ndoc_salida']} - Proceso Completado";
    
    // Construir tabla de materiales
    $tabla_items = '';
    foreach ($items as $item) {
        $tabla_items .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0;'>{$item['nom_producto']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>" . 
                    number_format($item['cant_salida_detalle'], 2) . "</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['nom_unidad_medida']}</td>
            </tr>";
    }
    
    $fecha_recepcion = !empty($salida['fec_recepciona_salida']) 
        ? date('d/m/Y H:i', strtotime($salida['fec_recepciona_salida'])) 
        : 'No disponible';
    
    $recepcionador = !empty($salida['recepcionador_nombre']) ? $salida['recepcionador_nombre'] : 'No disponible';
    
    $observaciones_html = !empty($salida['obs_salida']) 
        ? "<div style='background-color: #fff8e1; padding: 15px; border-left: 4px solid #ffc107; border-radius: 6px; margin: 20px 0;'>
            <strong>📝 Observaciones:</strong>
            <p style='margin: 5px 0 0 0;'>{$salida['obs_salida']}</p>
          </div>"
        : "";
    
    $mensaje = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 30px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>✅ Salida Recepcionada</h1>
                                <p style='color: #f0f0f0; margin: 10px 0 0 0; font-size: 14px;'>Documento #{$salida['ndoc_salida']}</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 20px; background-color: #d1ecf1; border-left: 4px solid #17a2b8;'>
                                <p style='margin: 0; color: #0c5460; font-weight: bold;'>✅ PROCESO COMPLETADO</p>
                                <p style='margin: 5px 0 0 0; color: #0c5460;'>Esta salida ha sido recepcionada exitosamente. El proceso de traslado está completo.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='padding: 30px;'>
                                <h2 style='color: #333; margin-top: 0; font-size: 18px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;'>Información de Recepción</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Fecha de Recepción:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$fecha_recepcion}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Recepcionado por:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$recepcionador}</td>
                                    </tr>
                                </table>
                                
                                <h2 style='color: #333; margin-top: 25px; font-size: 18px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;'>Detalles de la Salida</h2>
                                
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 20px 0;'>
                                    <tr>
                                        <td style='color: #666; width: 40%;'><strong>Número de Documento:</strong></td>
                                        <td style='color: #333;'>{$salida['ndoc_salida']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Fecha Requerida:</strong></td>
                                        <td style='color: #333;'>" . date('d/m/Y', strtotime($salida['fec_req_salida'])) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Tipo de Material:</strong></td>
                                        <td style='color: #333;'>{$salida['nom_material_tipo']}</td>
                                    </tr>
                                </table>
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Origen y Destino</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 15px 0; background-color: #f8f9fa; border-radius: 4px;'>
                                    <tr>
                                        <td style='color: #666; width: 40%; padding: 12px;'><strong>Almacén Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Origen:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_origen']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Almacén Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['almacen_destino']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666; padding: 12px;'><strong>Ubicación Destino:</strong></td>
                                        <td style='color: #333; padding: 12px;'>{$salida['ubicacion_destino']}</td>
                                    </tr>
                                </table>
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Personal</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='margin: 15px 0;'>
                                    <tr>
                                        <td style='color: #666; width: 40%;'><strong>Encargado:</strong></td>
                                        <td style='color: #333;'>{$salida['encargado_nombre']}</td>
                                    </tr>
                                    <tr>
                                        <td style='color: #666;'><strong>Receptor:</strong></td>
                                        <td style='color: #333;'>{$salida['receptor_nombre']}</td>
                                    </tr>
                                </table>
                                
                                {$observaciones_html}
                                
                                <h3 style='color: #333; font-size: 16px; margin-top: 25px;'>Materiales Recepcionados</h3>
                                <table width='100%' cellpadding='0' cellspacing='0' style='margin: 15px 0; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;'>
                                    <thead>
                                        <tr style='background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);'>
                                            <th style='padding: 12px; text-align: left; color: #ffffff; font-weight: bold;'>Producto</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Cantidad</th>
                                            <th style='padding: 12px; text-align: center; color: #ffffff; font-weight: bold;'>Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$tabla_items}
                                    </tbody>
                                </table>
                                
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='https://arceperusac.com/_controlador/salidas_mostrar.php' 
                                       style='background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); 
                                              color: #ffffff; 
                                              padding: 14px 30px; 
                                              text-decoration: none; 
                                              border-radius: 25px; 
                                              font-weight: bold; 
                                              display: inline-block;
                                              box-shadow: 0 4px 6px rgba(23, 162, 184, 0.3);'>
                                        ✅ Ver Registro Completo
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                <p style='margin: 0; color: #666; font-size: 12px;'>
                                    Este es un correo automático del Sistema de Gestión de Almacén
                                </p>
                                <p style='margin: 5px 0 0 0; color: #999; font-size: 11px;'>
                                    Montajes e Ingeniería Arce Perú SAC © " . date('Y') . "
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    $cabeceras = "MIME-Version: 1.0\r\n";
    $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
    $cabeceras .= "From: ARCE PERÚ <notificaciones@arceperusac.com>\r\n";
    $cabeceras .= "Bcc: notificaciones@arceperusac.com\r\n";
    
    //  ENVIAR AL ENCARGADO
    $enviado_encargado = false;
    if (!empty($salida['encargado_email'])) {
        $mail_encargado = @mail($salida['encargado_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_encargado) {
            error_log("✅ MAIL OK → Salida recepcionada enviado a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_encargado = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a ENCARGADO: {$salida['encargado_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    //  ENVIAR AL RECEPTOR
    $enviado_receptor = false;
    if (!empty($salida['receptor_email'])) {
        $mail_receptor = @mail($salida['receptor_email'], $asunto, $mensaje, $cabeceras);
        
        if ($mail_receptor) {
            error_log("✅ MAIL OK → Salida recepcionada enviado a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
            $enviado_receptor = true;
        } else {
            error_log("❌ MAIL FAIL → Error al enviar a RECEPTOR: {$salida['receptor_email']} (Salida #{$salida['ndoc_salida']})");
        }
    }
    
    return ($enviado_encargado || $enviado_receptor);
}