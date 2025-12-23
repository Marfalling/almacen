<?php
//-----------------------------------------------------------------------
// MODELO: m_pedidos.php
//-----------------------------------------------------------------------

// FUNCIONES CORREGIDAS PARA m_pedidos.php
// Estados: 0=Anulado, 1=Pendiente, 2=Completado, 3=Aprobado, 4=Ingresado, 5=Finalizado

//-----------------------------------------------------------------------
// Grabar Pedido (ahora acepta opcionalmente $id_obra) (debe iniciar con estado 1, no 5)
// Grabar Pedido con centros de costo múltiples por detalle
function GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo, 
                     $nom_pedido, $solicitante, $fecha_necesidad, 
                     $num_ot, $contacto, $lugar_entrega, $aclaraciones, $id_personal, 
                     $materiales, $archivos_subidos, $id_obra = null) 
{
    include("../_conexion/conexion.php");
    require_once("../_modelo/m_stock.php");
    require_once("../_modelo/m_centro_costo.php");

    //  OBTENER CENTRO DE COSTO DEL PERSONAL QUE REGISTRA
    $centro_costo_registrador = ObtenerCentroCostoPersonal($id_personal);
    $id_registrador_centro_costo = $centro_costo_registrador ? intval($centro_costo_registrador['id_centro_costo']) : 'NULL';

    $id_obra_sql = ($id_obra && $id_obra > 0) ? intval($id_obra) : "NULL";

    // Estado inicial debe ser 1 (Pendiente)
    $sql = "INSERT INTO pedido (
                id_producto_tipo, id_almacen, id_ubicacion, id_centro_costo, id_personal, 
                id_registrador_centro_costo, id_obra,
                cod_pedido, nom_pedido, ot_pedido, cel_pedido, lug_pedido, 
                acl_pedido, fec_req_pedido, fec_pedido, est_pedido
            ) VALUES (
                $id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo, $id_personal,
                $id_registrador_centro_costo, $id_obra_sql,
                'TEMP', '" . mysqli_real_escape_string($con, $nom_pedido) . "', 
                '" . mysqli_real_escape_string($con, $num_ot) . "',
                '" . mysqli_real_escape_string($con, $contacto) . "', 
                '" . mysqli_real_escape_string($con, $lugar_entrega) . "',
                '" . mysqli_real_escape_string($con, $aclaraciones) . "', 
                '" . mysqli_real_escape_string($con, $fecha_necesidad) . "', 
                NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_pedido = mysqli_insert_id($con);
        
        // ACTUALIZAR con el código basado en el ID
        $cod_pedido = 'P' . str_pad($id_pedido, 4, '0', STR_PAD_LEFT);
        $sql_update_codigo = "UPDATE pedido SET cod_pedido = '$cod_pedido' WHERE id_pedido = $id_pedido";
        mysqli_query($con, $sql_update_codigo);
        
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_unidad = intval($material['unidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst_descripcion = mysqli_real_escape_string($con, $material['sst_descripcion']); 
            $ot_detalle = mysqli_real_escape_string($con, $material['ot_detalle']);
            $centros_costo = isset($material['centros_costo']) ? $material['centros_costo'] : array();
            $personal_ids = isset($material['personal_ids']) ? $material['personal_ids'] : array();

            // Obtener el nombre de la unidad por su ID
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = $resultado_unidad ? mysqli_fetch_assoc($resultado_unidad) : null;
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            $requisitos = $sst_descripcion;
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            $sql_detalle = "INSERT INTO pedido_detalle (
                                id_pedido, id_producto, prod_pedido_detalle, 
                                ot_pedido_detalle, cant_pedido_detalle, cant_oc_pedido_detalle, 
                                com_pedido_detalle, req_pedido, est_pedido_detalle
                            ) VALUES (
                                $id_pedido, $id_producto, '$descripcion',
                                '$ot_detalle', $cantidad, NULL, 
                                '$comentario_detalle', '$requisitos', 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                $id_detalle = mysqli_insert_id($con);

                // Guardar centros de costo para este detalle
                if (!empty($centros_costo) && is_array($centros_costo)) {
                    foreach ($centros_costo as $id_centro) {
                        $id_centro = intval($id_centro);
                        if ($id_centro > 0) {
                            $sql_cc = "INSERT INTO pedido_detalle_centro_costo 
                                      (id_pedido_detalle, id_centro_costo) 
                                      VALUES ($id_detalle, $id_centro)";
                            
                            if (!mysqli_query($con, $sql_cc)) {
                                error_log("Error al insertar centro de costo: " . mysqli_error($con));
                            }
                        }
                    }
                }

                // Verificar stock y registrar reserva si hay disponible
                $stock = ObtenerStock($id_producto, $id_almacen, $id_ubicacion);
                $stock_disponible = floatval($stock['disponible']);

                /*if ($stock_disponible > 0) {
                    $cantidad_a_reservar = min($cantidad, $stock_disponible);

                    $sql_insert_mov = "INSERT INTO movimiento (
                                            id_personal, id_orden, id_producto, id_almacen, 
                                            id_ubicacion, tipo_orden, tipo_movimiento, 
                                            cant_movimiento, fec_movimiento, est_movimiento
                                        ) VALUES (
                                            '$id_personal', '$id_pedido', '$id_producto', '$id_almacen',
                                            '$id_ubicacion', 5, 2,
                                            '$cantidad_a_reservar', NOW(), 1
                                        )";
                    mysqli_query($con, $sql_insert_mov);
                }*/

                // Guardar personal asignado (CON VALIDACIÓN MANUAL)
                if (!empty($personal_ids) && is_array($personal_ids)) {
                    foreach ($personal_ids as $id_personal_item) {
                        $id_personal_item = intval($id_personal_item);
                        if ($id_personal_item > 0) {
                            // VALIDAR que el personal existe en la BD complementaria
                            $sql_check = "SELECT id_personal FROM {$bd_complemento}.personal 
                                        WHERE id_personal = $id_personal_item 
                                        AND act_personal = 1 
                                        LIMIT 1";
                            $res_check = mysqli_query($con, $sql_check);
                            
                            if ($res_check && mysqli_num_rows($res_check) > 0) {
                                // Personal existe, insertar
                                $sql_personal = "INSERT INTO pedido_detalle_personal 
                                            (id_pedido_detalle, id_personal) 
                                            VALUES ($id_detalle, $id_personal_item)";
                                
                                if (mysqli_query($con, $sql_personal)) {
                                    error_log("Personal ID $id_personal_item asignado correctamente al detalle $id_detalle");
                                } else {
                                    error_log("Error al insertar personal: " . mysqli_error($con));
                                }
                            } else {
                                error_log("Personal ID $id_personal_item no existe o está inactivo - NO SE INSERTÓ");
                            }
                        }
                    }
                }
                
                // Guardar archivos si existen
                if (isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                    foreach ($archivos_subidos[$index]['name'] as $key => $archivo_nombre) {
                        if (!empty($archivo_nombre)) {
                            $extension = pathinfo($archivo_nombre, PATHINFO_EXTENSION);
                            $nuevo_nombre = "pedido_" . $id_pedido . "_detalle_" . $id_detalle . "_" . uniqid() . "." . $extension;
                            
                            $ruta_destino = "../_archivos/pedidos/" . $nuevo_nombre;
                            
                            if (move_uploaded_file($archivos_subidos[$index]['tmp_name'][$key], $ruta_destino)) {
                                $sql_doc = "INSERT INTO pedido_detalle_documento (
                                                id_pedido_detalle, nom_pedido_detalle_documento, 
                                                est_pedido_detalle_documento
                                            ) VALUES ($id_detalle, '$nuevo_nombre', 1)";
                                mysqli_query($con, $sql_doc);
                            }
                        }
                    }
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
function MostrarPedidos()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
                p.*, 
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(c.nom_cliente, 'N/A') AS nom_cliente,
                COALESCE(pr.nom_personal, 'Sin asignar') AS nom_personal,
                COALESCE(a.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(u.nom_ubicacion, 'N/A') AS nom_ubicacion,
                COALESCE(pt.nom_producto_tipo, 'N/A') AS nom_producto_tipo,
                COALESCE(ar.nom_area, 'N/A') AS nom_centro_costo,
                p.est_pedido AS est_pedido_calc,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM pedido_detalle pd 
                        WHERE pd.id_pedido = p.id_pedido 
                          AND (pd.cant_oc_pedido_detalle IS NOT NULL OR pd.cant_os_pedido_detalle IS NOT NULL)
                          AND pd.est_pedido_detalle <> 0
                    ) THEN 1 
                    ELSE 0 
                END AS tiene_verificados
            FROM pedido p
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON p.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen a 
                ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON a.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente c 
                ON a.id_cliente = c.id_cliente AND c.act_cliente = 1
            LEFT JOIN ubicacion u 
                ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
            LEFT JOIN {$bd_complemento}.personal pr 
                ON p.id_personal = pr.id_personal AND pr.act_personal = 1
            LEFT JOIN {$bd_complemento}.area ar 
                ON pr.id_area = ar.id_area AND ar.act_area = 1
            LEFT JOIN producto_tipo pt 
                ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
            WHERE p.est_pedido IN (0, 1, 2, 3, 4, 5)
            ORDER BY p.fec_req_pedido DESC";

    $resc = mysqli_query($con, $sqlc);

    if (!$resc) {
        error_log("Error en MostrarPedidos(): " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }

    $resultado = [];
    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
//----------------------------------------------------------------------
function MostrarPedidosFecha($fecha_inicio = null, $fecha_fin = null, $id_personal_filtro = null)
{
    include("../_conexion/conexion.php");
   
    // ========================================
    //  ACTUALIZAR AUTOMÁTICAMENTE PEDIDOS CON STOCK
    //  CORRECCIÓN: EXCLUIR BASE ARCE (id_almacen = 1)
    // ========================================
    $sql_actualizar = "
    UPDATE pedido p
    INNER JOIN almacen a ON p.id_almacen = a.id_almacen
    SET p.est_pedido = 2
    WHERE p.est_pedido = 1
    AND p.id_personal_aprueba_tecnica IS NOT NULL
    AND p.id_producto_tipo != 2
    AND NOT (a.id_cliente = $id_cliente_arce AND a.id_obra IS NULL)  -- 🔹 EXCLUIR BASE ARCE
    AND NOT EXISTS (
        SELECT 1 FROM pedido_detalle pd 
        WHERE pd.id_pedido = p.id_pedido 
        AND (pd.cant_oc_pedido_detalle IS NOT NULL OR pd.cant_os_pedido_detalle IS NOT NULL)
        AND pd.est_pedido_detalle != 0
    )
    AND NOT EXISTS (
        SELECT 1 FROM pedido_detalle pd2
        WHERE pd2.id_pedido = p.id_pedido
            AND pd2.est_pedido_detalle = 1
            AND pd2.cant_pedido_detalle > (
                SELECT COALESCE(SUM(
                    CASE
                        WHEN m.tipo_movimiento = 1 THEN
                            CASE
                                WHEN m.tipo_orden = 3 AND m.est_movimiento = 1 THEN m.cant_movimiento
                                WHEN m.tipo_orden != 3 THEN m.cant_movimiento
                                ELSE 0
                            END
                        WHEN m.tipo_movimiento = 2 AND m.tipo_orden = 2 THEN -m.cant_movimiento
                        ELSE 0
                    END
                ), 0)
                FROM movimiento m
                WHERE m.id_producto = pd2.id_producto
                AND m.id_almacen = p.id_almacen
                AND m.id_ubicacion = p.id_ubicacion
                AND m.est_movimiento = 1
            )
    )
    ";

    mysqli_query($con, $sql_actualizar);
    // ========================================

    
    // Filtro de fechas
    if ($fecha_inicio && $fecha_fin) {
        $where_fecha = " AND DATE(p.fec_pedido) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where_fecha = "";
    }

    // Filtro por personal
    $where_personal = "";
    if ($id_personal_filtro !== null && $id_personal_filtro > 0) {
        $id_personal_filtro = intval($id_personal_filtro);
        $where_personal = " AND p.id_personal = $id_personal_filtro";
    }

    // Consulta principal - CORREGIDA
    $sqlc = "SELECT 
                p.*, 
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(c.nom_cliente, 'N/A') AS nom_cliente,
                COALESCE(pr.nom_personal, 'Sin asignar') AS nom_personal,
                COALESCE(per2.nom_personal, '-') AS nom_aprobado_tecnica,
                COALESCE(a.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(u.nom_ubicacion, 'N/A') AS nom_ubicacion,
                COALESCE(pt.nom_producto_tipo, 'N/A') AS nom_producto_tipo,
                COALESCE(ar.nom_area, 'N/A') AS nom_centro_costo,
                p.est_pedido AS est_pedido_calc,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM pedido_detalle pd 
                        WHERE pd.id_pedido = p.id_pedido 
                          AND (pd.cant_oc_pedido_detalle IS NOT NULL OR pd.cant_os_pedido_detalle IS NOT NULL)
                          AND pd.est_pedido_detalle <> 0
                    ) THEN 1 
                    ELSE 0 
                END AS tiene_verificados
            FROM pedido p
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON p.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen a 
                ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON a.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente c 
                ON a.id_cliente = c.id_cliente AND c.act_cliente = 1
            LEFT JOIN ubicacion u 
                ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
            LEFT JOIN {$bd_complemento}.personal pr 
                ON p.id_personal = pr.id_personal AND pr.act_personal = 1
            LEFT JOIN {$bd_complemento}.personal per2 
                ON p.id_personal_aprueba_tecnica = per2.id_personal
            LEFT JOIN {$bd_complemento}.area ar 
                ON pr.id_area = ar.id_area AND ar.act_area = 1
            LEFT JOIN producto_tipo pt 
                ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
            WHERE p.est_pedido IN (0, 1, 2, 3, 4, 5)
            $where_fecha
            $where_personal
            ORDER BY p.fec_req_pedido DESC";

    $resc = mysqli_query($con, $sqlc);

    if (!$resc) {
        error_log("❌ ERROR SQL: " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }

    $resultado = [];
    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
function ObtenerPedidosConComprasAnuladas() {
    include("../_conexion/conexion.php");
    
    $pedidos_rechazados = array();
    
    // Solo considerar rechazado si:
    // 1. Tiene al menos una compra
    // 2. TODAS sus compras están anuladas (no tiene ninguna activa)
    $sql = "SELECT DISTINCT c.id_pedido 
            FROM compra c
            WHERE c.id_pedido IN (
                -- Pedidos que tienen compras
                SELECT id_pedido FROM compra
            )
            AND c.id_pedido NOT IN (
                -- Pedidos que tienen al menos UNA compra activa
                SELECT id_pedido 
                FROM compra 
                WHERE est_compra != 0
            )
            GROUP BY c.id_pedido";
    
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $pedidos_rechazados[] = $row['id_pedido'];
        }
    }
    
    mysqli_close($con);
    return $pedidos_rechazados;
}
//-----------------------------------------------------------------------
function ConsultarPedido($id_pedido)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
                p.*, 
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(c.nom_cliente, 'N/A') AS nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') AS nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') AS nom_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') AS nom_producto_tipo,
                COALESCE(ar.nom_area, 'N/A') AS nom_centro_costo
            FROM pedido p
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON p.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen a 
                ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON a.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente c 
                ON a.id_cliente = c.id_cliente AND c.act_cliente = 1
            LEFT JOIN ubicacion u 
                ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
            LEFT JOIN {$bd_complemento}.personal pr 
                ON p.id_personal = pr.id_personal AND pr.act_personal = 1
            LEFT JOIN {$bd_complemento}.area ar 
                ON p.id_centro_costo = ar.id_area AND ar.act_area = 1
            LEFT JOIN producto_tipo pt 
                ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
            WHERE p.id_pedido = ?";

    // Preparar la sentencia
    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido);
    mysqli_stmt_execute($stmt);
    $resc = mysqli_stmt_get_result($stmt);

    $resultado = [];
    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

    // Cierre de recursos
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $resultado;
}
//-----------------------------------------------------------------------
function ObtenerArchivosActivosDetalle($id_pedido_detalle) 
{
    include("../_conexion/conexion.php");
    
    $archivos = array();
    $sql = "SELECT nom_pedido_detalle_documento 
            FROM pedido_detalle_documento 
            WHERE id_pedido_detalle = $id_pedido_detalle 
            AND est_pedido_detalle_documento = 1
            ORDER BY id_pedido_detalle_documento";
    
    $resultado = mysqli_query($con, $sql);
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        if (!empty(trim($fila['nom_pedido_detalle_documento']))) {
            $archivos[] = trim($fila['nom_pedido_detalle_documento']);
        }
    }
    
    mysqli_close($con);
    return $archivos;
}
//-----------------------------------------------------------------------
function ConsultarPedidoDetalle($id_pedido)
{
    include("../_conexion/conexion.php");

    $id_pedido = intval($id_pedido);

    $sqlc = "SELECT pd.*, 
             pd.ot_pedido_detalle,
             GROUP_CONCAT(
                CASE 
                    WHEN pdd.est_pedido_detalle_documento = 1 
                    THEN pdd.nom_pedido_detalle_documento 
                    ELSE NULL 
                END
             ) as archivos,
             p.nom_producto,
             p.cod_material,  
             p.id_producto as id_producto,
             
             --  STOCK EN UBICACIÓN DESTINO (usando movimientos)
             COALESCE(
                (SELECT SUM(CASE
                    -- INGRESOS: Incluye devoluciones SOLO si están CONFIRMADAS (est_movimiento = 1)
                    WHEN mov.tipo_movimiento = 1 THEN
                        CASE
                            WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                            WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                            ELSE 0
                        END
                    -- SALIDAS: Siempre restan
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END)
                FROM movimiento mov
                INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                WHERE mov.id_producto = pd.id_producto 
                AND mov.id_almacen = ped.id_almacen 
                AND mov.id_ubicacion = ped.id_ubicacion
                AND mov.est_movimiento != 0), 0
             ) AS cantidad_disponible_almacen,
             
             --  CANTIDAD YA ORDENADA EN SALIDAS (PENDIENTES + APROBADAS)
             --  CAMBIO CLAVE: Incluye estado 1 (PENDIENTE) para descuento visual inmediato
             (
                SELECT COALESCE(SUM(sd.cant_salida_detalle), 0)
                FROM salida_detalle sd
                INNER JOIN salida s ON sd.id_salida = s.id_salida
                WHERE sd.id_pedido_detalle = pd.id_pedido_detalle
                  AND sd.est_salida_detalle = 1
                  AND s.est_salida IN (1, 3)  --  PENDIENTE (1) + APROBADO (3)
             ) AS cantidad_ya_ordenada_os,
             
             --  CANTIDAD YA ORDENADA EN COMPRAS (sin cambios)
             (
                SELECT COALESCE(SUM(cd.cant_compra_detalle), 0)
                FROM compra_detalle cd
                INNER JOIN compra c ON cd.id_compra = c.id_compra
                WHERE cd.id_pedido_detalle = pd.id_pedido_detalle
                  AND c.est_compra IN (1, 2, 3, 4)
                  AND cd.est_compra_detalle = 1
             ) AS cantidad_ya_ordenada_oc,
             
             --  CANTIDAD PENDIENTE OS (Auto-calculado)
             (
                pd.cant_os_pedido_detalle - (
                    SELECT COALESCE(SUM(sd.cant_salida_detalle), 0)
                    FROM salida_detalle sd
                    INNER JOIN salida s ON sd.id_salida = s.id_salida
                    WHERE sd.id_pedido_detalle = pd.id_pedido_detalle
                      AND sd.est_salida_detalle = 1
                      AND s.est_salida IN (1, 3)
                )
             ) AS cantidad_pendiente_os,
             
             --  CANTIDAD PENDIENTE OC (Auto-calculado)
             (
                pd.cant_oc_pedido_detalle - (
                    SELECT COALESCE(SUM(cd.cant_compra_detalle), 0)
                    FROM compra_detalle cd
                    INNER JOIN compra c ON cd.id_compra = c.id_compra
                    WHERE cd.id_pedido_detalle = pd.id_pedido_detalle
                      AND c.est_compra IN (1, 2, 3, 4)
                      AND cd.est_compra_detalle = 1
                )
             ) AS cantidad_pendiente_oc,
             
            --  STOCK EN OTRAS UBICACIONES (usando movimientos)
            (
                SELECT COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 1 THEN
                        CASE
                            WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                            WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                            ELSE 0
                        END
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0)
                FROM movimiento mov
                INNER JOIN pedido ped2 ON pd.id_pedido = ped2.id_pedido
                WHERE mov.id_producto = pd.id_producto 
                  AND mov.id_almacen = ped2.id_almacen 
                  AND mov.id_ubicacion != ped2.id_ubicacion
                  AND mov.est_movimiento != 0
             ) AS stock_otras_ubicaciones
             
             FROM pedido_detalle pd 
             LEFT JOIN pedido_detalle_documento pdd ON pd.id_pedido_detalle = pdd.id_pedido_detalle
                AND pdd.est_pedido_detalle_documento = 1
             INNER JOIN producto p ON pd.id_producto = p.id_producto
             INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
             WHERE pd.id_pedido = $id_pedido AND pd.est_pedido_detalle IN (1, 2)
             GROUP BY pd.id_pedido_detalle
             ORDER BY pd.id_pedido_detalle";
             
    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("❌ Error en ConsultarPedidoDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }
    
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        // Procesar archivos
        if (!empty($rowc['archivos'])) {
            $archivos_array = explode(',', $rowc['archivos']);
            $archivos_limpio = array();
            
            foreach ($archivos_array as $archivo) {
                $archivo = trim($archivo);
                if (!empty($archivo) && $archivo !== 'NULL') {
                    $archivos_limpio[] = $archivo;
                }
            }
            
            $rowc['archivos'] = !empty($archivos_limpio) ? implode(', ', $archivos_limpio) : '';
        } else {
            $rowc['archivos'] = '';
        }
        
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
// Actualizar Pedido con centros de costo múltiples - FUNCIÓN ACTUALIZADA
function ActualizarPedido($id_pedido, $id_ubicacion, $id_centro_costo, $nom_pedido, $fecha_necesidad, $num_ot, 
                         $contacto, $lugar_entrega, $aclaraciones, $materiales, $archivos_subidos, 
                         $id_personal_editor, $id_obra = null) // 🔹 NUEVO PARÁMETRO
{
    include("../_conexion/conexion.php");
    require_once("../_modelo/m_centro_costo.php");

    // 🔹 OBTENER CENTRO DE COSTO DEL EDITOR
    $centro_costo_editor = ObtenerCentroCostoPersonal($id_personal_editor);
    $id_registrador_centro_costo = $centro_costo_editor ? intval($centro_costo_editor['id_centro_costo']) : 'NULL';

    $id_obra_sql = ($id_obra && $id_obra > 0) ? intval($id_obra) : "NULL";

    // 🔹 ACTUALIZAR PEDIDO PRINCIPAL (CON CENTRO DE COSTO DEL EDITOR)
    $sql = "UPDATE pedido SET 
            id_ubicacion = $id_ubicacion,
            id_centro_costo = $id_centro_costo,
            id_registrador_centro_costo = $id_registrador_centro_costo,
            id_obra = $id_obra_sql,
            nom_pedido = '" . mysqli_real_escape_string($con, $nom_pedido) . "',
            fec_req_pedido = '" . mysqli_real_escape_string($con, $fecha_necesidad) . "',
            ot_pedido = '" . mysqli_real_escape_string($con, $num_ot) . "',
            cel_pedido = '" . mysqli_real_escape_string($con, $contacto) . "',
            lug_pedido = '" . mysqli_real_escape_string($con, $lugar_entrega) . "',
            acl_pedido = '" . mysqli_real_escape_string($con, $aclaraciones) . "'
            WHERE id_pedido = $id_pedido";

    if (mysqli_query($con, $sql)) {
        
        // Obtener todos los detalles existentes para este pedido
        $detalles_existentes = array();
        $sql_existentes = "SELECT id_pedido_detalle FROM pedido_detalle 
                          WHERE id_pedido = $id_pedido AND est_pedido_detalle = 1";
        $result = mysqli_query($con, $sql_existentes);
        while ($row = mysqli_fetch_assoc($result)) {
            $detalles_existentes[] = $row['id_pedido_detalle'];
        }
        
        // Array para trackear qué detalles se están usando
        $detalles_utilizados = array();
        
        // Procesar cada material
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_unidad = intval($material['unidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst_descripcion = mysqli_real_escape_string($con, $material['sst_descripcion']);
            $id_detalle = isset($material['id_detalle']) ? intval($material['id_detalle']) : 0;
            $ot_detalle = mysqli_real_escape_string($con, $material['ot_detalle']);
            $centros_costo = isset($material['centros_costo']) ? $material['centros_costo'] : array(); 
            $personal_ids = isset($material['personal_ids']) ? $material['personal_ids'] : array();

            // OBTENER EL NOMBRE DE LA UNIDAD
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = $resultado_unidad ? mysqli_fetch_assoc($resultado_unidad) : null;
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            $requisitos = $sst_descripcion;
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            if ($id_detalle > 0 && in_array($id_detalle, $detalles_existentes)) {
                // ACTUALIZAR DETALLE EXISTENTE
                $sql_detalle = "UPDATE pedido_detalle SET 
                                id_producto = $id_producto,
                                prod_pedido_detalle = '$descripcion',
                                ot_pedido_detalle = '$ot_detalle',
                                cant_pedido_detalle = $cantidad,
                                com_pedido_detalle = '$comentario_detalle',
                                req_pedido = '$requisitos',
                                est_pedido_detalle = 1
                                WHERE id_pedido_detalle = $id_detalle";
                
                if (mysqli_query($con, $sql_detalle)) {
                    $id_detalle_actual = $id_detalle;
                    $detalles_utilizados[] = $id_detalle;

                    // Actualizar centros de costo
                    if (!empty($centros_costo) && is_array($centros_costo)) {
                        $sql_eliminar_cc = "DELETE FROM pedido_detalle_centro_costo 
                                          WHERE id_pedido_detalle = $id_detalle_actual";
                        mysqli_query($con, $sql_eliminar_cc);
                        
                        foreach ($centros_costo as $id_centro) {
                            $id_centro = intval($id_centro);
                            if ($id_centro > 0) {
                                $sql_cc = "INSERT INTO pedido_detalle_centro_costo 
                                          (id_pedido_detalle, id_centro_costo) 
                                          VALUES ($id_detalle_actual, $id_centro)";
                                mysqli_query($con, $sql_cc);
                            }
                        }
                    }
                    if (!empty($personal_ids) && is_array($personal_ids)) {
                        // Eliminar personal existente
                        $sql_eliminar_personal = "DELETE FROM pedido_detalle_personal 
                                                WHERE id_pedido_detalle = $id_detalle_actual";
                        mysqli_query($con, $sql_eliminar_personal);
                        
                        // Insertar nuevo personal (CON VALIDACIÓN)
                        foreach ($personal_ids as $id_personal_item) {
                            $id_personal_item = intval($id_personal_item);
                            if ($id_personal_item > 0) {
                                // Validar que el personal existe
                                $sql_check = "SELECT id_personal FROM {$bd_complemento}.personal 
                                            WHERE id_personal = $id_personal_item 
                                            AND act_personal = 1 
                                            LIMIT 1";
                                $res_check = mysqli_query($con, $sql_check);
                                
                                if ($res_check && mysqli_num_rows($res_check) > 0) {
                                    $sql_personal = "INSERT INTO pedido_detalle_personal 
                                                (id_pedido_detalle, id_personal) 
                                                VALUES ($id_detalle_actual, $id_personal_item)";
                                    mysqli_query($con, $sql_personal);
                                }
                            }
                        }
                    } else {
                        // Si no hay personal seleccionado, eliminar todas las asignaciones
                        $sql_eliminar_personal = "DELETE FROM pedido_detalle_personal 
                                                WHERE id_pedido_detalle = $id_detalle_actual";
                        mysqli_query($con, $sql_eliminar_personal);
                    }
                }
            } else {
                // INSERTAR NUEVO DETALLE
                $sql_detalle = "INSERT INTO pedido_detalle (
                                    id_pedido, id_producto, prod_pedido_detalle, 
                                    ot_pedido_detalle, cant_pedido_detalle, cant_oc_pedido_detalle, 
                                    com_pedido_detalle, req_pedido, est_pedido_detalle
                                ) VALUES (
                                    $id_pedido, $id_producto, '$descripcion', 
                                    '$ot_detalle',
                                    $cantidad, NULL, 
                                    '$comentario_detalle', '$requisitos', 1
                                )";
                
                if (mysqli_query($con, $sql_detalle)) {
                    $id_detalle_actual = mysqli_insert_id($con);

                    // Insertar centros de costo
                    if (!empty($centros_costo) && is_array($centros_costo)) {
                        foreach ($centros_costo as $id_centro) {
                            $id_centro = intval($id_centro);
                            if ($id_centro > 0) {
                                $sql_cc = "INSERT INTO pedido_detalle_centro_costo 
                                          (id_pedido_detalle, id_centro_costo) 
                                          VALUES ($id_detalle_actual, $id_centro)";
                                mysqli_query($con, $sql_cc);
                            }
                        }
                    }
                    
                    if (!empty($personal_ids) && is_array($personal_ids)) {
                        foreach ($personal_ids as $id_personal_item) {
                            $id_personal_item = intval($id_personal_item);
                            if ($id_personal_item > 0) {
                                // Validar que el personal existe
                                $sql_check = "SELECT id_personal FROM {$bd_complemento}.personal 
                                            WHERE id_personal = $id_personal_item 
                                            AND act_personal = 1 
                                            LIMIT 1";
                                $res_check = mysqli_query($con, $sql_check);
                                
                                if ($res_check && mysqli_num_rows($res_check) > 0) {
                                    $sql_personal = "INSERT INTO pedido_detalle_personal 
                                                (id_pedido_detalle, id_personal) 
                                                VALUES ($id_detalle_actual, $id_personal_item)";
                                    mysqli_query($con, $sql_personal);
                                }
                            }
                        }
                    }
                } else {
                    $id_detalle_actual = 0;
                }
            }
            
            // MANEJO DE ARCHIVOS (código existente)
            if ($id_detalle_actual > 0 && isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                
                // Obtener archivos existentes
                $sql_archivos_existentes = "SELECT nom_pedido_detalle_documento 
                                          FROM pedido_detalle_documento 
                                          WHERE id_pedido_detalle = $id_detalle_actual 
                                          AND est_pedido_detalle_documento = 1";
                $resultado_archivos = mysqli_query($con, $sql_archivos_existentes);
                $archivos_a_eliminar = array();
                
                while ($row_archivo = mysqli_fetch_assoc($resultado_archivos)) {
                    $archivos_a_eliminar[] = $row_archivo['nom_pedido_detalle_documento'];
                }
                
                // Eliminar archivos físicos
                foreach ($archivos_a_eliminar as $nombre_archivo) {
                    $ruta_archivo = "../_archivos/pedidos/" . $nombre_archivo;
                    if (file_exists($ruta_archivo)) {
                        unlink($ruta_archivo);
                    }
                }
                
                // Marcar como inactivos los registros anteriores
                $sql_inactivar_docs = "UPDATE pedido_detalle_documento 
                                      SET est_pedido_detalle_documento = 0 
                                      WHERE id_pedido_detalle = $id_detalle_actual";
                mysqli_query($con, $sql_inactivar_docs);
                
                // Guardar nuevos archivos
                foreach ($archivos_subidos[$index]['name'] as $key => $archivo_nombre) {
                    if (!empty($archivo_nombre)) {
                        $extension = pathinfo($archivo_nombre, PATHINFO_EXTENSION);
                        $nuevo_nombre = "pedido_" . $id_pedido . "_detalle_" . $id_detalle_actual . "_" . $key . "_" . uniqid() . "." . $extension;
                        
                        $ruta_destino = "../_archivos/pedidos/" . $nuevo_nombre;
                        
                        if (move_uploaded_file($archivos_subidos[$index]['tmp_name'][$key], $ruta_destino)) {
                            $sql_doc = "INSERT INTO pedido_detalle_documento (
                                            id_pedido_detalle, nom_pedido_detalle_documento, 
                                            est_pedido_detalle_documento
                                        ) VALUES ($id_detalle_actual, '$nuevo_nombre', 1)";
                            mysqli_query($con, $sql_doc);
                        }
                    }
                }
            }
        }
        
        // Marcar como inactivos los detalles que ya no existen
        if (!empty($detalles_existentes)) {
            $detalles_a_eliminar = array_diff($detalles_existentes, $detalles_utilizados);
            if (!empty($detalles_a_eliminar)) {
                $ids_eliminar = implode(',', $detalles_a_eliminar);
                
                // Eliminar archivos físicos
                $sql_archivos_eliminar = "SELECT nom_pedido_detalle_documento 
                                        FROM pedido_detalle_documento 
                                        WHERE id_pedido_detalle IN ($ids_eliminar) 
                                        AND est_pedido_detalle_documento = 1";
                $resultado_archivos_eliminar = mysqli_query($con, $sql_archivos_eliminar);
                
                while ($row_archivo = mysqli_fetch_assoc($resultado_archivos_eliminar)) {
                    $ruta_archivo = "../_archivos/pedidos/" . $row_archivo['nom_pedido_detalle_documento'];
                    if (file_exists($ruta_archivo)) {
                        unlink($ruta_archivo);
                    }
                }
                
                // Eliminar centros de costo asociados
                $sql_eliminar_cc = "DELETE FROM pedido_detalle_centro_costo 
                                   WHERE id_pedido_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar_cc);
                
                // Eliminar personal asociado
                $sql_eliminar_personal = "DELETE FROM pedido_detalle_personal 
                                         WHERE id_pedido_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar_personal);
                
                // Marcar detalles como inactivos
                $sql_eliminar = "UPDATE pedido_detalle SET est_pedido_detalle = 0 
                                WHERE id_pedido_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar);
                
                // Marcar documentos como inactivos
                $sql_eliminar_docs = "UPDATE pedido_detalle_documento SET est_pedido_detalle_documento = 0 
                                     WHERE id_pedido_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar_docs);
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
/**
 * ConsultarDetallePorId
 * Devuelve un arreglo con información del detalle de pedido y del pedido asociado:
 * id_pedido_detalle, id_pedido, id_producto, cant_pedido_detalle, id_almacen, id_ubicacion, etc.
 */
