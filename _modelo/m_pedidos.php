<?php
//-----------------------------------------------------------------------
// MODELO: m_pedidos.php
//-----------------------------------------------------------------------

// FUNCIONES CORREGIDAS PARA m_pedidos.php
// Estados: 0=Anulado, 1=Pendiente, 2=Completado, 3=Aprobado, 4=Ingresado, 5=Finalizado

//-----------------------------------------------------------------------
// Grabar Pedido (ahora acepta opcionalmente $id_obra) (debe iniciar con estado 1, no 5)
function GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo, 
                     $nom_pedido, $solicitante, $fecha_necesidad, 
                     $num_ot, $contacto, $lugar_entrega, $aclaraciones, $id_personal, 
                     $materiales, $archivos_subidos, $id_obra = null) 
{
    include("../_conexion/conexion.php");
    require_once("../_modelo/m_stock.php");

    $id_obra_sql = ($id_obra && $id_obra > 0) ? intval($id_obra) : "NULL";

    //estado inicial debe ser 1 (Pendiente)
    $sql = "INSERT INTO pedido (
                id_producto_tipo, id_almacen, id_ubicacion, id_centro_costo, id_personal, id_obra,
                cod_pedido, nom_pedido, ot_pedido, cel_pedido, lug_pedido, 
                acl_pedido, fec_req_pedido, fec_pedido, est_pedido
            ) VALUES (
                $id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo, $id_personal, $id_obra_sql,
                'TEMP', '" . mysqli_real_escape_string($con, $nom_pedido) . "', '" . mysqli_real_escape_string($con, $num_ot) . "',
                '" . mysqli_real_escape_string($con, $contacto) . "', '" . mysqli_real_escape_string($con, $lugar_entrega) . "',
                '" . mysqli_real_escape_string($con, $aclaraciones) . "', '" . mysqli_real_escape_string($con, $fecha_necesidad) . "', NOW(), 1
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
            
            // Obtener el nombre de la unidad por su ID
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = $resultado_unidad ? mysqli_fetch_assoc($resultado_unidad) : null;
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            $requisitos = $sst_descripcion;
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            $sql_detalle = "INSERT INTO pedido_detalle (
                                id_pedido, id_producto, prod_pedido_detalle, 
                                ot_pedido_detalle, cant_pedido_detalle, cant_fin_pedido_detalle, 
                                com_pedido_detalle, req_pedido, est_pedido_detalle
                            ) VALUES (
                                $id_pedido, $id_producto, '$descripcion',
                                 '$ot_detalle', $cantidad, NULL, 
                                '$comentario_detalle', '$requisitos', 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                $id_detalle = mysqli_insert_id($con);

                // ----------------------------------------------------------------
                // 🔍 Verificar stock y registrar reserva si hay disponible
                // ----------------------------------------------------------------
                $stock = ObtenerStock($id_producto, $id_almacen, $id_ubicacion);
                $stock_disponible = floatval($stock['disponible']);

                if ($stock_disponible > 0) {
                    // Si hay stock total o parcial
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
                          AND pd.cant_fin_pedido_detalle IS NOT NULL 
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
            ORDER BY p.fec_pedido DESC";

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
function MostrarPedidosFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");
   
    // Filtro de fechas
    if ($fecha_inicio && $fecha_fin) {
        $where_fecha = " AND DATE(p.fec_pedido) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where_fecha = " AND DATE(p.fec_pedido) = CURDATE()";
    }

    // Consulta principal con todos los JOINs, incluyendo el área (por personal)
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
                          AND pd.cant_fin_pedido_detalle IS NOT NULL 
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
            $where_fecha
            ORDER BY p.fec_pedido DESC";

    $resc = mysqli_query($con, $sqlc);

    if (!$resc) {
        error_log("Error en MostrarPedidosFecha(): " . mysqli_error($con));
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
                ON pr.id_area = ar.id_area AND ar.act_area = 1
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
             COALESCE(
                (SELECT SUM(CASE
                    WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END)
                FROM movimiento mov
                INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
                WHERE mov.id_producto = pd.id_producto 
                AND mov.id_almacen = ped.id_almacen 
                AND mov.id_ubicacion = ped.id_ubicacion
                AND mov.est_movimiento = 1), 0
             ) AS cantidad_disponible_almacen
             FROM pedido_detalle pd 
             LEFT JOIN pedido_detalle_documento pdd ON pd.id_pedido_detalle = pdd.id_pedido_detalle
                AND pdd.est_pedido_detalle_documento = 1
             INNER JOIN producto p ON pd.id_producto = p.id_producto
             INNER JOIN pedido ped ON pd.id_pedido = ped.id_pedido
             WHERE pd.id_pedido = $id_pedido AND pd.est_pedido_detalle IN (1, 2)
             GROUP BY pd.id_pedido_detalle
             ORDER BY pd.id_pedido_detalle";
             
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
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
function ActualizarPedido($id_pedido, $id_ubicacion, $id_centro_costo, $nom_pedido, $fecha_necesidad, $num_ot, 
                         $contacto, $lugar_entrega, $aclaraciones, $materiales, $archivos_subidos, $id_obra = null) 
{
    include("../_conexion/conexion.php");

    $id_obra_sql = ($id_obra && $id_obra > 0) ? intval($id_obra) : "NULL";

    // Actualizar pedido principal - AHORA INCLUYE id_ubicacion, id_centro_costo e id_obra
    $sql = "UPDATE pedido SET 
            id_ubicacion = $id_ubicacion,
            id_centro_costo = $id_centro_costo,
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
        
        // Procesar cada material - CORREGIDO: SST como campo único
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_unidad = intval($material['unidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst_descripcion = mysqli_real_escape_string($con, $material['sst_descripcion']); // CAMBIO: campo único
            $id_detalle = isset($material['id_detalle']) ? intval($material['id_detalle']) : 0;
            $ot_detalle = mysqli_real_escape_string($con, $material['ot_detalle']);

            // OBTENER EL NOMBRE DE LA UNIDAD
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = $resultado_unidad ? mysqli_fetch_assoc($resultado_unidad) : null;
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            // CAMBIO: req_pedido ahora almacena directamente la descripción SST/MA/CA
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
                } else {
                    // en caso de error, continuar para no interrumpir la edición masiva
                    $id_detalle_actual = $id_detalle;
                }
            } else {
                // INSERTAR NUEVO DETALLE (solo para materiales completamente nuevos)
                $sql_detalle = "INSERT INTO pedido_detalle (
                                    id_pedido, id_producto, prod_pedido_detalle, 
                                    ot_pedido_detalle,cant_pedido_detalle, cant_fin_pedido_detalle, 
                                    com_pedido_detalle, req_pedido, est_pedido_detalle
                                ) VALUES (
                                    $id_pedido, $id_producto, '$descripcion', 
                                    '$ot_detalle',
                                    $cantidad, NULL, 
                                    '$comentario_detalle', '$requisitos', 1
                                )";
                
                if (mysqli_query($con, $sql_detalle)) {
                    $id_detalle_actual = mysqli_insert_id($con);
                } else {
                    $id_detalle_actual = 0;
                }
            }
            
            // MANEJO MEJORADO DE ARCHIVOS - REEMPLAZAR ARCHIVOS EXISTENTES
            if ($id_detalle_actual > 0 && isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                
                // PASO 1: Obtener archivos existentes para este detalle
                $sql_archivos_existentes = "SELECT nom_pedido_detalle_documento 
                                          FROM pedido_detalle_documento 
                                          WHERE id_pedido_detalle = $id_detalle_actual 
                                          AND est_pedido_detalle_documento = 1";
                $resultado_archivos = mysqli_query($con, $sql_archivos_existentes);
                $archivos_a_eliminar = array();
                
                while ($row_archivo = mysqli_fetch_assoc($resultado_archivos)) {
                    $archivos_a_eliminar[] = $row_archivo['nom_pedido_detalle_documento'];
                }
                
                // PASO 2: Eliminar archivos físicos del servidor
                foreach ($archivos_a_eliminar as $nombre_archivo) {
                    $ruta_archivo = "../_archivos/pedidos/" . $nombre_archivo;
                    if (file_exists($ruta_archivo)) {
                        unlink($ruta_archivo); // Eliminar archivo físico
                    }
                }
                
                // PASO 3: Marcar como inactivos los registros de archivos anteriores
                $sql_inactivar_docs = "UPDATE pedido_detalle_documento 
                                      SET est_pedido_detalle_documento = 0 
                                      WHERE id_pedido_detalle = $id_detalle_actual";
                mysqli_query($con, $sql_inactivar_docs);
                
                // PASO 4: Guardar los nuevos archivos
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
        
        // Marcar como inactivos los detalles que ya no existen (fueron eliminados en la edición)
        if (!empty($detalles_existentes)) {
            $detalles_a_eliminar = array_diff($detalles_existentes, $detalles_utilizados);
            if (!empty($detalles_a_eliminar)) {
                $ids_eliminar = implode(',', $detalles_a_eliminar);
                
                // Antes de marcar como inactivos, eliminar los archivos físicos asociados
                $sql_archivos_eliminar = "SELECT nom_pedido_detalle_documento 
                                        FROM pedido_detalle_documento 
                                        WHERE id_pedido_detalle IN ($ids_eliminar) 
                                        AND est_pedido_detalle_documento = 1";
                $resultado_archivos_eliminar = mysqli_query($con, $sql_archivos_eliminar);
                
                while ($row_archivo = mysqli_fetch_assoc($resultado_archivos_eliminar)) {
                    $ruta_archivo = "../_archivos/pedidos/" . $row_archivo['nom_pedido_detalle_documento'];
                    if (file_exists($ruta_archivo)) {
                        unlink($ruta_archivo); // Eliminar archivo físico
                    }
                }
                
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

    $sql = "SELECT pd.id_pedido_detalle, pd.id_pedido, pd.id_producto, pd.cant_pedido_detalle, pd.cant_fin_pedido_detalle,
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
function verificarItem($id_pedido_detalle, $new_cant_fin)
{
    include("../_conexion/conexion.php");

    // Convertir a float para asegurar decimales
    $cantidad_verificada = floatval($new_cant_fin);
    
    // Validación adicional por seguridad
    if ($cantidad_verificada <= 0) {
        mysqli_close($con);
        return "ERROR: La cantidad verificada debe ser mayor a 0";
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
                   SET cant_fin_pedido_detalle = $cantidad_verificada
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
            AND cant_fin_pedido_detalle IS NOT NULL 
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
                            WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento = 1), 0
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
                COALESCE((
                    SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
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
                      AND mov.est_movimiento = 1
                ), 0) AS stock_comprometido,

                -- ==========================================
                -- STOCK DISPONIBLE = físico - comprometido
                -- ==========================================
                COALESCE((
                    SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
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
                      AND mov.est_movimiento = 1
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
              AND pd.est_pedido_detalle = 1
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
    $sql_finalizar = "UPDATE pedido SET est_pedido = 5 WHERE id_pedido = $id_pedido";
    
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
                            WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento = 1), 0
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
function CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, 
                         $observacion, $direccion, $plazo_entrega, $porte, 
                         $fecha_orden, $items, 
                         $id_detraccion = null, $archivos_homologacion = [],
                         $id_retencion = null, $id_percepcion = null) 
{
    include("../_conexion/conexion.php");

    // 🔹 VALIDAR CANTIDADES ANTES DE CREAR (sin id_compra porque es nueva)
    $errores = ValidarCantidadesOrden($id_pedido, $items, NULL); // ← NULL está correcto aquí
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
            $id_detalle = intval($item['id_detalle']);
            
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
                                id_compra, id_producto, cant_compra_detalle, 
                                prec_compra_detalle, igv_compra_detalle, hom_compra_detalle,
                                est_compra_detalle
                            ) VALUES (
                                $id_compra, $id_producto, $cantidad, 
                                $precio_unitario, $igv, $hom_sql,
                                1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                error_log("ERROR al insertar detalle: " . mysqli_error($con));
            }

            // VERIFICAR SI DEBE CERRARSE EL ITEM
            $cant_ordenada_total = ObtenerCantidadYaOrdenada($id_pedido, $id_producto);
            
            $sql_get_verificada = "SELECT cant_fin_pedido_detalle 
                                   FROM pedido_detalle 
                                   WHERE id_pedido_detalle = $id_detalle";
            $res_ver = mysqli_query($con, $sql_get_verificada);
            $row_ver = mysqli_fetch_assoc($res_ver);
            $cant_verificada = $row_ver ? floatval($row_ver['cant_fin_pedido_detalle']) : 0;
            
            // Solo cerrar si se alcanzó EXACTAMENTE la cantidad verificada
            if ($cant_ordenada_total >= $cant_verificada) {
                $sql_update = "UPDATE pedido_detalle 
                           SET est_pedido_detalle = 2 
                           WHERE id_pedido_detalle = $id_detalle";
                mysqli_query($con, $sql_update);
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
    $sql_verificada = "SELECT pd.cant_fin_pedido_detalle
                       FROM pedido_detalle pd
                       WHERE pd.id_pedido = $id_pedido 
                       AND pd.id_producto = $id_producto
                       AND pd.est_pedido_detalle IN (1, 2)
                       LIMIT 1";
    
    $res_verificada = mysqli_query($con, $sql_verificada);
    $row_verificada = mysqli_fetch_assoc($res_verificada);
    $cant_verificada = $row_verificada ? floatval($row_verificada['cant_fin_pedido_detalle']) : 0;
    
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
//-----------------------------------------------------------------------
function ActualizarOrdenCompra($id_compra, $proveedor, $moneda, $observacion, $direccion, 
                              $plazo_entrega, $porte, $fecha_orden, $items, 
                              $id_detraccion = null, $archivos_homologacion = [],
                              $id_retencion = null, $id_percepcion = null) {
    include("../_conexion/conexion.php");
    
    error_log(" ActualizarOrdenCompra - ID Compra: $id_compra");
    
    // Obtener id_pedido antes de actualizar
    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = $id_compra";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido['id_pedido'];
    
    error_log(" ID Pedido obtenido: $id_pedido");
    error_log(" Llamando a ValidarCantidadesOrden con id_compra: $id_compra");
    
    // 🔹 VALIDAR CANTIDADES ANTES DE ACTUALIZAR - PASAR ID_COMPRA
    $errores = ValidarCantidadesOrden($id_pedido, $items, $id_compra);
    
    if (!empty($errores)) {
        error_log(" Errores de validación: " . implode(", ", $errores));
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }
    
    error_log(" Validación pasada, continuando con actualización...");
    
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
        // RASTREAR PRODUCTOS AFECTADOS
        $productos_afectados = array();
        
        foreach ($items as $id_detalle => $item) {
            $id_detalle = intval($id_detalle);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            
            // OBTENER ID_PRODUCTO del detalle
            $sql_prod = "SELECT id_producto FROM compra_detalle WHERE id_compra_detalle = $id_detalle";
            $res_prod = mysqli_query($con, $sql_prod);
            $row_prod = mysqli_fetch_assoc($res_prod);
            if ($row_prod) {
                $productos_afectados[] = intval($row_prod['id_producto']);
            }
            
            // Manejar archivo de homologación si existe
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_detalle]) && !empty($archivos_homologacion[$id_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_det_" . $id_detalle . "_" . uniqid() . "." . $extension;
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
            
            $sql_detalle .= " WHERE id_compra_detalle = $id_detalle";
            
            if (!mysqli_query($con, $sql_detalle)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR en detalle: " . $error;
            }
        }
        
        // VERIFICAR SI LOS PRODUCTOS DEBEN REABRIRSE
        $productos_afectados = array_unique($productos_afectados);
        foreach ($productos_afectados as $id_producto) {
            VerificarReaperturaItem($id_pedido, $id_producto); 
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
 * Verificar si un item de pedido debe reabrirse después de editar/anular una orden
 */
function VerificarReaperturaItem($id_pedido, $id_producto)
{
    include("../_conexion/conexion.php");
    
    // Obtener cantidad verificada del item
    $sql_verificada = "SELECT cant_fin_pedido_detalle, id_pedido_detalle
                       FROM pedido_detalle 
                       WHERE id_pedido = $id_pedido 
                       AND id_producto = $id_producto 
                       AND cant_fin_pedido_detalle IS NOT NULL
                       LIMIT 1";
    $res = mysqli_query($con, $sql_verificada);
    $row = mysqli_fetch_assoc($res);
    
    if (!$row) {
        mysqli_close($con);
        return;
    }
    
    $cant_verificada = floatval($row['cant_fin_pedido_detalle']);
    $id_pedido_detalle = $row['id_pedido_detalle'];
    
    //  CORRECCIÓN: Calcular cantidad total en órdenes activas (excluir anuladas)
    $sql_ordenada = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as total_ordenado
                     FROM compra_detalle cd
                     INNER JOIN compra c ON cd.id_compra = c.id_compra
                     WHERE c.id_pedido = $id_pedido 
                     AND cd.id_producto = $id_producto
                     AND c.est_compra != 0  -- 🔹 EXCLUIR ANULADAS
                     AND cd.est_compra_detalle = 1";
    $res_ord = mysqli_query($con, $sql_ordenada);
    $row_ord = mysqli_fetch_assoc($res_ord);
    $total_ordenado = floatval($row_ord['total_ordenado']);
    
    // Si la cantidad ordenada es menor a la verificada, reabrir el item
    if ($total_ordenado < $cant_verificada) {
        $sql_reabrir = "UPDATE pedido_detalle 
                        SET est_pedido_detalle = 1 
                        WHERE id_pedido_detalle = $id_pedido_detalle";
        mysqli_query($con, $sql_reabrir);
    }
    
    mysqli_close($con);
}

/**
 * Validar que las cantidades no excedan lo verificado
 */
function ValidarCantidadesOrden($id_pedido, $items_orden, $id_compra_actual = null)
{
    include("../_conexion/conexion.php");
    
    $errores = array();
    
    foreach ($items_orden as $key => $item) {
        $id_producto = intval($item['id_producto']);
        $cantidad_nueva = floatval($item['cantidad']);
                
        // Obtener cantidad verificada
        $sql_verificada = "SELECT cant_fin_pedido_detalle
                           FROM pedido_detalle 
                           WHERE id_pedido = $id_pedido 
                           AND id_producto = $id_producto
                           LIMIT 1";
        $res = mysqli_query($con, $sql_verificada);
        $row = mysqli_fetch_assoc($res);
        
        if (!$row || $row['cant_fin_pedido_detalle'] === null) {
            $errores[] = "El producto ID $id_producto no está verificado";
            continue;
        }
        
        $cant_verificada = floatval($row['cant_fin_pedido_detalle']);
        
        // Calcular cantidad ya ordenada (excluyendo la orden actual si estamos editando)
        $where_compra = "";
        if ($id_compra_actual) {
            $where_compra = "AND c.id_compra != $id_compra_actual";
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
        
        // Validación: Verificar que la cantidad nueva no exceda lo disponible
        $disponible = $cant_verificada - $ya_ordenado;
        
        if ($cantidad_nueva > $disponible) {
            $errores[] = "Producto ID $id_producto: Cantidad excede lo verificado. Verificado: $cant_verificada, Ya ordenado (sin esta orden): $ya_ordenado, Disponible: $disponible, Intentaste ordenar: $cantidad_nueva";
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
                            WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END)
                        FROM movimiento mov
                        WHERE mov.id_producto = pd.id_producto 
                        AND mov.id_almacen = $id_almacen
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento = 1), 0
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
function ObtenerStockProducto($id_producto, $id_almacen = null, $id_ubicacion = null) {
    include("../_conexion/conexion.php");

    $id_producto = intval($id_producto);

    $whereBase = "id_producto = $id_producto AND est_movimiento = 1";

    if (!is_null($id_almacen)) {
        $id_almacen = intval($id_almacen);
        $whereBase .= " AND id_almacen = $id_almacen";
    }
    if (!is_null($id_ubicacion)) {
        $id_ubicacion = intval($id_ubicacion);
        $whereBase .= " AND id_ubicacion = $id_ubicacion";
    }

    // 1) Stock físico: consideramos movimientos que afectan realmente el stock (EXCLUIMOS tipo_orden = 5 que son reservas)
    $sql_fisico = "SELECT COALESCE(SUM(
                    CASE
                        WHEN tipo_movimiento = 1 THEN cant_movimiento
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento
                        ELSE 0
                    END), 0) AS stock_fisico
                  FROM movimiento
                  WHERE $whereBase
                    AND tipo_orden <> 5";

    $res = mysqli_query($con, $sql_fisico);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    $stock_fisico = $row ? floatval($row['stock_fisico']) : 0.0;

    // 2) Stock reservado (compromisos): tipo_orden = 5, tipo_movimiento = 2, est_movimiento = 1
    $sql_reservado = "SELECT COALESCE(SUM(cant_movimiento),0) AS stock_reservado
                      FROM movimiento
                      WHERE $whereBase
                        AND tipo_orden = 5
                        AND tipo_movimiento = 2
                        AND est_movimiento = 1";

    $res2 = mysqli_query($con, $sql_reservado);
    $row2 = $res2 ? mysqli_fetch_assoc($res2) : null;
    $stock_reservado = $row2 ? floatval($row2['stock_reservado']) : 0.0;

    // 3) (opcional) stock neto si consideráramos las reservas como salidas (para comparar con versiones anteriores)
    $sql_incluye_comp = "SELECT COALESCE(SUM(
                            CASE
                              WHEN tipo_movimiento = 1 THEN cant_movimiento
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
            $id_detalle = intval($item['id_detalle']);
            
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
                                id_compra, id_producto, cant_compra_detalle, 
                                prec_compra_detalle, igv_compra_detalle, hom_compra_detalle,
                                est_compra_detalle
                            ) VALUES (
                                $id_compra, $id_producto, $cantidad, 
                                $precio_unitario, $igv, $hom_sql,
                                1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                error_log("ERROR al insertar detalle de servicio: " . mysqli_error($con));
            }

            // VERIFICAR SI DEBE CERRARSE EL ITEM
            // Obtener la cantidad ORIGINAL del pedido_detalle (NO del item de la orden)
            $sql_get_original = "SELECT cant_pedido_detalle 
                                FROM pedido_detalle 
                                WHERE id_pedido_detalle = $id_detalle";
            $res_original = mysqli_query($con, $sql_get_original);
            $row_original = mysqli_fetch_assoc($res_original);
            $cant_original = $row_original ? floatval($row_original['cant_pedido_detalle']) : 0;
            
            // Obtener cuánto se ha ordenado en total (todas las órdenes activas)
            $cant_total_ordenada = ObtenerCantidadYaOrdenadaServicio($id_pedido, $id_producto);
            
            // Solo cerrar si se alcanzó o superó la cantidad original
            if ($cant_total_ordenada >= $cant_original) {
                $sql_cerrar = "UPDATE pedido_detalle 
                            SET est_pedido_detalle = 2 
                            WHERE id_pedido_detalle = $id_detalle";
                mysqli_query($con, $sql_cerrar);
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
 * Actualizar Orden de Servicio (sin validación de stock)
 */
function ActualizarOrdenServicio($id_compra, $proveedor, $moneda, $observacion, $direccion, 
                                 $plazo_entrega, $porte, $fecha_orden, $items, 
                                 $id_detraccion = null, $archivos_homologacion = [],
                                 $id_retencion = null, $id_percepcion = null) 
{
    include("../_conexion/conexion.php");
    
    error_log(" ActualizarOrdenServicio - ID Compra: $id_compra");
    
    // Obtener id_pedido antes de actualizar
    $sql_pedido = "SELECT id_pedido FROM compra WHERE id_compra = $id_compra";
    $res_pedido = mysqli_query($con, $sql_pedido);
    $row_pedido = mysqli_fetch_assoc($res_pedido);
    $id_pedido = $row_pedido['id_pedido'];
    
    error_log(" ID Pedido obtenido: $id_pedido");
    
    //  NUEVA VALIDACIÓN PARA SERVICIOS
    $errores = ValidarCantidadesOrdenServicio($id_pedido, $items, $id_compra);
    
    if (!empty($errores)) {
        error_log(" Errores de validación en servicio: " . implode(", ", $errores));
        mysqli_close($con);
        return "ERROR: " . implode(". ", $errores);
    }
    
    error_log(" Validación de servicio pasada, continuando...");
    
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
        //  RASTREAR PRODUCTOS AFECTADOS PARA SERVICIOS
        $productos_afectados = array();
        
        foreach ($items as $id_detalle => $item) {
            $id_detalle = intval($id_detalle);
            $cantidad = floatval($item['cantidad']);
            $precio_unitario = floatval($item['precio_unitario']);
            $igv = floatval($item['igv']);
            
            // Obtener ID_PRODUCTO del detalle
            $sql_prod = "SELECT id_producto FROM compra_detalle WHERE id_compra_detalle = $id_detalle";
            $res_prod = mysqli_query($con, $sql_prod);
            $row_prod = mysqli_fetch_assoc($res_prod);
            if ($row_prod) {
                $productos_afectados[] = intval($row_prod['id_producto']);
            }
            
            // Manejar archivo de homologación si existe
            $nombre_archivo_hom = null;
            if (isset($archivos_homologacion[$id_detalle]) && !empty($archivos_homologacion[$id_detalle]['name'])) {
                $archivo = $archivos_homologacion[$id_detalle];
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombre_archivo_hom = "hom_compra_" . $id_compra . "_det_" . $id_detalle . "_" . uniqid() . "." . $extension;
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
            
            $sql_detalle .= " WHERE id_compra_detalle = $id_detalle";
            
            if (!mysqli_query($con, $sql_detalle)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR en detalle: " . $error;
            }
        }
        
        //  VERIFICAR SI LOS PRODUCTOS DEBEN REABRIRSE (PARA SERVICIOS)
        $productos_afectados = array_unique($productos_afectados);
        foreach ($productos_afectados as $id_producto) {
            VerificarReaperturaItemServicio($id_pedido, $id_producto); 
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
 * Validar cantidades en órdenes de servicio (usa cantidad ORIGINAL, no verificada)
 */
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
        
        // Calcular cantidad ya ordenada en TODAS las órdenes activas
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
        
        // Si estamos editando, obtener la cantidad actual de esta orden
        $cantidad_actual_orden = 0;
        if ($id_compra_actual) {
            $sql_actual = "SELECT COALESCE(SUM(cd.cant_compra_detalle), 0) as cantidad_actual
                           FROM compra_detalle cd
                           WHERE cd.id_compra = $id_compra_actual 
                           AND cd.id_producto = $id_producto
                           AND cd.est_compra_detalle = 1";
            $res_actual = mysqli_query($con, $sql_actual);
            $row_actual = mysqli_fetch_assoc($res_actual);
            $cantidad_actual_orden = floatval($row_actual['cantidad_actual']);
        }
        
        //  CÁLCULO CORREGIDO:
        // - $total_ordenado: suma de TODAS las órdenes activas (incluyendo la actual si existe)
        // - $cantidad_actual_orden: cantidad que actualmente tiene esta orden (solo en edición)
        // - $cantidad_nueva: cantidad que queremos asignar
        
        // El total ordenado SIN esta orden sería: $total_ordenado - $cantidad_actual_orden
        $ordenado_sin_esta_orden = $total_ordenado - $cantidad_actual_orden;
        
        // El nuevo total ordenado si se aprueba esta orden sería: $ordenado_sin_esta_orden + $cantidad_nueva
        $nuevo_total_ordenado = $ordenado_sin_esta_orden + $cantidad_nueva;
        
        // Validación: El nuevo total NO debe exceder la cantidad original
        if ($nuevo_total_ordenado > $cant_original) {
            $descripcion_corta = "Producto ID $id_producto";
            
            // Obtener descripción del producto para el mensaje de error
            $sql_desc = "SELECT nom_producto FROM producto WHERE id_producto = $id_producto";
            $res_desc = mysqli_query($con, $sql_desc);
            $row_desc = mysqli_fetch_assoc($res_desc);
            if ($row_desc) {
                $descripcion_corta = substr($row_desc['nom_producto'], 0, 50);
                if (strlen($row_desc['nom_producto']) > 50) $descripcion_corta .= '...';
            }
            
            $tipoItem = $id_compra_actual ? '[EDITANDO]' : '[NUEVO]';
            
            $error = "<strong>{$tipoItem} {$descripcion_corta}:</strong><br>" .
                    "Cantidad ingresada: <strong>{$cantidad_nueva}</strong><br>" .
                    "Original: {$cant_original} | " .
                    "Ya ordenado (total): {$total_ordenado} | " .
                    ($id_compra_actual ? "Cantidad actual en esta orden: {$cantidad_actual_orden} | " : "") .
                    "Nuevo total ordenado: {$nuevo_total_ordenado} | " .
                    "<strong style=\"color: #dc3545;\">Excede el original por: " . ($nuevo_total_ordenado - $cant_original) . "</strong>";
            
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