function ConsultarDetallePorId($id_pedido_detalle) {
    include("../_conexion/conexion.php");

    $sql = "SELECT pd.id_pedido_detalle, pd.id_pedido, pd.id_producto, pd.cant_pedido_detalle, pd.cant_oc_pedido_detalle,
                   p.id_almacen, p.id_ubicacion, p.cod_pedido
            FROM pedido_detalle pd
            INNER JOIN pedido p ON pd.id_pedido = p.id_pedido
            WHERE pd.id_pedido_detalle = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido_detalle);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $row ? $row : null;
}
//-----------------------------------------------------------------------
/**
 * RegistrarMovimientoPedido
 * Inserta un movimiento tipo pedido (reservado) en la tabla movimiento.
 */
function RegistrarMovimientoPedido($id_pedido, $id_producto, $id_almacen, $id_ubicacion, $cantidad)
{
    include("../_conexion/conexion.php");
    $id_personal = $_SESSION['id_personal'] ?? 1; // usa 1 si no existe sesión

    // Verificar si ya hay una reserva activa para ese producto y pedido
    $sql_check = "SELECT id_movimiento 
                  FROM movimiento 
                  WHERE id_orden = '$id_pedido' 
                    AND id_producto = '$id_producto' 
                    AND tipo_orden = 5 
                    AND est_movimiento = 1";
    $res = mysqli_query($con, $sql_check);

    if (mysqli_num_rows($res) == 0) {
        // No existe → insertar nueva reserva
        $sql_insert = "INSERT INTO movimiento (
                            id_personal, id_orden, id_producto, id_almacen, 
                            id_ubicacion, tipo_orden, tipo_movimiento, 
                            cant_movimiento, fec_movimiento, est_movimiento
                        ) VALUES (
                            '$id_personal', '$id_pedido', '$id_producto', '$id_almacen',
                            '$id_ubicacion', 5, 2,
                            '$cantidad', NOW(), 1
                        )";
        mysqli_query($con, $sql_insert);
    } else {
        // Ya existe → actualizar cantidad reservada
        $sql_update = "UPDATE movimiento 
                       SET cant_movimiento = '$cantidad', fec_movimiento = NOW()
                       WHERE id_orden = '$id_pedido' 
                         AND id_producto = '$id_producto' 
                         AND tipo_orden = 5 
                         AND est_movimiento = 1";
        mysqli_query($con, $sql_update);
    }
}
//-----------------------------------------------------------------------
function verificarItem($id_pedido_detalle, $cant_oc, $cant_os)
{
    include("../_conexion/conexion.php");

    // Convertir a float para asegurar decimales
    $cantidad_oc = floatval($cant_oc);
    $cantidad_os = floatval($cant_os);
    
    // Validación: al menos una debe ser mayor a 0
    if ($cantidad_oc <= 0 && $cantidad_os <= 0) {
        mysqli_close($con);
        return "ERROR: Debe haber al menos una cantidad para OS o OC";
    }

    //  Verificar que el detalle exista
    $sql_check = "SELECT id_pedido_detalle FROM pedido_detalle WHERE id_pedido_detalle = $id_pedido_detalle";
    $res_check = mysqli_query($con, $sql_check);
    
    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        mysqli_close($con);
        return "ERROR: Item no encontrado";
    }

    //  Actualizar la cantidad verificada
    $sql_update = "UPDATE pedido_detalle 
                   SET cant_os_pedido_detalle = $cant_os,
                       cant_oc_pedido_detalle = $cant_oc
                   WHERE id_pedido_detalle = $id_pedido_detalle";
    
    if (!mysqli_query($con, $sql_update)) {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }

    //  Verificar que se actualizó
    $filas_afectadas = mysqli_affected_rows($con);
    
    mysqli_close($con);
    
    if ($filas_afectadas > 0) {
        return "SI";
    } else {
        return "ERROR: No se pudo actualizar la cantidad verificada";
    }
}
//-----------------------------------------------------------------------
function PedidoTieneVerificaciones($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT COUNT(*) as total_verificados 
            FROM pedido_detalle 
            WHERE id_pedido = $id_pedido 
            AND cant_oc_pedido_detalle IS NOT NULL 
            AND est_pedido_detalle <> 0";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    
    return ($row['total_verificados'] > 0);
}
//-----------------------------------------------------------------------
function PedidoTieneTodoConStock($id_pedido)
{
    include("../_conexion/conexion.php");
    
    // Obtener almacén y ubicación del pedido
    $sql_pedido = "SELECT id_almacen, id_ubicacion FROM pedido WHERE id_pedido = $id_pedido";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $pedido_info = mysqli_fetch_assoc($res_pedido);
    
    if (!$pedido_info) {
        mysqli_close($con);
        return false;
    }
    
    $id_almacen = $pedido_info['id_almacen'];
    $id_ubicacion = $pedido_info['id_ubicacion'];
    
    // Verificar stock de todos los items activos
    $sql_items = "SELECT 
                    pd.id_producto,
                    pd.cant_pedido_detalle,
                    COALESCE(
                        (SELECT SUM(CASE
                            WHEN mov.tipo_movimiento = 1 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0), 0
                    ) AS stock_disponible
                  FROM pedido_detalle pd
                  WHERE pd.id_pedido = $id_pedido
                  AND pd.est_pedido_detalle = 1";
    
    $res_items = mysqli_query($con, $sql_items);
    
    if (!$res_items || mysqli_num_rows($res_items) == 0) {
        mysqli_close($con);
        return false;
    }
    
    $todos_con_stock = true;
    
    while ($item = mysqli_fetch_assoc($res_items)) {
        if ($item['stock_disponible'] < $item['cant_pedido_detalle']) {
            $todos_con_stock = false;
            break;
        }
    }
    
    mysqli_close($con);
    return $todos_con_stock;
}
//-----------------------------------------------------------------------
function ObtenerItemsParaSalida($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                pd.id_producto,
                pd.prod_pedido_detalle AS descripcion,
                pd.cant_pedido_detalle AS cantidad,
                p.nom_producto,

                -- ==========================================
                -- STOCK FÍSICO: entradas - salidas reales
                -- ==========================================
                SELECT COALESCE((
                    SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 THEN
                            CASE
                                -- Devoluciones confirmadas
                                WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                -- Ingresos normales
                                WHEN mov.tipo_orden = 1 AND mov.est_movimiento != 0 THEN mov.cant_movimiento
                                ELSE 0
                            END
                        WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                    WHERE mov.id_producto = pd.id_producto
                      AND mov.id_almacen = ped.id_almacen
                      AND mov.id_ubicacion = ped.id_ubicacion
                ), 0) AS stock_fisico,

                -- ==========================================
                -- STOCK COMPROMETIDO: pedidos activos
                -- ==========================================
                COALESCE((
                    SELECT SUM(mov.cant_movimiento)
                    FROM movimiento mov
                    INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                    WHERE mov.id_producto = pd.id_producto
                      AND mov.id_almacen = ped.id_almacen
                      AND mov.id_ubicacion = ped.id_ubicacion
                      AND mov.tipo_movimiento = 2
                      AND mov.tipo_orden = 5
                      AND mov.est_movimiento != 0
                      AND mov.id_orden <> pd.id_pedido
                ), 0) AS stock_comprometido,

                -- ==========================================
                -- STOCK DISPONIBLE = físico - comprometido
                -- ==========================================
                COALESCE((
                    SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden = 1 AND mov.est_movimiento != 0 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                    WHERE mov.id_producto = pd.id_producto
                      AND mov.id_almacen = ped.id_almacen
                      AND mov.id_ubicacion = ped.id_ubicacion
                ), 0)
                -
                COALESCE((
                    SELECT SUM(mov.cant_movimiento)
                    FROM movimiento mov
                    INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                    WHERE mov.id_producto = pd.id_producto
                      AND mov.id_almacen = ped.id_almacen
                      AND mov.id_ubicacion = ped.id_ubicacion
                      AND mov.tipo_movimiento = 2
                      AND mov.tipo_orden = 5
                      AND mov.est_movimiento != 0
                      AND mov.id_orden <> pd.id_pedido
                ), 0)
                AS stock_disponible,

                (
                    SELECT id_movimiento
                    FROM movimiento m
                    WHERE m.id_producto = pd.id_producto
                      AND m.id_orden = pd.id_pedido
                      AND m.tipo_orden = 5
                      AND m.est_movimiento = 1
                    ORDER BY m.id_movimiento ASC
                    LIMIT 1
                ) AS id_movimiento_comprometido

            FROM pedido_detalle pd
            INNER JOIN producto p ON pd.id_producto = p.id_producto
            WHERE pd.id_pedido = $id_pedido
              AND pd.est_pedido_detalle IN (1, 2)
            ORDER BY pd.id_pedido_detalle";

    $resultado = mysqli_query($con, $sql);
    $items = array();
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $items[] = $row;
    }
    
    mysqli_close($con);
    return $items;
}
//-----------------------------------------------------------------------
function FinalizarPedido($id_pedido)
{
    include("../_conexion/conexion.php");
    
    // Verificar el estado actual
    $sql_estado = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $res_estado = mysqli_query($con, $sql_estado);
    
    if (!$res_estado || mysqli_num_rows($res_estado) == 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo' => 'error',
            'mensaje' => 'Pedido no encontrado'
        ];
    }
    
    $row_estado = mysqli_fetch_assoc($res_estado);
    $estado_actual = intval($row_estado['est_pedido']);
    
    // Si ya está finalizado (estado 5), retornar éxito
    if ($estado_actual == 5) {
        mysqli_close($con);
        return [
            'success' => true,
            'ya_completado' => true,
            'mensaje' => 'El pedido ya estaba finalizado'
        ];
    }
    
    // Si está anulado, no se puede finalizar
    if ($estado_actual == 0) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo' => 'error',
            'mensaje' => 'No se puede finalizar un pedido anulado'
        ];
    }
    
    // Actualizar el pedido a FINALIZADO (estado = 5)
    $sql_finalizar = "UPDATE pedido SET est_pedido = 4 WHERE id_pedido = $id_pedido";
    
    if (mysqli_query($con, $sql_finalizar)) {
        $verificar = mysqli_affected_rows($con);
        mysqli_close($con);
        
        if ($verificar > 0) {
            return [
                'success' => true,
                'mensaje' => 'El pedido se ha marcado como finalizado exitosamente'
            ];
        } else {
            return [
                'success' => true,
                'ya_completado' => true,
                'mensaje' => 'El pedido ya estaba finalizado'
            ];
        }
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return [
            'success' => false,
            'tipo' => 'error',
            'mensaje' => 'Error al actualizar el pedido: ' . $error
        ];
    }
}
//-----------------------------------------------------------------------
// Verificar si un pedido está listo para finalizarse
//-----------------------------------------------------------------------
function verificarPedidoListo($id_pedido, $con = null)
{
    $cerrar_conexion = false;
    
    if ($con === null) {
        include("../_conexion/conexion.php");
        $cerrar_conexion = true;
    }
    
    // Obtener información del pedido
    $sql_pedido = "SELECT id_almacen, id_ubicacion, id_producto_tipo 
                   FROM pedido 
                   WHERE id_pedido = $id_pedido";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $pedido_info = mysqli_fetch_assoc($res_pedido);
    
    if (!$pedido_info) {
        if ($cerrar_conexion) mysqli_close($con);
        return [
            'listo' => false,
            'mensaje' => 'Pedido no encontrado'
        ];
    }
    
    $id_almacen = $pedido_info['id_almacen'];
    $id_ubicacion = $pedido_info['id_ubicacion'];
    $es_auto_orden = ($pedido_info['id_producto_tipo'] == 2);
    
    // Si es auto-orden, verificar que tenga al menos una orden de compra activa
    if ($es_auto_orden) {
        $sql_compras = "SELECT COUNT(*) as total 
                        FROM compra 
                        WHERE id_pedido = $id_pedido 
                        AND est_compra <> 0";
        $res_compras = mysqli_query($con, $sql_compras);
        $compras = mysqli_fetch_assoc($res_compras);
        
        if ($compras['total'] == 0) {
            if ($cerrar_conexion) mysqli_close($con);
            return [
                'listo' => false,
                'mensaje' => 'Debe crear al menos una orden de compra para este pedido tipo AUTO-ORDEN'
            ];
        }
    }
    
    // Obtener todos los items activos del pedido
    $sql_items = "SELECT 
                    pd.id_pedido_detalle,
                    pd.id_producto,
                    pd.cant_pedido_detalle,
                    pd.est_pedido_detalle,
                    COALESCE(
                        (SELECT SUM(CASE
                            WHEN mov.tipo_movimiento = 1 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0), 0
                    ) AS stock_disponible
                  FROM pedido_detalle pd
                  WHERE pd.id_pedido = $id_pedido
                  AND pd.est_pedido_detalle IN (1, 2) ";
    
    $res_items = mysqli_query($con, $sql_items);
    
    if (!$res_items || mysqli_num_rows($res_items) == 0) {
        if ($cerrar_conexion) mysqli_close($con);
        return [
            'listo' => false,
            'mensaje' => 'No hay items activos en el pedido'
        ];
    }
    
    $items_pendientes = [];
    $todos_con_stock = true;
    
    while ($item = mysqli_fetch_assoc($res_items)) {
        $tiene_stock_suficiente = ($item['stock_disponible'] >= $item['cant_pedido_detalle']);
        
        // Si NO tiene stock suficiente
        if (!$tiene_stock_suficiente) {
            $todos_con_stock = false;
            
            // Verificar si el item está en alguna orden de compra activa
            $sql_en_compra = "SELECT COUNT(*) as en_compra
                             FROM compra_detalle cd
                             INNER JOIN compra c ON cd.id_compra = c.id_compra
                             WHERE c.id_pedido = $id_pedido
                             AND cd.id_producto = {$item['id_producto']}
                             AND c.est_compra <> 0
                             AND cd.est_compra_detalle = 1";
            $res_en_compra = mysqli_query($con, $sql_en_compra);
            $en_compra = mysqli_fetch_assoc($res_en_compra);
            
            $esta_en_orden = ($en_compra['en_compra'] > 0);
            
            // El item está pendiente si NO tiene stock Y NO está en ninguna orden
            if (!$esta_en_orden) {
                $items_pendientes[] = $item['id_pedido_detalle'];
            }
        }
    }
    
    if ($cerrar_conexion) {
        mysqli_close($con);
    }
    
    // Si TODOS los items tienen stock suficiente, el pedido está listo
    if ($todos_con_stock) {
        return [
            'listo' => true,
            'mensaje' => 'Todos los items tienen stock disponible'
        ];
    }
    
    // Si hay items pendientes (sin stock y sin orden de compra)
    if (!empty($items_pendientes)) {
        return [
            'listo' => false,
            'mensaje' => 'Hay ' . count($items_pendientes) . ' item(s) pendiente(s) que necesitan una orden de compra'
        ];
    }
    
    // Si llegamos aquí, todos los items sin stock están en órdenes de compra
    return [
        'listo' => true,
        'mensaje' => 'El pedido tiene stock suficiente y puede marcarse como completado'
    ];
}
//-----------------------------------------------------------------------
// ============================================================================
// CORRECCIÓN: CrearOrdenCompra - Verificar cierre correcto por detalle
// ============================================================================
function CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, 
                         $observacion, $direccion, $plazo_entrega, $porte, 
                         $fecha_orden, $items, 
                         $id_detraccion = null, $archivos_homologacion = [],
                         $id_retencion = null, $id_percepcion = null) 
{
    include("../_conexion/conexion.php");

    // 🔹 VALIDAR CANTIDADES ANTES DE CREAR (sin id_compra porque es nueva)
    $errores = ValidarCantidadesOrden($id_pedido, $items, NULL);
    if (!empty($errores)) {
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }

    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';
    $id_retencion_sql = ($id_retencion && $id_retencion > 0) ? $id_retencion : 'NULL';
    $id_percepcion_sql = ($id_percepcion && $id_percepcion > 0) ? $id_percepcion : 'NULL';

    $sql = "INSERT INTO compra (
                id_pedido, id_proveedor, id_moneda, id_personal, id_personal_aprueba, 
                obs_compra, denv_compra, plaz_compra, port_compra, 
                id_detraccion, id_retencion, id_percepcion,
                fec_compra, est_compra, fecha_reg_compra
            ) VALUES (
                $id_pedido, $proveedor, $moneda, $id_personal, NULL, 
                '$observacion', '$direccion', '$plazo_entrega', '$porte', 
                $id_detraccion_sql, $id_retencion_sql, $id_percepcion_sql,
                '$fecha_orden', 1, NOW()
            )";
            
    if (mysqli_query($con, $sql)) {
        $id_compra = mysqli_insert_id($con);
        
        // 🔹 Array para trackear detalles afectados
        $detalles_afectados = array();
        
        foreach ($items as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            $id_detalle = intval($item['id_detalle']);
            
            // Guardar en el array de afectados
            $detalles_afectados[] = $id_detalle;
            
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_detalle]) && !empty($archivos_homologacion[$id_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_prod_" . $id_producto . "_" . uniqid() . "." . $extension;
                $ruta_destino = "../_archivos/homologaciones/" . $nombre_archivo_hom;
                
                if (!file_exists("../_archivos/homologaciones/")) {
                    mkdir("../_archivos/homologaciones/", 0777, true);
                }
                
                move_uploaded_file($archivo['tmp_name'], $ruta_destino);
            }
            
            $hom_sql = $nombre_archivo_hom ? "'" . mysqli_real_escape_string($con, $nombre_archivo_hom) . "'" : "NULL";
            
            $sql_detalle = "INSERT INTO compra_detalle (
                                id_compra, id_pedido_detalle, id_producto, 
                                cant_compra_detalle, prec_compra_detalle, 
                                igv_compra_detalle, hom_compra_detalle, est_compra_detalle
                            ) VALUES (
                                $id_compra, $id_detalle, $id_producto, 
                                $cantidad, $precio_unitario, $igv, $hom_sql, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                $id_compra_detalle = mysqli_insert_id($con);
                
                //  HEREDAR CENTROS DE COSTO AUTOMÁTICAMENTE
                if ($id_detalle > 0 && $id_compra_detalle > 0) {
                    error_log("🔄 Heredando centros para compra_detalle $id_compra_detalle desde pedido_detalle $id_detalle");
                    
                    // Obtener centros del pedido_detalle
                    $sql_centros = "SELECT id_centro_costo 
                                   FROM pedido_detalle_centro_costo 
                                   WHERE id_pedido_detalle = $id_detalle";
                    
                    $result_centros = mysqli_query($con, $sql_centros);
                    $centros_heredados = array();
                    
                    while ($row = mysqli_fetch_assoc($result_centros)) {
                        $centros_heredados[] = intval($row['id_centro_costo']);
                    }
                    
                    // Guardar centros en compra_detalle
                    if (!empty($centros_heredados)) {
                        require_once("m_compras.php");
                        GuardarCentrosCostoCompraDetalle($con, $id_compra_detalle, $centros_heredados);
                        error_log("✅ Heredados " . count($centros_heredados) . " centros de costo");
                    } else {
                        error_log("⚠️ Pedido_detalle $id_detalle no tiene centros de costo asignados");
                    }
                }
            } else {
                error_log("ERROR al insertar detalle: " . mysqli_error($con));
            }
        }
        
        // 🔹 VERIFICAR ESTADO DE CADA DETALLE AFECTADO
        foreach ($detalles_afectados as $id_pedido_detalle) {
            VerificarEstadoItemPorDetalle($id_pedido_detalle);
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $err = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $err;
    }
}
function ActualizarOrdenCompra($id_compra, $proveedor, $moneda, $observacion, $direccion, 
                              $plazo_entrega, $porte, $fecha_orden, $items, 
                              $id_detraccion = null, $archivos_homologacion = [],
                              $id_retencion = null, $id_percepcion = null) {
    include("../_conexion/conexion.php");
    
    error_log("🔧 ActualizarOrdenCompra - ID Compra: $id_compra");
    
    // Obtener id_pedido antes de actualizar
    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = $id_compra";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido['id_pedido'];
    
    error_log("📋 ID Pedido obtenido: $id_pedido");
    error_log("🔍 Items recibidos para actualizar: " . print_r($items, true));
    
    // 🔹 VALIDAR CANTIDADES ANTES DE ACTUALIZAR
    $errores = ValidarCantidadesOrden($id_pedido, $items, $id_compra);
    
    if (!empty($errores)) {
        error_log("❌ Errores de validación: " . implode(", ", $errores));
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }
    
    error_log("✅ Validación pasada, continuando con actualización...");
    
    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';
    $id_retencion_sql = ($id_retencion && $id_retencion > 0) ? $id_retencion : 'NULL';
    $id_percepcion_sql = ($id_percepcion && $id_percepcion > 0) ? $id_percepcion : 'NULL';
    
    $sql = "UPDATE compra SET 
            id_proveedor = $proveedor, 
            id_moneda = $moneda, 
            obs_compra = '$observacion', 
            denv_compra = '$direccion', 
            plaz_compra = '$plazo_entrega', 
            port_compra = '$porte', 
            id_detraccion = $id_detraccion_sql,
            id_retencion = $id_retencion_sql,
            id_percepcion = $id_percepcion_sql,
            fec_compra = '$fecha_orden' 
            WHERE id_compra = $id_compra";
    
    if (mysqli_query($con, $sql)) {
        // 🔹 RASTREAR DETALLES AFECTADOS (por id_pedido_detalle)
        $detalles_afectados = array();
        
        foreach ($items as $id_compra_detalle => $item) {
            $id_compra_detalle = intval($id_compra_detalle);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            
            error_log("   🔄 Actualizando compra_detalle ID: $id_compra_detalle | Nueva cantidad: $cantidad");
            
            // 🔹 OBTENER ID_PEDIDO_DETALLE del compra_detalle ANTES de actualizar
            $sql_detalle_info = "SELECT id_pedido_detalle, id_producto FROM compra_detalle WHERE id_compra_detalle = $id_compra_detalle";
            $res_detalle_info = mysqli_query($con, $sql_detalle_info);
            $row_detalle_info = mysqli_fetch_assoc($res_detalle_info);
            
            if ($row_detalle_info) {
                $id_pedido_detalle_actual = intval($row_detalle_info['id_pedido_detalle']);
                $id_producto_actual = intval($row_detalle_info['id_producto']);
                
                // 🔹 GUARDAR SOLO UNA VEZ CADA ID_PEDIDO_DETALLE
                if (!isset($detalles_afectados[$id_pedido_detalle_actual])) {
                    $detalles_afectados[$id_pedido_detalle_actual] = $id_producto_actual;
                    error_log("   📌 Detalle afectado registrado: pedido_detalle=$id_pedido_detalle_actual | producto=$id_producto_actual");
                }
            }
            
            // Manejar archivo de homologación si existe
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_compra_detalle]) && !empty($archivos_homologacion[$id_compra_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_compra_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_det_" . $id_compra_detalle . "_" . uniqid() . "." . $extension;
                $ruta_destino = "../_archivos/homologaciones/" . $nombre_archivo_hom;
                
                if (!file_exists("../_archivos/homologaciones/")) {
                    mkdir("../_archivos/homologaciones/", 0777, true);
                }
                
                move_uploaded_file($archivo['tmp_name'], $ruta_destino);
            }
            
            $sql_detalle = "UPDATE compra_detalle 
                           SET cant_compra_detalle = $cantidad,
                               prec_compra_detalle = $precio_unitario,
                               igv_compra_detalle = $igv";
            
            if ($nombre_archivo_hom) {
                $sql_detalle .= ", hom_compra_detalle = '" . mysqli_real_escape_string($con, $nombre_archivo_hom) . "'";
            }
            
            $sql_detalle .= " WHERE id_compra_detalle = $id_compra_detalle";
            
            if (!mysqli_query($con, $sql_detalle)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR en detalle: " . $error;
            }
        }
        
        // 🔹 VERIFICAR REAPERTURA/CIERRE POR CADA DETALLE AFECTADO (UNA SOLA VEZ)
        error_log("🔄 Verificando reapertura/cierre de " . count($detalles_afectados) . " detalles únicos...");
        foreach ($detalles_afectados as $id_pedido_detalle => $id_producto) {
            error_log("   🔍 Procesando detalle: $id_pedido_detalle (producto: $id_producto)");
            VerificarEstadoItemPorDetalle($id_pedido_detalle);
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}


// Nueva función para obtener cantidad ya ordenada
function ObtenerCantidadYaOrdenada($id_pedido, $id_producto) {
    include("../_conexion/conexion.php");
    
    // 🔹 CORRECCIÓN: Excluir órdenes anuladas (est_compra = 0)
    $sql = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra
            WHERE c.id_pedido = $id_pedido 
            AND cd.id_producto = $id_producto
            AND c.est_compra != 0  -- 🔹 IMPORTANTE: Excluir anuladas
            AND cd.est_compra_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return floatval($row['total_ordenado']);
}

// Función para obtener cantidad pendiente
function ObtenerCantidadPendienteOrdenar($id_pedido, $id_producto) {
    include("../_conexion/conexion.php");
    
    // Obtener cantidad verificada
    $sql_verificada = "SELECT pd.cant_oc_pedido_detalle
                       FROM pedido_detalle pd
                       WHERE pd.id_pedido = $id_pedido 
                       AND pd.id_producto = $id_producto
                       AND pd.est_pedido_detalle IN (1, 2)
                       LIMIT 1";
    
    $res_verificada = mysqli_query($con, $sql_verificada);
    $row_verificada = mysqli_fetch_assoc($res_verificada);
    $cant_verificada = $row_verificada ? floatval($row_verificada['cant_oc_pedido_detalle']) : 0;
    
    //  Excluir anuladas
    $cant_ordenada = ObtenerCantidadYaOrdenada($id_pedido, $id_producto);
    
    mysqli_close($con);
    
    $cantidad_pendiente = $cant_verificada - $cant_ordenada;
    return max(0, $cantidad_pendiente);
}

//-----------------------------------------------------------------------
function ObtenerOrdenPorId($id_compra) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT c.*, p.id_pedido, 
            d.nombre_detraccion, d.porcentaje as porcentaje_detraccion,
            r.nombre_detraccion as nombre_retencion, r.porcentaje as porcentaje_retencion,
            per.nombre_detraccion as nombre_percepcion, per.porcentaje as porcentaje_percepcion
            FROM compra c 
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido 
            LEFT JOIN detraccion d ON c.id_detraccion = d.id_detraccion
            LEFT JOIN detraccion r ON c.id_retencion = r.id_detraccion
            LEFT JOIN detraccion per ON c.id_percepcion = per.id_detraccion
            WHERE c.id_compra = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_compra);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $orden = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $orden;
}
//-----------------------------------------------------------------------
function ObtenerDetalleOrden($id_compra) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT cd.*, p.nom_producto 
            FROM compra_detalle cd 
            INNER JOIN producto p ON cd.id_producto = p.id_producto 
            WHERE cd.id_compra = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_compra);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $detalles = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $detalles[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $detalles;
}

// ============================================================================
// NUEVA FUNCIÓN: Verificar estado correcto del item (cerrado/abierto) por detalle
// ============================================================================
function VerificarEstadoItemPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    // Validar que la conexión esté activa
    if (!$con) {
        error_log("❌ Error de conexión a la base de datos");
        return;
    }
    
    error_log("🔍 VerificarEstadoItemPorDetalle - ID: $id_pedido_detalle");
    
    // 🔹 OBTENER DATOS DEL DETALLE + INFO DEL ALMACÉN
    $sql_detalle = "SELECT 
                        pd.cant_pedido_detalle, 
                        pd.cant_os_pedido_detalle, 
                        pd.cant_oc_pedido_detalle,
                        pd.id_producto,
                        p.id_almacen, 
                        p.id_ubicacion,
                        a.id_cliente,
                        a.id_obra
                    FROM pedido_detalle pd
                    INNER JOIN pedido p ON pd.id_pedido = p.id_pedido
                    INNER JOIN almacen a ON p.id_almacen = a.id_almacen
                    WHERE pd.id_pedido_detalle = $id_pedido_detalle";
    
    error_log("📝 SQL ejecutado: $sql_detalle");
    
    $res_detalle = mysqli_query($con, $sql_detalle);
    
    if (!$res_detalle) {
        error_log("❌ ERROR en consulta SQL: " . mysqli_error($con));
        mysqli_close($con);
        return;
    }
    
    if (mysqli_num_rows($res_detalle) == 0) {
        error_log("❌ No se encontró el detalle con ID: $id_pedido_detalle");
        mysqli_close($con);
        return;
    }
    
    $detalle = mysqli_fetch_assoc($res_detalle);
    
    if (!$detalle) {
        error_log("❌ Error al obtener datos del detalle: $id_pedido_detalle");
        mysqli_close($con);
        return;
    }
    
    $cantidad_pedida = floatval($detalle['cant_pedido_detalle']);
    $cant_os_verificada = floatval($detalle['cant_os_pedido_detalle']);
    $cant_oc_verificada = floatval($detalle['cant_oc_pedido_detalle']);
    $id_producto = intval($detalle['id_producto']);
    $id_cliente = intval($detalle['id_cliente']);
    $id_obra = $detalle['id_obra'];
    
    // ============================================================
    // 🔹 DETECTAR SI ES BASE ARCE
    // ============================================================
    $es_base_arce = ($id_cliente == $id_cliente_arce && $id_obra === NULL);
    
    if ($es_base_arce) {
        error_log(" BASE ARCE detectado en VerificarEstadoItemPorDetalle");
    }
    
    error_log(" Datos obtenidos - Cantidad Pedida: $cantidad_pedida | OS: $cant_os_verificada | OC: $cant_oc_verificada | Producto: $id_producto");
    
    // 🔹 Obtener cantidades ordenadas
    $total_ordenado_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($id_pedido_detalle);
    $total_recepcionado_os = ObtenerCantidadRecepcionadaOSPorDetalle($id_pedido_detalle); 
    
    error_log("📊 VerificarEstadoItemPorDetalle - Detalle: $id_pedido_detalle");
    error_log("   OS Verificada: $cant_os_verificada | OS Recepcionada: $total_recepcionado_os | Pendiente: " . ($cant_os_verificada - $total_recepcionado_os));
    error_log("   OC Verificada: $cant_oc_verificada | OC Ordenada: $total_ordenado_oc | Pendiente: " . ($cant_oc_verificada - $total_ordenado_oc));    
    
    // ============================================================
    // 🔹 LÓGICA DIFERENCIADA: BASE ARCE vs NORMAL
    // ============================================================
    $estado = 1; // Por defecto: ABIERTO
    
    if ($es_base_arce) {
        // ═══════════════════════════════════════════════════════
        // PARA BASE ARCE: Validar contra cantidad PEDIDA
        // ═══════════════════════════════════════════════════════
        $pendiente_oc = max(0, $cantidad_pedida - $total_ordenado_oc);
        
        error_log(" BASE ARCE - Pendientes - OC: $pendiente_oc (Pedida: $cantidad_pedida | Ordenada: $total_ordenado_oc)");
        
        if ($pendiente_oc <= 0) {
            $estado = 1; // FINALIZADO (mantiene abierto para re-verificar)
            error_log(" BASE ARCE - Item FINALIZADO (OC completada - mantiene estado 1)");
        } else {
            error_log(" BASE ARCE - Item ABIERTO (OC pendiente: $pendiente_oc)");
        }
        
    } else {
        // ═══════════════════════════════════════════════════════
        // PARA PEDIDOS NORMALES: Validar contra cantidades VERIFICADAS
        // ═══════════════════════════════════════════════════════
        $pendiente_os = max(0, $cant_os_verificada - $total_recepcionado_os);
        $pendiente_oc = max(0, $cant_oc_verificada - $total_ordenado_oc);
        error_log(" Pendientes - OS: $pendiente_os | OC: $pendiente_oc");
        
        // CASO 1: Solo se verificó OC (sin OS)
        if ($cant_oc_verificada > 0 && $cant_os_verificada == 0) {
            if ($pendiente_oc <= 0) {
                $estado = 1;
                error_log(" Item FINALIZADO (Solo OC verificada y completada - mantiene estado 1)");
            } else {
                error_log("🔓 Item ABIERTO (OC pendiente: $pendiente_oc)");
            }
        }
        // CASO 2: Solo se verificó OS (sin OC)
        elseif ($cant_os_verificada > 0 && $cant_oc_verificada == 0) {
            if ($pendiente_os <= 0) {
            $estado = 1; // FINALIZADO (mantiene abierto para poder re-verificar)
                error_log("✅ Item FINALIZADO (Solo OS verificada y completada - mantiene estado 1)");
            } else {
                error_log("🔓 Item ABIERTO (OS pendiente: $pendiente_os)");
            }
        }
        // CASO 3: Se verificaron AMBAS (OC + OS)
        elseif ($cant_oc_verificada > 0 && $cant_os_verificada > 0) {
            if ($pendiente_os <= 0 && $pendiente_oc <= 0) {
                $estado = 1;
                error_log("✅ Item FINALIZADO (OC y OS completadas - mantiene estado 1)");
            } else {
                error_log("🔓 Item ABIERTO (Pendiente OC: $pendiente_oc | Pendiente OS: $pendiente_os)");
            }
        }
        // CASO 4: No se verificó nada
        else {
            error_log("⚠️ Item sin verificación");
        }
    }
    
    // Actualizar estado en la base de datos
    $sql_update = "UPDATE pedido_detalle SET est_pedido_detalle = $estado 
                   WHERE id_pedido_detalle = $id_pedido_detalle";
    
    error_log("📝 SQL Update: $sql_update");
    
    if (mysqli_query($con, $sql_update)) {
        error_log("🎯 Item actualizado: " . ($estado == 1 ? 'ABIERTO' : 'CERRADO'));
    } else {
        error_log("❌ Error actualizando estado: " . mysqli_error($con));
    }
    
    mysqli_close($con);
}
// ============================================================================
// CORRECCIÓN: ObtenerCantidadYaOrdenadaOCPorDetalle
// ============================================================================
function ObtenerCantidadYaOrdenadaOCPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    // 🔹 Suma solo las cantidades asociadas a este detalle específico (órdenes activas)
    $sql = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra
            WHERE cd.id_pedido_detalle = $id_pedido_detalle
            AND c.est_compra != 0
            AND cd.est_compra_detalle = 1";
    
    error_log("   📊 SQL ObtenerCantidadYaOrdenadaOCPorDetalle: $sql");
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("   ❌ ERROR en ObtenerCantidadYaOrdenadaOCPorDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_ordenado']);
    
    error_log("   ✅ Total ordenado para detalle $id_pedido_detalle: $total");
    
    mysqli_close($con);
    return $total;
}

/**
 * Obtiene la cantidad YA ORDENADA en salidas (incluyendo pendientes)
 * PROPÓSITO: Descuento visual inmediato, igual que las OC
 * 
 * @param int $id_pedido_detalle
 * @return float Cantidad ya ordenada en salidas (pendientes + aprobadas)
 */
function ObtenerCantidadYaOrdenadaOSPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    //  Contar PENDIENTES (1) 
    // Esto da el descuento visual inmediato que el usuario necesita
    $sql = "SELECT 
                COALESCE(SUM(sd.cant_salida_detalle), 0) as total_ordenado
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
              AND sd.est_salida_detalle = 1
              AND s.est_salida = 1  --  PENDIENTE (1) 
            ";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("❌ Error en ObtenerCantidadYaOrdenadaOSPorDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_ordenado']);
    
    mysqli_close($con);
    
    return $total;
}
/**
 * Obtiene la cantidad REALMENTE COMPLETADA (recepcionada)
 * PROPÓSITO: Para cerrar items solo cuando se recepcionen las salidas
 * 
 * @param int $id_pedido_detalle
 * @return float Cantidad recepcionada (estado 2)
 */
function ObtenerCantidadRecepcionadaOSPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    //  SOLO RECEPCIONADAS (2) para cierre definitivo
    $sql = "SELECT 
                COALESCE(SUM(sd.cant_salida_detalle), 0) as total_recepcionado
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
              AND sd.est_salida_detalle = 1
              AND s.est_salida = 2  --  SOLO RECEPCIONADA (2)
            ";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("❌ Error en ObtenerCantidadRecepcionadaOSPorDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_recepcionado']);
    
    mysqli_close($con);
    
    return $total;
}
/**
 * Obtiene SOLO las salidas APROBADAS (para validaciones de stock real)
 * PROPÓSITO: Validaciones de stock físico real
 * 
 * @param int $id_pedido_detalle
 * @return float Cantidad realmente entregada (solo aprobadas)
 */
function ObtenerCantidadRealmenteEntregadaOS($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    // Solo salidas APROBADAS (estado 3) para stock real
    $sql = "SELECT 
                COALESCE(SUM(
                    CASE 
                        WHEN s.est_salida = 3 THEN (
                            SELECT COALESCE(SUM(md.cant_movimiento_detalle), 0)
                            FROM movimiento_detalle md
                            INNER JOIN movimiento m ON md.id_movimiento = m.id_movimiento
                            WHERE md.id_pedido_detalle = $id_pedido_detalle
                              AND m.id_salida = s.id_salida
                              AND m.est_movimiento = 1
                        )
                        ELSE 0
                    END
                ), 0) AS total_entregado
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
              AND sd.est_salida_detalle = 1
              AND s.est_salida = 3";  
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("❌ Error en ObtenerCantidadRealmenteEntregadaOS: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_entregado']);
    
    mysqli_close($con);
    
    return $total;
}


function VerificarReaperturaItemPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $sql_verificada = "SELECT cant_oc_pedido_detalle, cant_os_pedido_detalle
                       FROM pedido_detalle 
                       WHERE id_pedido_detalle = $id_pedido_detalle";
    $res = mysqli_query($con, $sql_verificada);
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        mysqli_close($con);
        return;
    }
    
    $cant_verificada_oc = floatval($row['cant_oc_pedido_detalle']);
    $cant_verificada_os = floatval($row['cant_os_pedido_detalle']);
    
    // Obtener cantidad ordenada para este detalle específico
    $total_ordenado_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($id_pedido_detalle);
    $total_ordenado_os = ObtenerCantidadYaOrdenadaOSPorDetalle($id_pedido_detalle);
    
    // Si la cantidad ordenada es menor a la verificada, REABRIR SI AL MENOS UNO NO ESTÁ COMPLETO
    if ($total_ordenado_oc < $cant_verificada_oc || $total_ordenado_os < $cant_verificada_os) {
        error_log("   🔓 REABRIENDO item (falta completar OC o OS)");
        $sql_reabrir = "UPDATE pedido_detalle 
                        SET est_pedido_detalle = 1 
                        WHERE id_pedido_detalle = $id_pedido_detalle";
        mysqli_query($con, $sql_reabrir);
    } else {
        error_log("   🔒 Item permanece CERRADO (ambos completos)");
    }
    
    mysqli_close($con);
}
/**
 * Verificar si un item de pedido debe reabrirse después de editar/anular una orden
 */
function VerificarReaperturaItem($id_pedido, $id_producto)
{
    include("../_conexion/conexion.php");
    
    // Obtener cantidad verificada del item
    $sql_verificada = "SELECT cant_oc_pedido_detalle, cant_os_pedido_detalle, id_pedido_detalle
                       FROM pedido_detalle 
                       WHERE id_pedido = $id_pedido 
                       AND id_producto = $id_producto 
                       AND cant_oc_pedido_detalle IS NOT NULL
                       AND cant_os_pedido_detalle IS NOT NULL
                       LIMIT 1";
    $res = mysqli_query($con, $sql_verificada);
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        mysqli_close($con);
        return;
    }
    
    $cant_verificada_oc = floatval($row['cant_oc_pedido_detalle']);
    $cant_verificada_os = floatval($row['cant_os_pedido_detalle']);
    $id_pedido_detalle = $row['id_pedido_detalle'];
    
    // Calcular cantidad total en órdenes de compra activas (excluir anuladas)
    $sql_ordenada_oc = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                        FROM compra_detalle cd
                        INNER JOIN compra c ON cd.id_compra = c.id_compra
                        WHERE c.id_pedido = $id_pedido 
                        AND cd.id_producto = $id_producto
                        AND c.est_compra != 0
                        AND cd.est_compra_detalle = 1";
    $res_oc = mysqli_query($con, $sql_ordenada_oc);
    $row_oc = mysqli_fetch_assoc($res_oc);
    $total_ordenado_oc = floatval($row_oc['total_ordenado']);
    
    // Calcular cantidad total en órdenes de salida activas (excluir anuladas)
    $sql_ordenada_os = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_ordenado
                        FROM salida_detalle sd
                        INNER JOIN salida s ON sd.id_salida = s.id_salida
                        WHERE s.id_pedido = $id_pedido 
                        AND sd.id_producto = $id_producto
                        AND s.est_salida != 0
                        AND sd.est_salida_detalle = 1";
    $res_os = mysqli_query($con, $sql_ordenada_os);
    $row_os = mysqli_fetch_assoc($res_os);
    $total_ordenado_os = floatval($row_os['total_ordenado']);
    
    // Si la cantidad ordenada en os u oc es menor a la verificada, reabrir el item
    if ($total_ordenado_oc < $cant_verificada_oc || $total_ordenado_os < $cant_verificada_os) {
        error_log("   🔓 REABRIENDO item (falta completar OC o OS)");
        $sql_reabrir = "UPDATE pedido_detalle 
                        SET est_pedido_detalle = 1 
                        WHERE id_pedido_detalle = $id_pedido_detalle";
        mysqli_query($con, $sql_reabrir);
    } else {
        error_log("   🔒 Item permanece CERRADO (ambos completos)");
    }
    
    mysqli_close($con);
}

/** FALTA REPLICAR SALIDA
 * Validar que las cantidades no excedan lo verificado
 * id_pedido_detalle EN LUGAR DE id_producto
 */
function ValidarCantidadesOrden($id_pedido, $items_orden, $id_compra_actual = null)
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    error_log("🔍 ValidarCantidadesOrden - Pedido: $id_pedido | Compra actual: " . ($id_compra_actual ?? 'NUEVA'));
    
    //  DETECTAR SI ES PEDIDO BASE ARCE (las variables ya vienen del include)
    $sql_pedido = "SELECT p.id_almacen, a.id_cliente, a.id_obra 
                   FROM pedido p
                   INNER JOIN almacen a ON p.id_almacen = a.id_almacen
                   WHERE p.id_pedido = $id_pedido";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    
    $es_pedido_base_arce = false;
    if ($row_pedido) {
        $id_cliente_pedido = intval($row_pedido['id_cliente']);
        $id_obra_pedido = $row_pedido['id_obra'];
        $es_pedido_base_arce = ($id_cliente_pedido == $id_cliente_arce && $id_obra_pedido === NULL);
    }
    
    if ($es_pedido_base_arce) {
        error_log("🏢 ValidarCantidadesOrden - Pedido BASE ARCE detectado - Sin validación de verificación");
    }
    
    foreach ($items_orden as $key => $item) {
        // 🔹 OBTENER id_pedido_detalle (puede venir como 'id_detalle' o dentro de $key)
        $id_pedido_detalle = 0;
        
        if (is_numeric($key)) {
            $id_pedido_detalle = isset($item['id_detalle']) ? intval($item['id_detalle']) : 0;
        } else {
            $id_pedido_detalle = isset($item['id_detalle']) ? intval($item['id_detalle']) : 0;
        }
        
        $cantidad_nueva = floatval($item['cantidad']);
        
        error_log("   📦 Validando detalle ID: $id_pedido_detalle | Cantidad nueva: $cantidad_nueva | Key: $key");
        
        if ($id_pedido_detalle <= 0) {
            error_log("   ⚠️ ADVERTENCIA: id_pedido_detalle no válido para key $key");
            continue;
        }
        
        // 🔹 OBTENER DATOS DEL DETALLE
        $sql_detalle = "SELECT cant_pedido_detalle, cant_oc_pedido_detalle, id_producto 
                        FROM pedido_detalle 
                        WHERE id_pedido_detalle = $id_pedido_detalle
                        LIMIT 1";
        $res = mysqli_query($con, $sql_detalle);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row) {
            error_log("   ❌ Detalle ID $id_pedido_detalle no existe");
            $errores[] = "El detalle ID $id_pedido_detalle no existe";
            continue;
        }
        
        $cant_pedida = floatval($row['cant_pedido_detalle']);
        $cant_oc_verificada = floatval($row['cant_oc_pedido_detalle']);
        $id_producto = intval($row['id_producto']);
        
        // ============================================================
        // 🔹 VALIDACIÓN DIFERENCIADA: BASE ARCE vs NORMAL
        // ============================================================
        if ($es_pedido_base_arce) {
            // ═══════════════════════════════════════════════════════
            // PARA BASE ARCE: Validar contra cantidad VERIFICADA (OC)
            // Si no tiene verificada (0), usar cantidad pedida
            // ═══════════════════════════════════════════════════════
            if ($cant_oc_verificada > 0) {
                // Caso reverificado (con bloqueos)
                $cant_verificada = $cant_oc_verificada;
                error_log("   🏢 BASE ARCE (reverificado) - Usando cantidad OC verificada: $cant_verificada");
            } else {
                // Caso sin reverificar (sin bloqueos todavía)
                $cant_verificada = $cant_pedida;
                error_log("   🏢 BASE ARCE (sin reverificar) - Usando cantidad pedida: $cant_verificada");
            }
            
        } else {
            // ═══════════════════════════════════════════════════════
            // PARA PEDIDOS NORMALES: Validar contra cantidad VERIFICADA
            // ═══════════════════════════════════════════════════════
            if ($cant_oc_verificada === null || $cant_oc_verificada <= 0) {
                error_log("   ❌ Detalle ID $id_pedido_detalle no está verificado");
                $errores[] = "El detalle ID $id_pedido_detalle no está verificado";
                continue;
            }
            $cant_verificada = $cant_oc_verificada;
            error_log("   ✅ Cantidad verificada: $cant_verificada | Producto ID: $id_producto");
        }
        
        // 🔹 CALCULAR CANTIDAD YA ORDENADA PARA ESTE DETALLE ESPECÍFICO
        $where_compra = "";
        if ($id_compra_actual) {
            $where_compra = "AND c.id_compra != $id_compra_actual";
        }
        
        $sql_ordenada = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                         FROM compra_detalle cd
                         INNER JOIN compra c ON cd.id_compra = c.id_compra
                         WHERE cd.id_pedido_detalle = $id_pedido_detalle
                         AND c.est_compra != 0
                         AND cd.est_compra_detalle = 1
                         $where_compra";
        
        error_log("   📊 SQL Ordenada: $sql_ordenada");
        
        $res_ord = mysqli_query($con, $sql_ordenada);
        $row_ord = mysqli_fetch_assoc($res_ord);
        $ya_ordenado = floatval($row_ord['total_ordenado']);
        
        error_log("   📈 Ya ordenado (sin esta orden): $ya_ordenado");
        
        // Validación: la cantidad nueva + ya ordenado NO debe exceder lo verificado
        $disponible = $cant_verificada - $ya_ordenado;
        $nuevo_total = $ya_ordenado + $cantidad_nueva;
        
        error_log("   🔢 Disponible: $disponible | Nuevo total: $nuevo_total");
        
        if ($cantidad_nueva > $disponible) {
            error_log("   ⚠️ EXCEDE - Disponible: $disponible | Intentas: $cantidad_nueva");
            
            // 🔹 OBTENER DESCRIPCIÓN DEL PRODUCTO
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            $errores[] = "$descripcion (Detalle $id_pedido_detalle): Cantidad excede lo verificado. Verificado: $cant_verificada, Ya ordenado: $ya_ordenado, Disponible: $disponible, Intentaste ordenar: $cantidad_nueva";
        } else {
            error_log("   ✅ VÁLIDO");
        }
    }
    
    mysqli_close($con);
    return $errores;
}

//-----------------------------------------------------------------------
function ConsultarPedidoAnulado($id_pedido)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
                p.*, 
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(c.nom_cliente, 'N/A') AS nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') AS nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') AS nom_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') AS nom_producto_tipo,
                COALESCE(ar.nom_area, 'N/A') AS nom_centro_costo
            FROM pedido p
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON p.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen a 
                ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON a.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente c 
                ON a.id_cliente = c.id_cliente AND c.act_cliente = 1
            LEFT JOIN ubicacion u 
                ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
            LEFT JOIN {$bd_complemento}.personal pr 
                ON p.id_personal = pr.id_personal AND pr.act_personal = 1
            LEFT JOIN {$bd_complemento}.area ar 
                ON pr.id_area = ar.id_area AND ar.act_area = 1
            LEFT JOIN producto_tipo pt 
                ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
            WHERE p.id_pedido = ?";

    // Preparar consulta
    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido);
    mysqli_stmt_execute($stmt);
    $resc = mysqli_stmt_get_result($stmt);

    $resultado = [];
    while ($rowc = mysqli_fetch_assoc($resc)) {
        $resultado[] = $rowc;
    }

    // Cerrar recursos
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $resultado;
}
//-----------------------------------------------------------------------
// NUEVA FUNCIÓN: Verificar y actualizar estado automáticamente
//-----------------------------------------------------------------------
function VerificarYActualizarEstadoPedido($id_pedido)
{
    include("../_conexion/conexion.php");
    
    // Obtener información del pedido
    $sql_pedido = "SELECT id_almacen, id_ubicacion, id_producto_tipo, est_pedido 
                   FROM pedido 
                   WHERE id_pedido = $id_pedido";
    $res_pedido = mysqli_query($con, $sql_pedido);
    
    if (!$res_pedido) {
        mysqli_close($con);
        return ['error' => 'Query pedido falló: ' . mysqli_error($con)];
    }
    
    $pedido_info = mysqli_fetch_assoc($res_pedido);
    
    if (!$pedido_info) {
        mysqli_close($con);
        return ['error' => 'Pedido no encontrado'];
    }
    
    // Solo actualizar si está en estado PENDIENTE (1)
    if ($pedido_info['est_pedido'] != 1) {
        mysqli_close($con);
        return ['error' => 'Pedido no está pendiente (estado: ' . $pedido_info['est_pedido'] . ')'];
    }
    
    $id_almacen = $pedido_info['id_almacen'];
    $id_ubicacion = $pedido_info['id_ubicacion'];
    $es_auto_orden = ($pedido_info['id_producto_tipo'] == 2);
    
    if ($es_auto_orden) {
        mysqli_close($con);
        return ['error' => 'Es auto-orden (SERVICIO), no se completa automáticamente'];
    }
    
    // Verificar items
    $sql_items = "SELECT 
                    pd.id_pedido_detalle,
                    pd.id_producto,
                    pd.cant_pedido_detalle,
                    p.nom_producto,
                    COALESCE(
                        (SELECT SUM(CASE
                            WHEN mov.tipo_movimiento = 1 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0), 0
                    ) AS stock_disponible
                  FROM pedido_detalle pd
                  INNER JOIN producto p ON pd.id_producto = p.id_producto
                  WHERE pd.id_pedido = $id_pedido
                  AND pd.est_pedido_detalle = 1";
    
    $res_items = mysqli_query($con, $sql_items);
    
    if (!$res_items) {
        mysqli_close($con);
        return ['error' => 'Query items falló: ' . mysqli_error($con)];
    }
    
    $total_items = mysqli_num_rows($res_items);
    
    if ($total_items == 0) {
        mysqli_close($con);
        return ['error' => 'No hay items activos en el pedido'];
    }
    
    $todos_con_stock = true;
    $items_detalle = [];
    
    while ($item = mysqli_fetch_assoc($res_items)) {
        $tiene_stock = ($item['stock_disponible'] >= $item['cant_pedido_detalle']);
        
        $items_detalle[] = [
            'id' => $item['id_pedido_detalle'],
            'producto' => $item['nom_producto'],
            'necesita' => $item['cant_pedido_detalle'],
            'stock' => $item['stock_disponible'],
            'suficiente' => $tiene_stock
        ];
        
        if (!$tiene_stock) {
            $todos_con_stock = false;
        }
    }
    
    if (!$todos_con_stock) {
        mysqli_close($con);
        return [
            'error' => 'No todos los items tienen stock suficiente',
            'items' => $items_detalle
        ];
    }
    
    // CAMBIO CRÍTICO: Actualizar a COMPLETADO (estado 2) cuando tiene todo el stock
    $sql_update = "UPDATE pedido SET est_pedido = 2 WHERE id_pedido = $id_pedido";
    $resultado = mysqli_query($con, $sql_update);
    
    if ($resultado) {
        $filas_afectadas = mysqli_affected_rows($con);
        mysqli_close($con);
        
        if ($filas_afectadas > 0) {
            return true;
        } else {
            return ['error' => 'UPDATE ejecutado pero no afectó filas'];
        }
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return ['error' => 'UPDATE falló: ' . $error];
    }
}
//-----------------------------------------------------------------------
function ObtenerTipoMaterialProducto($id_producto)
{
    include("../_conexion/conexion.php");
    
    $id_producto = intval($id_producto);
    $sql = "SELECT id_material_tipo FROM producto WHERE id_producto = $id_producto";
    $resultado = mysqli_query($con, $sql);
    
    $id_material_tipo = 0;
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $id_material_tipo = intval($row['id_material_tipo']);
    }
    
    mysqli_close($con);
    return $id_material_tipo;
}

/*function ObtenerStockDisponible($id_producto) {
    include("../_conexion/conexion.php");

    $sql = "
        SELECT 
            COALESCE(SUM(
                CASE 
                    WHEN tipo_movimiento = 1 THEN cant_movimiento
                    WHEN tipo_movimiento = 2 AND tipo_orden IN (2) THEN -cant_movimiento
                    ELSE 0
                END
            ),0) 
            - COALESCE(SUM(
                CASE 
                    WHEN tipo_orden = 5 AND est_movimiento = 1 THEN cant_movimiento
                    ELSE 0
                END
            ),0) AS stock_disponible,
            COALESCE(SUM(
                CASE 
                    WHEN tipo_movimiento = 1 THEN cant_movimiento
                    WHEN tipo_movimiento = 2 THEN -cant_movimiento
                    ELSE 0
                END
            ),0) AS stock_almacen
        FROM movimiento
        WHERE id_producto = $id_producto
                AND id_almacen = $id_almacen
                AND id_ubicacion = $id_ubicacion
    ";

    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    return [
        'disponible' => floatval($row['stock_disponible']),
        'almacen' => floatval($row['stock_almacen'])
    ];
}*/

/**
 * Obtener stock (físico, reservado y disponible).
 * Si $id_almacen / $id_ubicacion son null -> hace el cálculo para todos los almacenes/ubicaciones (global).
 *
 * Retorna:
 *  [
 *    'stock_fisico' => float,
 *    'stock_reservado' => float,
 *    'stock_disponible' => float,
 *    'stock_incluye_compromisos' => float // (opcional) suma neta incluyendo movimientos tipo_orden=5 como si fueran reales
 *  ]
 */
//-----------------------------------------------------------------------
function ObtenerStockProducto($id_producto, $id_almacen = null, $id_ubicacion = null, $id_pedido_excluir = null) {
    include("../_conexion/conexion.php");

    $id_producto = intval($id_producto);

    $whereBase = "id_producto = $id_producto AND est_movimiento != 0";

    if (!is_null($id_almacen)) {
        $id_almacen = intval($id_almacen);
        $whereBase .= " AND id_almacen = $id_almacen";
    }
    if (!is_null($id_ubicacion)) {
        $id_ubicacion = intval($id_ubicacion);
        $whereBase .= " AND id_ubicacion = $id_ubicacion";
    }

    // 1) Stock físico: consideramos movimientos que afectan realmente el stock (EXCLUIMOS tipo_orden = 5 que son reservas y tipo_orden = 3 en ingresos)
    $sql_fisico = "SELECT COALESCE(SUM(
                    CASE
                        -- INGRESOS: Devoluciones solo si están confirmadas
                        WHEN tipo_movimiento = 1 THEN
                            CASE
                                WHEN tipo_orden = 3 AND est_movimiento = 1 THEN cant_movimiento
                                WHEN tipo_orden != 3 THEN cant_movimiento
                                ELSE 0
                            END
                        -- SALIDAS: Siempre restan
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento
                        ELSE 0
                    END), 0) AS stock_fisico
                  FROM movimiento
                  WHERE $whereBase
                    AND est_movimiento != 0
                    AND tipo_orden <> 5";

    $res = mysqli_query($con, $sql_fisico);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    $stock_fisico = $row ? floatval($row['stock_fisico']) : 0.0;

    // 2) Stock reservado (compromisos): tipo_orden = 5, tipo_movimiento = 2, est_movimiento != 0
    $whereReservado = "$whereBase AND tipo_orden = 5 AND tipo_movimiento = 2 AND est_movimiento != 0";

     // 👇 Excluir compromisos del pedido actual (si se pasa el ID)
    if (!is_null($id_pedido_excluir)) {
        $id_pedido_excluir = intval($id_pedido_excluir);
        $whereReservado .= " AND id_orden <> $id_pedido_excluir";
    }

    $sql_reservado = "SELECT COALESCE(SUM(cant_movimiento),0) AS stock_reservado
                      FROM movimiento
                      WHERE $whereReservado";

    $res2 = mysqli_query($con, $sql_reservado);
    $row2 = $res2 ? mysqli_fetch_assoc($res2) : null;
    $stock_reservado = $row2 ? floatval($row2['stock_reservado']) : 0.0;

    // 3) (opcional) stock neto si consideráramos las reservas como salidas (para comparar con versiones anteriores)
    $sql_incluye_comp = "SELECT COALESCE(SUM(
                            CASE
                              WHEN tipo_movimiento = 1 AND tipo_orden != 3 THEN cant_movimiento
                              WHEN tipo_movimiento = 2 THEN -cant_movimiento
                              ELSE 0
                            END),0) AS stock_net_incluye_comp
                         FROM movimiento
                         WHERE $whereBase";
    $res3 = mysqli_query($con, $sql_incluye_comp);
    $row3 = $res3 ? mysqli_fetch_assoc($res3) : null;
    $stock_incluye_comp = $row3 ? floatval($row3['stock_net_incluye_comp']) : 0.0;

    mysqli_close($con);

    $stock_disponible = $stock_fisico - $stock_reservado;

    return [
        'stock_fisico' => $stock_fisico,
        'stock_reservado' => $stock_reservado,
        'stock_disponible' => $stock_disponible,
        'stock_incluye_compromisos' => $stock_incluye_comp
    ];
}
//=======================================================================
// FUNCIONES ESPECÍFICAS PARA ÓRDENES DE SERVICIO
//=======================================================================

/**
 * Crear Orden de Servicio (sin validación de stock)
 */
function CrearOrdenServicio($id_pedido, $proveedor, $moneda, $id_personal, 
                            $observacion, $direccion, $plazo_entrega, $porte, 
                            $fecha_orden, $items, 
                            $id_detraccion = null, $archivos_homologacion = [],
                            $id_retencion = null, $id_percepcion = null) 
{
    include("../_conexion/conexion.php");

    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';
    $id_retencion_sql = ($id_retencion && $id_retencion > 0) ? $id_retencion : 'NULL';
    $id_percepcion_sql = ($id_percepcion && $id_percepcion > 0) ? $id_percepcion : 'NULL';

    $sql = "INSERT INTO compra (
                id_pedido, id_proveedor, id_moneda, id_personal, id_personal_aprueba, 
                obs_compra, denv_compra, plaz_compra, port_compra, 
                id_detraccion, id_retencion, id_percepcion,
                fec_compra, est_compra
            ) VALUES (
                $id_pedido, $proveedor, $moneda, $id_personal, NULL, 
                '$observacion', '$direccion', '$plazo_entrega', '$porte', 
                $id_detraccion_sql, $id_retencion_sql, $id_percepcion_sql,
                '$fecha_orden', 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_compra = mysqli_insert_id($con);
        
        foreach ($items as $item) {
            $id_producto = intval($item['id_producto']);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            $id_pedido_detalle = isset($item['id_pedido_detalle']) ? intval($item['id_pedido_detalle']) : intval($item['id_detalle']);
            
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_pedido_detalle]) && !empty($archivos_homologacion[$id_pedido_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_pedido_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_prod_" . $id_producto . "_" . uniqid() . "." . $extension;
                $ruta_destino = "../_archivos/homologaciones/" . $nombre_archivo_hom;
                
                if (!file_exists("../_archivos/homologaciones/")) {
                    mkdir("../_archivos/homologaciones/", 0777, true);
                }
                
                move_uploaded_file($archivo['tmp_name'], $ruta_destino);
            }
            
            $hom_sql = $nombre_archivo_hom ? "'" . mysqli_real_escape_string($con, $nombre_archivo_hom) . "'" : "NULL";
            
            $sql_detalle = "INSERT INTO compra_detalle (
                                id_compra, id_pedido_detalle, id_producto, 
                                cant_compra_detalle, prec_compra_detalle, 
                                igv_compra_detalle, hom_compra_detalle, est_compra_detalle
                            ) VALUES (
                                $id_compra, $id_pedido_detalle, $id_producto, 
                                $cantidad, $precio_unitario, $igv, $hom_sql, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                $id_compra_detalle = mysqli_insert_id($con);
                
                // HEREDAR CENTROS DE COSTO AUTOMÁTICAMENTE
                if ($id_pedido_detalle > 0 && $id_compra_detalle > 0) {
                    error_log("🔄 Heredando centros para compra_detalle $id_compra_detalle desde pedido_detalle $id_pedido_detalle");
                    
                    // Obtener centros del pedido_detalle
                    $sql_centros = "SELECT id_centro_costo 
                                   FROM pedido_detalle_centro_costo 
                                   WHERE id_pedido_detalle = $id_pedido_detalle";
                    
                    $result_centros = mysqli_query($con, $sql_centros);
                    $centros_heredados = array();
                    
                    while ($row = mysqli_fetch_assoc($result_centros)) {
                        $centros_heredados[] = intval($row['id_centro_costo']);
                    }
                    
                    // Guardar centros en compra_detalle
                    if (!empty($centros_heredados)) {
                        require_once("m_compras.php");
                        GuardarCentrosCostoCompraDetalle($con, $id_compra_detalle, $centros_heredados);
                        error_log("✅ Heredados " . count($centros_heredados) . " centros de costo para servicio");
                    } else {
                        error_log("⚠️ Pedido_detalle $id_pedido_detalle no tiene centros de costo asignados");
                    }
                }
                
                // 🔹 CORRECCIÓN: Verificar cierre basado en el detalle específico
                $cant_ordenada_para_este_detalle = ObtenerCantidadYaOrdenadaServicioPorDetalle($id_pedido_detalle);
                
                // Para servicios usamos cant_pedido_detalle (cantidad original)
                $sql_get_original = "SELECT cant_pedido_detalle 
                                    FROM pedido_detalle 
                                    WHERE id_pedido_detalle = $id_pedido_detalle";
                $res_original = mysqli_query($con, $sql_get_original);
                $row_original = mysqli_fetch_assoc($res_original);
                $cant_original = $row_original ? floatval($row_original['cant_pedido_detalle']) : 0;
                
                // Solo cerrar este detalle específico si se completó
                if ($cant_ordenada_para_este_detalle >= $cant_original) {
                    $sql_cerrar = "UPDATE pedido_detalle 
                                SET est_pedido_detalle = 2 
                                WHERE id_pedido_detalle = $id_pedido_detalle";
                    mysqli_query($con, $sql_cerrar);
                }
            } else {
                error_log("ERROR al insertar detalle de servicio: " . mysqli_error($con));
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $err = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $err;
    }
}

/**
 * Obtener cantidad ya ordenada para un detalle específico del pedido (SERVICIOS)
 */
function ObtenerCantidadYaOrdenadaServicioPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    // 🔹 CLAVE: Suma solo las cantidades asociadas a este detalle específico
    $sql = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra
            WHERE cd.id_pedido_detalle = $id_pedido_detalle
            AND c.est_compra != 0
            AND cd.est_compra_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("ERROR en ObtenerCantidadYaOrdenadaServicioPorDetalle: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_ordenado']);
    
    mysqli_close($con);
    return $total;
}

/**
 * Actualizar Orden de Servicio (sin validación de stock)
 */
function ActualizarOrdenServicio($id_compra, $proveedor, $moneda, $observacion, $direccion, 
                                 $plazo_entrega, $porte, $fecha_orden, $items, 
                                 $id_detraccion = null, $archivos_homologacion = [],
                                 $id_retencion = null, $id_percepcion = null) 
{
    include("../_conexion/conexion.php");
    
    error_log("🔧 ActualizarOrdenServicio - ID Compra: $id_compra");
    
    // Obtener id_pedido antes de actualizar
    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = $id_compra";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido['id_pedido'];
    
    error_log("📋 ID Pedido obtenido: $id_pedido");
    
    // 🔹 VALIDACIÓN CORREGIDA PARA SERVICIOS
    $errores = ValidarCantidadesOrdenServicio($id_pedido, $items, $id_compra);
    
    if (!empty($errores)) {
        error_log("❌ Errores de validación en servicio: " . implode(", ", $errores));
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }
    
    error_log("✅ Validación de servicio pasada, continuando...");
    
    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';
    $id_retencion_sql = ($id_retencion && $id_retencion > 0) ? $id_retencion : 'NULL';
    $id_percepcion_sql = ($id_percepcion && $id_percepcion > 0) ? $id_percepcion : 'NULL';
    
    $sql = "UPDATE compra SET 
            id_proveedor = $proveedor, 
            id_moneda = $moneda, 
            obs_compra = '$observacion', 
            denv_compra = '$direccion', 
            plaz_compra = '$plazo_entrega', 
            port_compra = '$porte', 
            id_detraccion = $id_detraccion_sql,
            id_retencion = $id_retencion_sql,
            id_percepcion = $id_percepcion_sql,
            fec_compra = '$fecha_orden' 
            WHERE id_compra = $id_compra";
    
    if (mysqli_query($con, $sql)) {
        // 🔹 RASTREAR DETALLES AFECTADOS (por id_pedido_detalle)
        $detalles_afectados = array();
        
        foreach ($items as $id_compra_detalle => $item) {
            $id_compra_detalle = intval($id_compra_detalle);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            
            // 🔹 OBTENER ID_PEDIDO_DETALLE del compra_detalle
            $sql_detalle_info = "SELECT id_pedido_detalle FROM compra_detalle WHERE id_compra_detalle = $id_compra_detalle";
            $res_detalle_info = mysqli_query($con, $sql_detalle_info);
            $row_detalle_info = mysqli_fetch_assoc($res_detalle_info);
            if ($row_detalle_info) {
                $detalles_afectados[] = intval($row_detalle_info['id_pedido_detalle']);
            }
            
            // Manejar archivo de homologación
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_compra_detalle]) && !empty($archivos_homologacion[$id_compra_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_compra_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_det_" . $id_compra_detalle . "_" . uniqid() . "." . $extension;
                $ruta_destino = "../_archivos/homologaciones/" . $nombre_archivo_hom;
                
                if (!file_exists("../_archivos/homologaciones/")) {
                    mkdir("../_archivos/homologaciones/", 0777, true);
                }
                
                move_uploaded_file($archivo['tmp_name'], $ruta_destino);
            }
            
            $sql_detalle = "UPDATE compra_detalle 
                           SET cant_compra_detalle = $cantidad,
                               prec_compra_detalle = $precio_unitario,
                               igv_compra_detalle = $igv";
            
            if ($nombre_archivo_hom) {
                $sql_detalle .= ", hom_compra_detalle = '" . mysqli_real_escape_string($con, $nombre_archivo_hom) . "'";
            }
            
            $sql_detalle .= " WHERE id_compra_detalle = $id_compra_detalle";
            
            if (!mysqli_query($con, $sql_detalle)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR en detalle: " . $error;
            }
        }
        
        // 🔹 VERIFICAR REAPERTURA POR CADA DETALLE AFECTADO
        foreach ($detalles_afectados as $id_pedido_detalle) {
            VerificarReaperturaItemServicioPorDetalle($id_pedido_detalle);
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}
/**
 * Verificar si un item de servicio debe reabrirse después de editar/anular una orden (POR DETALLE)
 */
function VerificarReaperturaItemServicioPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $sql_original = "SELECT cant_pedido_detalle
                     FROM pedido_detalle 
                     WHERE id_pedido_detalle = $id_pedido_detalle";
    $res = mysqli_query($con, $sql_original);
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        mysqli_close($con);
        return;
    }
    
    $cant_original = floatval($row['cant_pedido_detalle']);
    
    // Obtener cantidad ordenada para este detalle específico
    $total_ordenado = ObtenerCantidadYaOrdenadaServicioPorDetalle($id_pedido_detalle);
    
    // Si la cantidad ordenada es menor a la original, reabrir el item
    if ($total_ordenado < $cant_original) {
        $sql_reabrir = "UPDATE pedido_detalle 
                        SET est_pedido_detalle = 1 
                        WHERE id_pedido_detalle = $id_pedido_detalle";
        mysqli_query($con, $sql_reabrir);
    }
    
    mysqli_close($con);
}
/**
 * Validar cantidades en órdenes de servicio (usa cantidad ORIGINAL, no verificada) - CORREGIDA
 */
function ValidarCantidadesOrdenServicio($id_pedido, $items_orden, $id_compra_actual = null)
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    foreach ($items_orden as $key => $item) {
        $id_producto = intval($item['id_producto']);
        $cantidad_nueva = floatval($item['cantidad']);
        
        // 🔹 Para SERVICIOS: obtener cantidad ORIGINAL del pedido
        $sql_original = "SELECT cant_pedido_detalle
                         FROM pedido_detalle 
                         WHERE id_pedido = $id_pedido 
                         AND id_producto = $id_producto
                         AND est_pedido_detalle IN (1, 2)
                         LIMIT 1";
        $res = mysqli_query($con, $sql_original);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row) {
            $errores[] = "El producto ID $id_producto no existe en el pedido";
            continue;
        }
        
        $cant_original = floatval($row['cant_pedido_detalle']);
        
        // 🔹 CALCULAR CANTIDAD YA ORDENADA (excluyendo la orden actual si se está editando)
        $where_compra = "";
        if ($id_compra_actual) {
            $where_compra = " AND c.id_compra != $id_compra_actual";
        }
        
        $sql_ordenada = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                         FROM compra_detalle cd
                         INNER JOIN compra c ON cd.id_compra = c.id_compra
                         WHERE c.id_pedido = $id_pedido 
                         AND cd.id_producto = $id_producto
                         AND c.est_compra != 0
                         AND cd.est_compra_detalle = 1
                         $where_compra";
        
        $res_ord = mysqli_query($con, $sql_ordenada);
        $row_ord = mysqli_fetch_assoc($res_ord);
        $ya_ordenado = floatval($row_ord['total_ordenado']);
        
        // 🔹 VALIDACIÓN: cantidad nueva + ya ordenado NO debe exceder lo original
        $disponible = $cant_original - $ya_ordenado;
        $nuevo_total = $ya_ordenado + $cantidad_nueva;
        
        if ($cantidad_nueva > $disponible) {
            // Obtener descripción del producto para el mensaje de error
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            $descripcion_corta = $row_desc ? $row_desc['nom_producto'] : "Producto ID $id_producto";
            
            if (strlen($descripcion_corta) > 50) {
                $descripcion_corta = substr($descripcion_corta, 0, 50) . '...';
            }
            
            $tipoItem = $id_compra_actual ? '[EDITANDO]' : '[NUEVO]';
            
            $error = "<strong>{$tipoItem} {$descripcion_corta}:</strong><br>" .
                    "Cantidad ingresada: <strong>{$cantidad_nueva}</strong><br>" .
                    "Original: {$cant_original} | " .
                    "Ya ordenado (otras órdenes): {$ya_ordenado} | " .
                    "<strong style=\"color: #28a745;\">Disponible: {$disponible}</strong>";
            
            $errores[] = $error;
        }
    }
    
    mysqli_close($con);
    return $errores;
}
/**
 *  NUEVA FUNCIÓN: Verificar si un item de servicio debe reabrirse
 */
function VerificarReaperturaItemServicio($id_pedido, $id_producto)
{
    include("../_conexion/conexion.php");
    
    error_log("🔍 VerificarReaperturaItemServicio - Pedido: $id_pedido, Producto: $id_producto");
    
    // Obtener cantidad ORIGINAL del pedido
    $sql_original = "SELECT cant_pedido_detalle, id_pedido_detalle
                     FROM pedido_detalle 
                     WHERE id_pedido = $id_pedido 
                     AND id_producto = $id_producto 
                     AND est_pedido_detalle IN (1, 2)
                     LIMIT 1";
    $res = mysqli_query($con, $sql_original);
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        error_log(" No se encontró el detalle del pedido");
        mysqli_close($con);
        return;
    }
    
    $cant_original = floatval($row['cant_pedido_detalle']);
    $id_pedido_detalle = $row['id_pedido_detalle'];
    
    error_log(" Cantidad original: $cant_original");
    
    //  Calcular cantidad total en órdenes activas (excluyendo anuladas)
    $sql_ordenada = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                     FROM compra_detalle cd
                     INNER JOIN compra c ON cd.id_compra = c.id_compra
                     WHERE c.id_pedido = $id_pedido 
                     AND cd.id_producto = $id_producto
                     AND c.est_compra != 0
                     AND cd.est_compra_detalle = 1";
    $res_ord = mysqli_query($con, $sql_ordenada);
    $row_ord = mysqli_fetch_assoc($res_ord);
    $total_ordenado = floatval($row_ord['total_ordenado']);
    
    error_log(" Total ordenado: $total_ordenado");
    
    //  Si la cantidad ordenada es menor a la original, reabrir el item
    if ($total_ordenado < $cant_original) {
        error_log(" Reabriendo item (ordenado $total_ordenado < original $cant_original)");
        $sql_reabrir = "UPDATE pedido_detalle 
                        SET est_pedido_detalle = 1 
                        WHERE id_pedido_detalle = $id_pedido_detalle";
        mysqli_query($con, $sql_reabrir);
    } else {
        error_log(" Item se mantiene cerrado (ordenado $total_ordenado >= original $cant_original)");
    }
    
    mysqli_close($con);
}
/**
 * Obtener cantidad ya ordenada en servicios (total de todas las órdenes activas)
 */
/**
 * Obtener cantidad ya ordenada en servicios (total de todas las órdenes activas)
 */
function ObtenerCantidadYaOrdenadaServicio($id_pedido, $id_producto) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    $id_producto = intval($id_producto);
    
    $sql = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra
            WHERE c.id_pedido = $id_pedido 
            AND cd.id_producto = $id_producto
            AND c.est_compra != 0
            AND cd.est_compra_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("ERROR en ObtenerCantidadYaOrdenadaServicio: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    $total = floatval($row['total_ordenado']);
    
    mysqli_close($con);
    
    error_log(" [SERVICIO] Cantidad ordenada para producto $id_producto en pedido $id_pedido: $total");
    
    return $total;
}

/**
 * Obtener cantidad pendiente de ordenar en servicios
 */
function ObtenerCantidadPendienteOrdenarServicio($id_pedido, $id_producto) {
    include("../_conexion/conexion.php");
    
    //  Para servicios: usar cantidad ORIGINAL del pedido (no verificada)
    $sql_original = "SELECT cant_pedido_detalle
                     FROM pedido_detalle 
                     WHERE id_pedido = $id_pedido 
                     AND id_producto = $id_producto
                     AND est_pedido_detalle IN (1, 2)
                     LIMIT 1";
    
    $res_original = mysqli_query($con, $sql_original);
    $row_original = mysqli_fetch_assoc($res_original);
    $cant_original = $row_original ? floatval($row_original['cant_pedido_detalle']) : 0;
    
    // Obtener cantidad ya ordenada (excluyendo anuladas)
    $cant_ordenada = ObtenerCantidadYaOrdenadaServicio($id_pedido, $id_producto);
    
    mysqli_close($con);
    
    $cantidad_pendiente = $cant_original - $cant_ordenada;
    return max(0, $cantidad_pendiente);
}

function AprobarPedidoTecnica($id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");

    //  AGREGAR {$bd_complemento} 
    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = '$id_personal'";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        mysqli_close($con);
        error_log("Error: id_personal $id_personal no existe en la tabla personal");
        return false;
    }

    $sql_check = "SELECT p.est_pedido, p.id_personal_aprueba_tecnica
                  FROM pedido p 
                  WHERE p.id_pedido = '$id_pedido'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    // 🔸 Validar existencia y estados no válidos
    if (!$row || $row['est_pedido'] == 0 || $row['est_pedido'] == 5) {
        mysqli_close($con);
        return false;
    }

    // 🔸 Evitar aprobar si ya fue aprobado antes
    if (!empty($row['id_personal_aprueba_tecnica'])) {
        mysqli_close($con);
        return false;
    }

    // 🔸 Registrar aprobación técnica
    $sql_update = "UPDATE pedido 
                   SET id_personal_aprueba_tecnica = '$id_personal'
                   WHERE id_pedido = '$id_pedido'";
    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}
/**
 * Obtener IDs de personal asignado a un detalle de pedido
*/


function ObtenerPersonalDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $personal_ids = array();
    $sql = "SELECT id_personal 
            FROM pedido_detalle_personal 
            WHERE id_pedido_detalle = " . intval($id_pedido_detalle);
    
    $resultado = mysqli_query($con, $sql);
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $personal_ids[] = intval($fila['id_personal']);
    }
    
    mysqli_close($con);
    return $personal_ids;
}
function ObtenerStockFisicoEnUbicacion($id_producto, $id_almacen, $id_ubicacion) {
    include("../_conexion/conexion.php");

    $sql = "SELECT COALESCE(
                SUM(
                    CASE
                        WHEN tipo_movimiento = 1 THEN
                            CASE
                                WHEN tipo_orden = 3 AND est_movimiento = 1 THEN cant_movimiento
                                WHEN tipo_orden != 3 THEN cant_movimiento
                                ELSE 0
                            END
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento
                        ELSE 0
                    END
                ), 0
              ) AS stock_fisico
              FROM movimiento
              WHERE id_producto = ?
              AND id_almacen = ?
              AND id_ubicacion = ?
              AND est_movimiento != 0";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $id_producto, $id_almacen, $id_ubicacion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_close($con);
    return floatval($row['stock_fisico']);
}
/**
 * Guardar personal asignado a un detalle de pedido
 */
function GuardarPersonalDetalle($id_pedido_detalle, $personal_ids) {
    include("../_conexion/conexion.php");
    
    $id_pedido_detalle = intval($id_pedido_detalle);
    
    // Primero eliminar el personal existente
    $sql_delete = "DELETE FROM pedido_detalle_personal 
                   WHERE id_pedido_detalle = $id_pedido_detalle";
    mysqli_query($con, $sql_delete);
    
    // Insertar nuevo personal
    if (!empty($personal_ids) && is_array($personal_ids)) {
        foreach ($personal_ids as $id_personal) {
            $id_personal = intval($id_personal);
            if ($id_personal > 0) {
                $sql_insert = "INSERT INTO pedido_detalle_personal 
                              (id_pedido_detalle, id_personal) 
                              VALUES ($id_pedido_detalle, $id_personal)";
                
                if (!mysqli_query($con, $sql_insert)) {
                    error_log("Error al insertar personal: " . mysqli_error($con));
                }
            }
        }
    }
    
    mysqli_close($con);
}

/**
 * Obtener información detallada del personal asignado
 */
function ObtenerPersonalDetalleCompleto($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $personal = array();
    $sql = "SELECT 
                p.id_personal,
                p.nom_personal,
                p.dni_personal,
                p.cel_personal,
                p.email_personal,
                a.nom_area,
                c.nom_cargo
            FROM pedido_detalle_personal pdp
            INNER JOIN {$bd_complemento}.personal p ON pdp.id_personal = p.id_personal
            LEFT JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
            LEFT JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
            WHERE pdp.id_pedido_detalle = " . intval($id_pedido_detalle) . "
            AND p.act_personal = 1
            ORDER BY p.nom_personal ASC";
    
    $resultado = mysqli_query($con, $sql);
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $personal[] = $fila;
    }
    
    mysqli_close($con);
    return $personal;
}
/**
 * Obtener stock disponible en una ubicación específica
 */
function ObtenerStockEnUbicacion($id_producto, $id_almacen, $id_ubicacion) {
    include("../_conexion/conexion.php");

    $sql = "SELECT COALESCE(
                SUM(
                    CASE
                        WHEN tipo_movimiento = 1 THEN
                            CASE
                                WHEN tipo_orden = 3 AND est_movimiento = 1 THEN cant_movimiento
                                WHEN tipo_orden != 3 THEN cant_movimiento
                                ELSE 0
                            END
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento
                        ELSE 0
                    END
                ), 0
              ) AS stock
              FROM movimiento
              WHERE id_producto = ?
              AND id_almacen = ?
              AND id_ubicacion = ?
              AND est_movimiento != 0";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $id_producto, $id_almacen, $id_ubicacion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return floatval($row['stock']);
}

/**
 * Obtener otras ubicaciones con stock disponible (VERSIÓN FINAL CORREGIDA)
 * 
 * REGLAS:
 * 1. Mismo cliente Y misma obra Y almacén diferente
 * 2. Cliente ARCE con obra NULL (BASE ARCE) puede abastecer a cualquier almacén ARCE
 */
function ObtenerOtrasUbicacionesConStock($id_producto, $id_almacen, $id_ubicacion_excluir) {
    include("../_conexion/conexion.php");
    
    // Obtener información del almacén destino (pedido)
    $sql_destino = "SELECT id_obra, id_cliente FROM almacen WHERE id_almacen = $id_almacen LIMIT 1";
    $res_destino = mysqli_query($con, $sql_destino);
    $row_destino = mysqli_fetch_assoc($res_destino);
    
    if (!$row_destino) {
        mysqli_close($con);
        return [];
    }
    
    $id_obra_destino = intval($row_destino['id_obra']);
    $id_cliente_destino = intval($row_destino['id_cliente']);

    $sql = "SELECT 
                u.id_ubicacion,
                u.nom_ubicacion,
                a.nom_almacen,
                a.id_almacen,
                a.id_cliente,
                a.id_obra,
                c.nom_cliente,
                o.nom_subestacion as nom_obra,
                CASE 
                    WHEN a.id_almacen = $id_almacen THEN 'Mismo Almacén'
                    WHEN a.id_obra IS NULL AND a.id_cliente = $id_cliente_arce THEN 'BASE ARCE'
                    ELSE 'Mismo Cliente - Misma Obra'
                END as tipo_origen,
                CASE 
                    WHEN a.id_cliente = $id_cliente_destino THEN 0
                    ELSE 1
                END as es_otro_cliente,
                COALESCE(
                    SUM(
                        CASE
                            WHEN m.tipo_movimiento = 1 THEN
                                CASE
                                    WHEN m.tipo_orden = 3 AND m.est_movimiento = 1 THEN m.cant_movimiento
                                    WHEN m.tipo_orden != 3 THEN m.cant_movimiento
                                    ELSE 0
                                END
                            WHEN m.tipo_movimiento = 2 THEN -m.cant_movimiento
                            ELSE 0
                        END
                    ), 0
                ) AS stock
              FROM movimiento m
              INNER JOIN ubicacion u ON m.id_ubicacion = u.id_ubicacion
              INNER JOIN almacen a ON m.id_almacen = a.id_almacen
              LEFT JOIN {$bd_complemento}.cliente c ON a.id_cliente = c.id_cliente
              LEFT JOIN {$bd_complemento}.subestacion o ON a.id_obra = o.id_subestacion
              WHERE m.id_producto = $id_producto
              AND m.est_movimiento != 0
              AND NOT (a.id_almacen = $id_almacen AND u.id_ubicacion = $id_ubicacion_excluir)
              AND (
                  -- ═══════════════════════════════════════════════════════
                  -- CASO 1: Mismo cliente Y misma obra, almacén diferente
                  -- ═══════════════════════════════════════════════════════
                  (
                      a.id_cliente = $id_cliente_destino
                      AND a.id_obra = $id_obra_destino
                  )
                  
                  OR
                  
                  -- ═══════════════════════════════════════════════════════
                  -- CASO 2: Cliente ARCE con obra NULL (BASE ARCE)
                  --         Puede abastecer a cualquier almacén ARCE
                  -- ═══════════════════════════════════════════════════════
                  (
                      a.id_cliente = $id_cliente_arce
                      AND a.id_obra IS NULL
                      AND $id_cliente_destino = $id_cliente_arce
                  )
              )
              GROUP BY u.id_ubicacion, u.nom_ubicacion, a.nom_almacen, a.id_almacen, 
                       a.id_cliente, a.id_obra, c.nom_cliente, o.nom_subestacion
              HAVING stock > 0
              ORDER BY 
                  -- Prioridad 1: Mismo almacén primero
                  CASE WHEN a.id_almacen = $id_almacen THEN 0 ELSE 1 END,
                  -- Prioridad 2: Mayor stock primero
                  stock DESC,
                  -- Prioridad 3: BASE ARCE (obra NULL) antes que otros
                  CASE WHEN a.id_obra IS NULL THEN 0 ELSE 1 END";

    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ObtenerOtrasUbicacionesConStock: " . mysqli_error($con));
        mysqli_close($con);
        return [];
    }

    $ubicaciones = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $ubicaciones[] = $row;
    }

    mysqli_close($con);
    return $ubicaciones;
}
/**
 * Obtener stock total en todas las ubicaciones del almacén
 */
function ObtenerStockTotalAlmacen($id_producto, $id_almacen) {
    include("../_conexion/conexion.php");

    $sql = "SELECT COALESCE(
                SUM(
                    CASE
                        WHEN tipo_movimiento = 1 THEN
                            CASE
                                WHEN tipo_orden = 3 AND est_movimiento = 1 THEN cant_movimiento
                                WHEN tipo_orden != 3 THEN cant_movimiento
                                ELSE 0
                            END
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento
                        ELSE 0
                    END
               ), 0) AS stock
              FROM movimiento
              WHERE id_producto = ?
              AND id_almacen = ?
              AND est_movimiento != 0";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_producto, $id_almacen);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return floatval($row['stock']);
}

/**
 * Obtener faltante en ubicación destino
 * Retorna: cantidad faltante o 0 si hay suficiente
 */
function ObtenerFaltanteEnUbicacion($id_producto, $id_almacen, $id_ubicacion, $cantidad_requerida) {
    $stock = ObtenerStockEnUbicacion($id_producto, $id_almacen, $id_ubicacion);
    return max(0, $cantidad_requerida - $stock);
}

/**DE SALIDAS
 * 
 * Consultar Salida por ID
 */
function ConsultarSalidaPorId($id_salida) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                s.*,
                ao.nom_almacen as nom_almacen_origen,
                uo.nom_ubicacion as nom_ubicacion_origen,
                ad.nom_almacen as nom_almacen_destino,
                ud.nom_ubicacion as nom_ubicacion_destino,
                p.nom_personal,
                CASE 
                    WHEN s.est_salida = 0 THEN 'Anulada'
                    WHEN s.est_salida = 1 THEN 'Pendiente'
                    WHEN s.est_salida = 2 THEN 'Recepcionada'
                    WHEN s.est_salida = 3 THEN 'Aprobada'
                    WHEN s.est_salida = 4 THEN 'Denegada'
                    ELSE 'Desconocido'
                END as estado_texto
            FROM salida s
            LEFT JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
            LEFT JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
            LEFT JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
            LEFT JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
            LEFT JOIN {$bd_complemento}.personal p ON s.id_personal = p.id_personal
            WHERE s.id_salida = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_salida);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $data;
}

/**
 * OBTENER UBICACIONES BLOQUEADAS (SIN CAMBIOS)
 */
function ObtenerUbicacionesBloqueadasPorDenegacion($id_producto, $id_pedido_detalle = null)
{
    include("../_conexion/conexion.php");
    
    $ubicaciones_bloqueadas = [];
    
    if ($id_pedido_detalle === null || $id_pedido_detalle <= 0) {
        mysqli_close($con);
        return $ubicaciones_bloqueadas;
    }
    
    $sql_bloqueadas = "
        SELECT DISTINCT 
            s.id_almacen_origen, 
            s.id_ubicacion_origen,
            s.id_salida,
            s.fec_deniega_salida as fecha_bloqueo,
            a.nom_almacen,
            u.nom_ubicacion,
            p_deniega.nom_personal as personal_que_denego
        FROM salida s
        INNER JOIN salida_detalle sd ON s.id_salida = sd.id_salida
        INNER JOIN almacen a ON s.id_almacen_origen = a.id_almacen
        INNER JOIN ubicacion u ON s.id_ubicacion_origen = u.id_ubicacion
        LEFT JOIN {$bd_complemento}.personal p_deniega ON s.id_personal_deniega_salida = p_deniega.id_personal
        WHERE sd.id_producto = $id_producto
          AND sd.id_pedido_detalle = $id_pedido_detalle
          AND s.est_salida = 4
        ORDER BY s.fec_deniega_salida DESC
    ";
    
    $res_bloqueadas = mysqli_query($con, $sql_bloqueadas);
    
    if ($res_bloqueadas) {
        while ($row_bloq = mysqli_fetch_assoc($res_bloqueadas)) {
            $key = $row_bloq['id_almacen_origen'] . '_' . $row_bloq['id_ubicacion_origen'];
            $ubicaciones_bloqueadas[$key] = [
                'id_almacen' => $row_bloq['id_almacen_origen'],
                'id_ubicacion' => $row_bloq['id_ubicacion_origen'],
                'nom_almacen' => $row_bloq['nom_almacen'],
                'nom_ubicacion' => $row_bloq['nom_ubicacion'],
                'id_salida_denegada' => $row_bloq['id_salida'],
                'fecha_bloqueo' => $row_bloq['fecha_bloqueo'],
                'personal_que_denego' => $row_bloq['personal_que_denego']
            ];
            
            error_log("🚫 Ubicación BLOQUEADA: {$row_bloq['nom_almacen']} - {$row_bloq['nom_ubicacion']} (Salida #{$row_bloq['id_salida']})");
        }
    }
    
    mysqli_close($con);
    
    error_log("🚫 Total ubicaciones BLOQUEADAS para producto $id_producto, detalle $id_pedido_detalle: " . count($ubicaciones_bloqueadas));
    
    return $ubicaciones_bloqueadas;
}

/**
 * OBTENER OTRAS UBICACIONES CON STOCK (CON LOGGING DETALLADO)
 */
function ObtenerOtrasUbicacionesConStockConBloqueo($id_producto, $id_almacen_destino, $id_ubicacion_destino, $id_pedido_detalle = null)
{
    include("../_conexion/conexion.php");
    
    $ubicaciones = [];
    
    // ============================================
    // PASO 1: Obtener ubicaciones BLOQUEADAS
    // ============================================
    $ubicaciones_bloqueadas = [];
    
    if ($id_pedido_detalle !== null && $id_pedido_detalle > 0) {
        $ubicaciones_bloqueadas_data = ObtenerUbicacionesBloqueadasPorDenegacion($id_producto, $id_pedido_detalle);
        
        foreach ($ubicaciones_bloqueadas_data as $key => $data) {
            $ubicaciones_bloqueadas[$key] = true;
        }
    }
    
    error_log("🔍 Buscando ubicaciones con stock para producto $id_producto (excluyendo " . count($ubicaciones_bloqueadas) . " bloqueadas)");
    
    // ============================================
    // PASO 2: Obtener todas las ubicaciones con stock
    // ============================================
    $ubicaciones_desde_funcion_antigua = ObtenerOtrasUbicacionesConStock(
        $id_producto, 
        $id_almacen_destino, 
        $id_ubicacion_destino
    );
    
    error_log("📊 DEBUG: ObtenerOtrasUbicacionesConStock() retornó " . count($ubicaciones_desde_funcion_antigua) . " ubicaciones");
    
    $total_encontradas = 0;
    $total_bloqueadas = 0;
    
    // ============================================
    // LOGGING DETALLADO DE CADA UBICACIÓN
    // ============================================
    foreach ($ubicaciones_desde_funcion_antigua as $ub) {
        $total_encontradas++;
        
        $key_ubicacion = $ub['id_almacen'] . '_' . $ub['id_ubicacion'];
        
        // LOG DETALLADO
        error_log("   📦 [$total_encontradas] Almacén: {$ub['id_almacen']} | Ubicación: {$ub['id_ubicacion']} | Key: $key_ubicacion | Nombre: {$ub['nom_almacen']} - {$ub['nom_ubicacion']} | Stock: {$ub['stock']}");
        
        // Verificar si está bloqueada
        $esta_bloqueada = isset($ubicaciones_bloqueadas[$key_ubicacion]);
        
        if ($esta_bloqueada) {
            $total_bloqueadas++;
            error_log("      🚫 Ubicación EXCLUIDA (bloqueada): {$ub['nom_almacen']} - {$ub['nom_ubicacion']}");
            continue;
        }
        
        // ✅ AGREGAR UBICACIÓN DISPONIBLE
        error_log("      ✅ Ubicación DISPONIBLE: {$ub['nom_almacen']} - {$ub['nom_ubicacion']}");
        $ubicaciones[] = $ub;
    }
    
    mysqli_close($con);
    
    error_log("✅ Resultado: {$total_encontradas} encontradas, {$total_bloqueadas} bloqueadas, " . count($ubicaciones) . " disponibles");
    
    return $ubicaciones;
}

/**
 * CONVERTIR OS A OC (SIN CAMBIOS)
 */
function ConvertirCantidadOSaOC($id_pedido_detalle, $cantidad)
{
    include("../_conexion/conexion.php");
    
    mysqli_begin_transaction($con);
    
    try {
        $id_pedido_detalle = intval($id_pedido_detalle);
        $cantidad = floatval($cantidad);
        
        if ($cantidad <= 0) {
            throw new Exception("Cantidad inválida para conversión");
        }
        
        error_log("🔄 Convirtiendo $cantidad de OS a OC para detalle $id_pedido_detalle");
        
        $sql_get = "SELECT cant_os_pedido_detalle, cant_oc_pedido_detalle 
                    FROM pedido_detalle 
                    WHERE id_pedido_detalle = $id_pedido_detalle";
        
        $res = mysqli_query($con, $sql_get);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row) {
            throw new Exception("Detalle de pedido no encontrado");
        }
        
        $cant_os_actual = floatval($row['cant_os_pedido_detalle']);
        $cant_oc_actual = floatval($row['cant_oc_pedido_detalle']);
        
        if ($cantidad > $cant_os_actual) {
            throw new Exception("No hay suficiente cantidad OS para convertir. Disponible: $cant_os_actual, Solicitado: $cantidad");
        }
        
        $nueva_cant_os = $cant_os_actual - $cantidad;
        $nueva_cant_oc = $cant_oc_actual + $cantidad;
        
        $sql_update = "UPDATE pedido_detalle 
                       SET cant_os_pedido_detalle = $nueva_cant_os,
                           cant_oc_pedido_detalle = $nueva_cant_oc
                       WHERE id_pedido_detalle = $id_pedido_detalle";
        
        if (!mysqli_query($con, $sql_update)) {
            throw new Exception("Error al actualizar cantidades: " . mysqli_error($con));
        }
        
        error_log("✅ Conversión exitosa: OS {$cant_os_actual} → {$nueva_cant_os} | OC {$cant_oc_actual} → {$nueva_cant_oc}");
        
        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'success' => true,
            'message' => "✅ Cantidad convertida exitosamente de OS a OC",
            'cant_os_nueva' => $nueva_cant_os,
            'cant_oc_nueva' => $nueva_cant_oc,
            'cantidad_convertida' => $cantidad
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        
        error_log("❌ Error en ConvertirCantidadOSaOC: " . $e->getMessage());
        
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * HAY MÁS UBICACIONES DISPONIBLES (SIN CAMBIOS)
 */
function HayMasUbicacionesDisponibles($id_producto, $id_almacen_destino, $id_ubicacion_destino, $id_pedido_detalle)
{
    $ubicaciones = ObtenerOtrasUbicacionesConStockConBloqueo(
        $id_producto, 
        $id_almacen_destino, 
        $id_ubicacion_destino, 
        $id_pedido_detalle
    );
    
    $hay_disponibles = (count($ubicaciones) > 0);
    
    error_log("🔍 ¿Hay ubicaciones disponibles? " . ($hay_disponibles ? "SÍ (" . count($ubicaciones) . ")" : "NO"));
    
    return $hay_disponibles;
}

/**
 * ============================================
 * ACTUALIZACIÓN DE ESTADO DE PEDIDO
 * ============================================
 * 
 * REGLAS:
 * - OC: Completada cuando est_pedido = 4 (Ingresado)
 * - OS: Completada cuando suma de salidas = cant_os_pedido_detalle
 * 
 * ESTADOS:
 * 1 = Pendiente
 * 2 = Atendido
 * 4 = Ingresado (OC completa e ingresado el stock)
 */

/**
 * Verifica si el pedido requiere OC
 */
function PedidoRequiereOC($id_pedido) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT SUM(cant_oc_pedido_detalle) as total_oc
            FROM pedido_detalle 
            WHERE id_pedido = $id_pedido";
    
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $total = floatval($fila['total_oc']);
        mysqli_close($con);
        return ($total > 0);
    }
    
    mysqli_close($con);
    return false;
}

/**
 * Verifica si el pedido requiere OS
 */
function PedidoRequiereOS($id_pedido) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT SUM(cant_os_pedido_detalle) as total_os
            FROM pedido_detalle 
            WHERE id_pedido = $id_pedido";
    
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $total = floatval($fila['total_os']);
        mysqli_close($con);
        return ($total > 0);
    }
    
    mysqli_close($con);
    return false;
}

/**
 * Verifica si la OC está completada
 * CRITERIO: est_pedido = 4 (Ingresado)
 */
function OCCompletada($id_pedido) {
    include("../_conexion/conexion.php");
    
    // Primero verificar si requiere OC
    if (!PedidoRequiereOC($id_pedido)) {
        mysqli_close($con);
        return true; // Si no requiere OC, se considera completado
    }
    
    // Verificar si el estado del pedido es 4 (Ingresado)
    $sql = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $estado = intval($fila['est_pedido']);
        mysqli_close($con);
        
        // OC está completada si el estado del pedido es 4
        return ($estado == 4);
    }
    
    mysqli_close($con);
    return false;
}

/**
 * Verifica si todas las OS están completadas
 * CRITERIO: Suma de cant_salida_detalle = cant_os_pedido_detalle

 */
function TodasOSCompletadas($id_pedido) {
    include("../_conexion/conexion.php");
    
    // Primero verificar si requiere OS
    if (!PedidoRequiereOS($id_pedido)) {
        mysqli_close($con);
        return true; // Si no requiere OS, se considera completado
    }
    
    // Obtener total solicitado en OS
    $sql = "SELECT 
                COALESCE(SUM(pd.cant_os_pedido_detalle), 0) as total_solicitado,
                COALESCE(
                    (SELECT SUM(sd.cant_salida_detalle) 
                     FROM salida s 
                     INNER JOIN salida_detalle sd ON s.id_salida = sd.id_salida 
                     WHERE s.id_pedido = $id_pedido
                     AND s.est_salida = 1 
                     AND sd.est_salida_detalle = 1
                    ), 0
                ) as total_entregado
            FROM pedido_detalle pd
            WHERE pd.id_pedido = $id_pedido 
            AND pd.est_pedido_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $total_solicitado = floatval($fila['total_solicitado']);
        $total_entregado = floatval($fila['total_entregado']);
        
        mysqli_close($con);
        
        // Verificar si se entregó el 100%
        return ($total_entregado >= $total_solicitado && $total_solicitado > 0);
    }
    
    mysqli_close($con);
    return false;
}

/**
 * Obtiene el estado actual del pedido
 */
function ObtenerEstadoPedido($id_pedido) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return intval($fila['est_pedido']);
    }
    
    mysqli_close($con);
    return null;
}

/**
 * Cambia el estado del pedido 
 */
function CambiarEstadoPedido($id_pedido, $nuevo_estado) {
    include("../_conexion/conexion.php");
    
    $sql = "UPDATE pedido SET est_pedido = $nuevo_estado WHERE id_pedido = $id_pedido";
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado) {
        mysqli_close($con);
        return true;
    }
    
    mysqli_close($con);
    return false;
}

/**
 * ============================================
 * FUNCIÓN PRINCIPAL
 * ============================================
 * Actualiza el estado del pedido según las casuísticas
 * 
 * CASUISTICAS:
 * 1. Solo OS: Atendido cuando suma salidas = cant_os_pedido_detalle
 * 2. Solo OC: Atendido cuando est_pedido = 4
 * 3. OS + OC: Atendido cuando OS al 100% Y est_pedido = 4
 * 
 * REVERSIÓN:
 * - Si se edita/anula salida OS y ya no cumple: vuelve a Pendiente

 */
function ActualizarEstadoPedido($id_pedido) {
    $estado_actual = ObtenerEstadoPedido($id_pedido);
    
    if ($estado_actual === null) {
        return false;
    }
    
    // Verificar qué REQUIERE el pedido
    $requiere_oc = PedidoRequiereOC($id_pedido);
    $requiere_os = PedidoRequiereOS($id_pedido);
    
    // Verificar si están COMPLETADAS
    $oc_completada = OCCompletada($id_pedido);       // ← Evalúa est_pedido = 4
    $os_completadas = TodasOSCompletadas($id_pedido); // ← Evalúa suma cantidades
    
    $nuevo_estado = $estado_actual;
    
    // ============================================
    // ESCENARIO 1: Solo requiere OS
    // ============================================
    if ($requiere_os && !$requiere_oc) {
        if ($os_completadas) {
            $nuevo_estado = 2; // Atendido
        } else {
            // Si estaba Atendido y ahora no cumple, volver a Pendiente
            if ($estado_actual == 2) {
                $nuevo_estado = 1; // Pendiente
            }
        }
    }
    
    // ============================================
    // ESCENARIO 2: Solo requiere OC
    // ============================================
    elseif ($requiere_oc && !$requiere_os) {
        if ($oc_completada) {
            $nuevo_estado = 2; // Atendido
        } else {
            // Si estaba Atendido y ahora no cumple, volver a Pendiente
            if ($estado_actual == 2) {
                $nuevo_estado = 1; // Pendiente
            }
        }
    }
    
    // ============================================
    // ESCENARIO 3: Requiere ambos (OS + OC)
    // ============================================
    elseif ($requiere_os && $requiere_oc) {
        if ($os_completadas && $oc_completada) {
            $nuevo_estado = 2; // Atendido
        } else {
            // 🔹 Si estaba Atendido y ahora no cumple ambas condiciones
            if ($estado_actual == 2) {
                // 🔍 Verificar si todo está INGRESADO
                $todo_ingresado = TodoElPedidoEstaIngresado($id_pedido);
                
                if ($todo_ingresado) {
                    $nuevo_estado = 4; // Completado (todo ingresado pero falta OS)
                } else {
                    $nuevo_estado = 1; // Pendiente (aún falta ingresar)
                }
            }
        }
    }
    
    // Actualizar solo si cambió el estado
    if ($nuevo_estado != $estado_actual) {
        return CambiarEstadoPedido($id_pedido, $nuevo_estado);
    }
    
    return true;
}

function TodoElPedidoEstaIngresado($id_pedido) {
    include("../_conexion/conexion.php");
    
    // Verificar si se completó el 100% del ingreso
    $sql_verificar = "
        SELECT 
            (SELECT SUM(ppd2.cant_oc_pedido_detalle) 
            FROM pedido_detalle ppd2 
            WHERE ppd2.id_pedido = $id_pedido
            AND ppd2.est_pedido_detalle = 2 
            AND ppd2.cant_oc_pedido_detalle > 0
            ) as total_requerido,
            
            COALESCE(
                (SELECT SUM(id2.cant_ingreso_detalle) 
                FROM compra c2 
                INNER JOIN ingreso i2 ON c2.id_compra = i2.id_compra 
                INNER JOIN ingreso_detalle id2 ON i2.id_ingreso = id2.id_ingreso 
                WHERE c2.id_pedido = $id_pedido
                AND i2.est_ingreso = 1 
                AND id2.est_ingreso_detalle = 1
                ), 0
            ) as total_ingresado
    ";
    
    $res_verificar = mysqli_query($con, $sql_verificar);
    
    if ($res_verificar && mysqli_num_rows($res_verificar) > 0) {
        $row = mysqli_fetch_assoc($res_verificar);
        $total_requerido = floatval($row['total_requerido']);
        $total_ingresado = floatval($row['total_ingresado']);
        
        // Retornar true solo si está completo al 100%
        if ($total_ingresado >= $total_requerido && $total_requerido > 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Valida que haya stock físico real disponible para los materiales
 * SE USA ANTES DE CONFIRMAR CUALQUIER OPERACIÓN DE SALIDA
 * 
 * @param array $materiales - Array con id_producto, cantidad, id_pedido_detalle
 * @param int $id_almacen_origen
 * @param int $id_ubicacion_origen  
 * @param int|null $id_pedido - Para excluir reservas del mismo pedido
 * @param int|null $id_salida_actual - Para excluir la salida actual en ediciones
 * @return array - Array de errores (vacío si todo OK)
 */
function ValidarInventarioDisponibleParaSalida($materiales, $id_almacen_origen, $id_ubicacion_origen, $id_pedido = null, $id_salida_actual = null) {
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    foreach ($materiales as $material) {
        $id_producto = intval($material['id_producto']);
        $cantidad_solicitada = floatval($material['cantidad']);
        
        //  OBTENER DESCRIPCIÓN CORRECTA
        $descripcion = '';
        if (isset($material['descripcion']) && !empty(trim($material['descripcion']))) {
            $descripcion = trim($material['descripcion']);
        } else {
            //  BUSCAR EN LA BD
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            if ($res_desc && $row_desc = mysqli_fetch_assoc($res_desc)) {
                $descripcion = $row_desc['nom_producto'];
            } else {
                $descripcion = "Producto ID $id_producto";
            }
        }
        
        //  CALCULAR STOCK FÍSICO REAL (LÓGICA CORREGIDA)
        $sql_stock = "SELECT COALESCE(
                        SUM(
                            CASE
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
                            END
                        ), 0) AS stock_real
                      FROM movimiento mov
                      WHERE mov.id_producto = $id_producto
                        AND mov.id_almacen = $id_almacen_origen
                        AND mov.id_ubicacion = $id_ubicacion_origen
                        AND mov.est_movimiento != 0"; // ⬅ Incluye activos (1) y pendientes (2)
        
        //  CRÍTICO: Excluir movimientos de la salida actual si estamos editando
        if ($id_salida_actual !== null && $id_salida_actual > 0) {
            $sql_stock .= " AND NOT (mov.tipo_orden = 2 AND mov.id_orden = " . intval($id_salida_actual) . ")";
        }
        
        $res = mysqli_query($con, $sql_stock);
        
        if (!$res) {
            error_log(" ERROR SQL en ValidarInventarioDisponibleParaSalida: " . mysqli_error($con));
            $errores[] = "Error al consultar stock para {$descripcion}";
            continue;
        }
        
        $row = mysqli_fetch_assoc($res);
        $stock_real = floatval($row['stock_real']);
        
        error_log("    Validación stock - Producto: {$descripcion} | Disponible: {$stock_real} | Solicitado: {$cantidad_solicitada}");
        
        // 🔹 VALIDAR DISPONIBILIDAD
        if ($cantidad_solicitada > $stock_real) {
            //  Formato específico para detectar error de stock
            if ($stock_real <= 0) {
                $errores[] = sprintf(
                    "El producto '%s' no tiene stock disponible en la ubicación origen seleccionada. Solicitado: %.2f unidades.",
                    $descripcion,
                    $cantidad_solicitada
                );
            } else {
                $errores[] = sprintf(
                    "Stock insuficiente para '%s'. Disponible: %.2f, Solicitado: %.2f (Faltante: %.2f)",
                    $descripcion,
                    $stock_real,
                    $cantidad_solicitada,
                    $cantidad_solicitada - $stock_real
                );
            }
        } else {
            error_log("    Stock suficiente");
        }
    }
    
    mysqli_close($con);
    return $errores;
}

function ObtenerCantidadYaEntregadaOS($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_entregado
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
            AND s.est_salida = 1  -- Solo salidas activas (no anuladas)
            AND sd.est_salida_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return floatval($row['total_entregado']);
}
function ReverificarItemAutomaticamente($id_pedido_detalle) {
    include("../_conexion/conexion.php");

    $id_pedido_detalle = intval($id_pedido_detalle);

    error_log("🔄 ReverificarItemAutomaticamente - ID: $id_pedido_detalle");

    // ============================================================
    // PASO 1: Obtener información del detalle y del pedido
    // ============================================================
    $sql_detalle = "SELECT 
                        pd.id_pedido_detalle,
                        pd.id_pedido,
                        pd.id_producto,
                        pd.cant_pedido_detalle,
                        pd.cant_oc_pedido_detalle AS cant_oc_anterior,
                        pd.cant_os_pedido_detalle AS cant_os_anterior,
                        pd.est_pedido_detalle,
                        p.id_almacen,
                        p.id_ubicacion,
                        p.est_pedido,
                        a.id_cliente,
                        a.id_obra
                    FROM pedido_detalle pd
                    INNER JOIN pedido p ON pd.id_pedido = p.id_pedido
                    INNER JOIN almacen a ON p.id_almacen = a.id_almacen
                    WHERE pd.id_pedido_detalle = $id_pedido_detalle";

    $res = mysqli_query($con, $sql_detalle);
    $detalle = mysqli_fetch_assoc($res);

    if (!$detalle) {
        error_log("❌ No se encontró el detalle $id_pedido_detalle");
        mysqli_close($con);
        return false;
    }

    // ============================================================
    // 🔹 VALIDACIÓN NUEVA: NO reverificar pedidos BASE ARCE
    // ============================================================
    $id_cliente = intval($detalle['id_cliente']);
    $id_obra = $detalle['id_obra']; // Puede ser NULL

    if ($id_cliente == $id_cliente_arce && $id_obra === NULL) {
        error_log(" Pedido BASE ARCE detectado - NO se reverifica (cliente: $id_cliente, obra: NULL)");
        mysqli_close($con);
        return false;
    }

    $estado_item = intval($detalle['est_pedido_detalle']);
    $estado_pedido = intval($detalle['est_pedido']);

    // Variables base
    $id_pedido = intval($detalle['id_pedido']);
    $id_producto = intval($detalle['id_producto']);
    $cantidad_pedida = floatval($detalle['cant_pedido_detalle']);
    $id_almacen = intval($detalle['id_almacen']);
    $id_ubicacion = intval($detalle['id_ubicacion']);

    $cant_oc_anterior = floatval($detalle['cant_oc_anterior']);
    $cant_os_anterior = floatval($detalle['cant_os_anterior']);

    // ============================================================
    //  VALIDACIÓN 1: NO reverificar si está CERRADO MANUALMENTE
    // ============================================================
    if ($estado_item == 2) {
        error_log("🔒 Item cerrado manualmente (est_pedido_detalle=2) - NO se reverifica");
        mysqli_close($con);
        return false;
    }

    // ============================================================
    //  VALIDACIÓN 2: NO reverificar si ya está FINALIZADO (todo ordenado)
    // ============================================================
    $cantidad_verificada_total = $cant_oc_anterior + $cant_os_anterior;
    
    if ($cantidad_verificada_total > 0) {
        // Obtener cantidades ya ordenadas
        $cantidad_ya_ordenada_oc = ObtenerCantidadYaOrdenadaOCPorDetalle($id_pedido_detalle);
        $cantidad_ya_ordenada_os = ObtenerCantidadYaOrdenadaOSPorDetalle($id_pedido_detalle);
        $cantidad_ya_ordenada_total = $cantidad_ya_ordenada_oc + $cantidad_ya_ordenada_os;
        
        // Si ya ordenó todo lo verificado → FINALIZADO, NO reverificar
        if ($cantidad_ya_ordenada_total >= $cantidad_verificada_total) {
            error_log("✅ Item FINALIZADO (Ordenado: $cantidad_ya_ordenada_total / Verificado: $cantidad_verificada_total) - NO se reverifica");
            mysqli_close($con);
            return false;
        }
    }

    // ============================================================
    //  VALIDACIÓN 3: NO reverificar si el pedido completo está finalizado
    // ============================================================
    if ($estado_pedido == 5) {
        error_log("⏭️ Pedido finalizado (est_pedido=5) - NO se reverifica");
        mysqli_close($con);
        return true;
    }

    // ============================================================
    // PASO 2: Obtener stock disponible en ubicación DESTINO
    // ============================================================
    $stock_data = ObtenerStockProducto($id_producto, $id_almacen, $id_ubicacion, $id_pedido);
    $stock_destino = floatval($stock_data['stock_fisico']);

    error_log("📦 Stock destino: $stock_destino | Cantidad pedida: $cantidad_pedida");

    // ============================================================
    // PASO 3: Obtener stock en OTRAS ubicaciones
    // ============================================================
    $otras = ObtenerOtrasUbicacionesConStockConBloqueo(
        $id_producto, 
        $id_almacen, 
        $id_ubicacion,
        $id_pedido_detalle 
    );
    $stock_otras = 0;

    if (is_array($otras)) {
        foreach ($otras as $u) {
            $stock_otras += floatval($u['stock']);
        }
    }

    error_log("📦 Stock otras ubicaciones: $stock_otras");

    // ============================================================
    // PASO 4:  CALCULAR NUEVA DISTRIBUCIÓN OS/OC
    // LÓGICA: Calcular lo que FALTA en destino para completar el pedido
    // ============================================================
    
    // Lo que falta en la ubicación destino
    $falta_en_destino = max(0, $cantidad_pedida - $stock_destino);
    
    //  OS (Orden de Salida): Lo que se puede trasladar desde otras ubicaciones
    $nueva_os = min($falta_en_destino, $stock_otras);
    
    //  OC (Orden de Compra): Lo que NO se puede cubrir ni con stock destino ni con traslados
    //  IMPORTANTE: Esta es la lógica ANTIGUA que funcionaba correctamente
    $nueva_oc = max(0, $falta_en_destino - $nueva_os);

    error_log("📊 Distribución calculada:");
    error_log("   - Stock destino actual: $stock_destino");
    error_log("   - Falta en destino: $falta_en_destino");
    error_log("   - Stock en otras ubicaciones: $stock_otras");
    error_log("   - ✅ Nueva OS (trasladar): $nueva_os");
    error_log("   - ✅ Nueva OC (comprar): $nueva_oc");
    error_log("   - Anterior OS: $cant_os_anterior");
    error_log("   - Anterior OC: $cant_oc_anterior");

    // ============================================================
    // PASO 5: Actualizar SOLO si cambió algo
    // ============================================================
    if ($nueva_os != $cant_os_anterior || $nueva_oc != $cant_oc_anterior) {

        $sql_update = "UPDATE pedido_detalle SET 
                          cant_os_pedido_detalle = $nueva_os,
                          cant_oc_pedido_detalle = $nueva_oc,
                          est_pedido_detalle = 1
                       WHERE id_pedido_detalle = $id_pedido_detalle";

        if (mysqli_query($con, $sql_update)) {
            error_log("✅ Item reverificado | OS: $cant_os_anterior → $nueva_os | OC: $cant_oc_anterior → $nueva_oc");
        } else {
            error_log("❌ Error al actualizar: " . mysqli_error($con));
        }
    } else {
        error_log("⏭️ Sin cambios en la reverificación (OS=$nueva_os, OC=$nueva_oc)");
    }

    mysqli_close($con);
    return true;
}
/**
 * Re-verifica TODOS los items de un pedido
 * Se usa después de anular una salida completa
 * ⚠️ NO CREA COMPROMISOS
 */
function ReverificarTodosLosItemsDelPedido($id_pedido) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    error_log("🔄 ReverificarTodosLosItemsDelPedido - Pedido: $id_pedido");
    
    // Obtener todos los detalles activos del pedido
    $sql_detalles = "SELECT id_pedido_detalle 
                     FROM pedido_detalle 
                     WHERE id_pedido = $id_pedido 
                     AND est_pedido_detalle IN (1, 2)";
    
    $res = mysqli_query($con, $sql_detalles);
    $detalles = array();
    
    while ($row = mysqli_fetch_assoc($res)) {
        $detalles[] = intval($row['id_pedido_detalle']);
    }
    
    mysqli_close($con);
    
    // Re-verificar cada detalle (sin crear compromisos)
    foreach ($detalles as $id_detalle) {
        ReverificarItemAutomaticamente($id_detalle);
    }
    
    error_log("✅ Re-verificación completa del pedido: $id_pedido (" . count($detalles) . " items procesados)");
    
    return true;
}

/**
 * Obtener cantidad en salidas ACTIVAS para un detalle (excluyendo anuladas)
 */
function ObtenerCantidadEnSalidasActivasPorDetalle($id_pedido_detalle) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_activo
            FROM salida_detalle sd
            INNER JOIN salida s ON sd.id_salida = s.id_salida
            WHERE sd.id_pedido_detalle = $id_pedido_detalle
            AND s.est_salida = 1  -- SOLO ACTIVAS
            AND sd.est_salida_detalle = 1";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return floatval($row['total_activo']);
}

/**
 * ============================================
 * FUNCIÓN UNIFICADA DE ESTADO DEL PEDIDO
 * ============================================
 * 
 * ESTADOS:
 * 0 = Anulado
 * 1 = Pendiente (incluye aprobados técnicamente)
 * 2 = Atendido (TODO completado: OC 100% ingresada + OS 100% recepcionadas)
 */
function ActualizarEstadoPedidoUnificado($id_pedido, $con = null)
{
    $cerrar_conexion = false;
    
    if ($con === null) {
        include("../_conexion/conexion.php");
        $cerrar_conexion = true;
    }
    
    error_log("🔄 ActualizarEstadoPedidoUnificado - Pedido: $id_pedido");
    
    // PASO 1: Obtener estado actual
    $sql_estado = "SELECT est_pedido FROM pedido WHERE id_pedido = $id_pedido";
    $res_estado = mysqli_query($con, $sql_estado);
    $row_estado = mysqli_fetch_assoc($res_estado);
    
    if (!$row_estado) {
        if ($cerrar_conexion) mysqli_close($con);
        error_log("❌ Pedido no encontrado");
        return;
    }
    
    $estado_actual = intval($row_estado['est_pedido']);
    error_log("   📊 Estado actual: $estado_actual");
    
    // No tocar si está anulado o finalizado
    if ($estado_actual == 0 || $estado_actual == 5) {
        if ($cerrar_conexion) mysqli_close($con);
        error_log("   ⏭️ Pedido anulado/finalizado - no se actualiza");
        return;
    }
    
    // PASO 2: Verificar si requiere OC
    $sql_requiere_oc = "SELECT COALESCE(SUM(cant_oc_pedido_detalle), 0) as total_oc_verificada
                        FROM pedido_detalle 
                        WHERE id_pedido = $id_pedido 
                        AND est_pedido_detalle IN (1, 2)";
    
    $res_oc = mysqli_query($con, $sql_requiere_oc);
    $row_oc = mysqli_fetch_assoc($res_oc);
    $total_oc_verificada = floatval($row_oc['total_oc_verificada']);
    $requiere_oc = ($total_oc_verificada > 0);
    
    error_log("   📦 Requiere OC: " . ($requiere_oc ? "SÍ (verificada: $total_oc_verificada)" : 'NO'));
    
    // PASO 3: Verificar si OC está 100% ingresada
    $oc_completada = false;
    
    if ($requiere_oc) {
        // Total ordenado en compras
        $sql_total_oc = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                         FROM compra c
                         INNER JOIN compra_detalle cd ON c.id_compra = cd.id_compra
                         WHERE c.id_pedido = $id_pedido
                         AND c.est_compra != 0
                         AND cd.est_compra_detalle = 1";
        
        $res_total_oc = mysqli_query($con, $sql_total_oc);
        $row_total_oc = mysqli_fetch_assoc($res_total_oc);
        $total_ordenado = floatval($row_total_oc['total_ordenado']);
        
        // Total ingresado
        $sql_total_ingresado = "SELECT COALESCE(SUM(id2.cant_ingreso_detalle), 0) as total_ingresado
                                FROM compra c
                                INNER JOIN ingreso i ON c.id_compra = i.id_compra
                                INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                                WHERE c.id_pedido = $id_pedido
                                AND i.est_ingreso = 1
                                AND id2.est_ingreso_detalle = 1";
        
        $res_total_ingresado = mysqli_query($con, $sql_total_ingresado);
        $row_total_ingresado = mysqli_fetch_assoc($res_total_ingresado);
        $total_ingresado = floatval($row_total_ingresado['total_ingresado']);
        
        $oc_completada = ($total_ingresado >= $total_ordenado && $total_ordenado > 0);
        
        error_log("   📊 OC - Ordenado: $total_ordenado | Ingresado: $total_ingresado | Completada: " . ($oc_completada ? 'SÍ' : 'NO'));
    } else {
        $oc_completada = true;
        error_log("   ✅ No requiere OC - considerado completado");
    }
    
    // PASO 4: Verificar si requiere OS
    $sql_requiere_os = "SELECT COALESCE(SUM(cant_os_pedido_detalle), 0) as total_os_verificada
                        FROM pedido_detalle 
                        WHERE id_pedido = $id_pedido 
                        AND est_pedido_detalle IN (1, 2)";
    
    $res_os = mysqli_query($con, $sql_requiere_os);
    $row_os = mysqli_fetch_assoc($res_os);
    $total_os_verificada = floatval($row_os['total_os_verificada']);
    $requiere_os = ($total_os_verificada > 0);
    
    error_log("   📦 Requiere OS: " . ($requiere_os ? "SÍ (verificada: $total_os_verificada)" : 'NO'));
    
    // PASO 5: Verificar si OS está 100% recepcionada
    $os_completada = false;
    
    if ($requiere_os) {
        // Total RECEPCIONADO (solo salidas con estado 2)
        $sql_os_recepcionada = "SELECT COALESCE(SUM(sd.cant_salida_detalle), 0) as total_recepcionada
                               FROM salida s
                               INNER JOIN salida_detalle sd ON s.id_salida = sd.id_salida
                               WHERE s.id_pedido = $id_pedido
                               AND s.est_salida = 2
                               AND sd.est_salida_detalle = 1";
        
        $res_recepcionada = mysqli_query($con, $sql_os_recepcionada);
        $row_recepcionada = mysqli_fetch_assoc($res_recepcionada);
        $total_os_recepcionada = floatval($row_recepcionada['total_recepcionada']);
        
        // Comparar: recepcionado >= verificado
        $os_completada = ($total_os_recepcionada >= $total_os_verificada && $total_os_verificada > 0);
        
        error_log("   📊 OS - Verificada: $total_os_verificada | Recepcionada: $total_os_recepcionada | Completada: " . ($os_completada ? 'SÍ' : 'NO'));
    } else {
        $os_completada = true;
        error_log("   ✅ No requiere OS - considerado completado");
    }
    
    // PASO 6: DETERMINAR ESTADO FINAL
    $nuevo_estado = 1; // Por defecto PENDIENTE/APROBADO
    
    if ($oc_completada && $os_completada) {
        $nuevo_estado = 2; // ATENDIDO
        error_log("   🎯 Nuevo estado: 2 (ATENDIDO) - OC y OS completadas");
    } else {
        error_log("   ⏸️ Nuevo estado: 1 (PENDIENTE/APROBADO) - Faltan OC o OS por completar");
        
        if (!$oc_completada) {
            error_log("      📦 Pendiente: OC no completada");
        }
        if (!$os_completada) {
            error_log("      📦 Pendiente: OS no completada");
        }
    }
    
    // PASO 7: ACTUALIZAR SI CAMBIÓ
    if ($nuevo_estado != $estado_actual) {
        $sql_update = "UPDATE pedido SET est_pedido = $nuevo_estado WHERE id_pedido = $id_pedido";
        mysqli_query($con, $sql_update);
        
        error_log("   ✅ Pedido actualizado: $estado_actual → $nuevo_estado");
    } else {
        error_log("   ⏭️ Sin cambios (ya está en estado $estado_actual)");
    }
    
    if ($cerrar_conexion) {
        mysqli_close($con);
    }
}

function VerificarTecnicamente($id_pedido, $id_personal) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    $id_personal = intval($id_personal);
    $fecha_actual = date('Y-m-d H:i:s');
    
    $sql = "UPDATE pedido 
            SET id_personal_verifica_tecnica = ?, 
                fec_verifica_tecnica = ? 
            WHERE id_pedido = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("isi", $id_personal, $fecha_actual, $id_pedido);
    
    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($con);
        return "SI";
    } else {
        $error = $stmt->error;
        $stmt->close();
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}

function TieneVerificacionTecnica($id_pedido) {
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    $sql = "SELECT id_personal_verifica_tecnica, fec_verifica_tecnica 
            FROM pedido 
            WHERE id_pedido = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    mysqli_close($con);
    
    return [
        'verificado' => !empty($row['id_personal_verifica_tecnica']),
        'id_personal' => $row['id_personal_verifica_tecnica'] ?? null,
        'fecha' => $row['fec_verifica_tecnica'] ?? null
    ];
}