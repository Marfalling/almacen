<?php
//-----------------------------------------------------------------------
// MODELO: m_pedidos.php
//-----------------------------------------------------------------------

// Grabar Pedido (ahora acepta opcionalmente $id_obra)
function GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $id_centro_costo, 
                     $nom_pedido, $solicitante, $fecha_necesidad, 
                     $num_ot, $contacto, $lugar_entrega, $aclaraciones, $id_personal, 
                     $materiales, $archivos_subidos, $id_obra = null) 
{
    include("../_conexion/conexion.php");

    $id_obra_sql = ($id_obra && $id_obra > 0) ? intval($id_obra) : "NULL";

    // INSERTAR PRIMERO el pedido SIN código para obtener el ID
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
            
            // Obtener el nombre de la unidad por su ID
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = $resultado_unidad ? mysqli_fetch_assoc($resultado_unidad) : null;
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            $requisitos = $sst_descripcion;
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            $sql_detalle = "INSERT INTO pedido_detalle (
                                id_pedido, id_producto, prod_pedido_detalle, 
                                cant_pedido_detalle, cant_fin_pedido_detalle, 
                                com_pedido_detalle, req_pedido, est_pedido_detalle
                            ) VALUES (
                                $id_pedido, $id_producto, '$descripcion',
                                $cantidad, NULL, 
                                '$comentario_detalle', '$requisitos', 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                $id_detalle = mysqli_insert_id($con);
                
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
    include("../_conexion/conexion_complemento.php"); // AGREGAR para centros de costo

    $sqlc = "SELECT p.*, 
                -- intentar primero la obra asociada directamente al pedido,
                -- si no existe usar la obra asociada al almacén (compatibilidad)
                COALESCE(obp.nom_obra, oba.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo,
                p.est_pedido as est_pedido_calc,
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
             LEFT JOIN obra obp ON p.id_obra = obp.id_obra AND obp.est_obra = 1
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
             LEFT JOIN obra oba ON a.id_obra = oba.id_obra AND oba.est_obra = 1
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente AND c.est_cliente = 1
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal AND pr.est_personal = 1
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
             WHERE p.est_pedido IN (0, 1, 2)
             ORDER BY p.fec_pedido DESC";

    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("Error en MostrarPedidos(): " . mysqli_error($con));
        mysqli_close($con);
        mysqli_close($con_comp);
        return array();
    }
    
    // Cargar centros de costo una sola vez
    $sql_centros = "SELECT id_area, nom_area FROM area WHERE act_area = 1";
    $res_centros = mysqli_query($con_comp, $sql_centros);
    $centros_costo = array();
    while ($cc = mysqli_fetch_assoc($res_centros)) {
        $centros_costo[$cc['id_area']] = $cc['nom_area'];
    }
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        // Asignar el nombre del centro de costo
        $rowc['nom_centro_costo'] = isset($centros_costo[$rowc['id_centro_costo']]) 
            ? $centros_costo[$rowc['id_centro_costo']] 
            : 'N/A';
        
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    mysqli_close($con_comp);
    return $resultado;
}
//-----------------------------------------------------------------------
function MostrarPedidosFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php");

    $where_fecha = "";
    if ($fecha_inicio && $fecha_fin) {
        $where_fecha = " AND DATE(p.fec_pedido) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where_fecha = " AND DATE(p.fec_pedido) = CURDATE()";
    }

    $sqlc = "SELECT p.*, 
                COALESCE(obp.nom_obra, oba.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo,
                p.est_pedido as est_pedido_calc,
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
             LEFT JOIN obra obp ON p.id_obra = obp.id_obra AND obp.est_obra = 1
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
             LEFT JOIN obra oba ON a.id_obra = oba.id_obra AND oba.est_obra = 1
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente AND c.est_cliente = 1
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal AND pr.est_personal = 1
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
             WHERE p.est_pedido IN (0, 1, 2)
             $where_fecha
             ORDER BY p.fec_pedido DESC";

    $resc = mysqli_query($con, $sqlc);
    
    if (!$resc) {
        error_log("Error en MostrarPedidosFecha(): " . mysqli_error($con));
        mysqli_close($con);
        mysqli_close($con_comp);
        return array();
    }
    
    $sql_centros = "SELECT id_area, nom_area FROM area WHERE act_area = 1";
    $res_centros = mysqli_query($con_comp, $sql_centros);
    $centros_costo = array();
    while ($cc = mysqli_fetch_assoc($res_centros)) {
        $centros_costo[$cc['id_area']] = $cc['nom_area'];
    }
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $rowc['nom_centro_costo'] = isset($centros_costo[$rowc['id_centro_costo']]) 
            ? $centros_costo[$rowc['id_centro_costo']] 
            : 'N/A';
        
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    mysqli_close($con_comp);
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
    include("../_conexion/conexion_complemento.php");

    $sqlc = "SELECT p.*, 
                COALESCE(obp.nom_obra, oba.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo
             FROM pedido p 
             LEFT JOIN obra obp ON p.id_obra = obp.id_obra
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen
             LEFT JOIN obra oba ON a.id_obra = oba.id_obra
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             WHERE p.id_pedido = ?";
    
    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido);
    mysqli_stmt_execute($stmt);
    $resc = mysqli_stmt_get_result($stmt);
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        // Obtener nombre del centro de costo desde la BD complemento
        if (isset($rowc['id_centro_costo']) && !empty($rowc['id_centro_costo'])) {
            $id_cc = intval($rowc['id_centro_costo']);
            $sql_cc = "SELECT nom_area FROM area WHERE id_area = $id_cc";
            $res_cc = mysqli_query($con_comp, $sql_cc);
            if ($res_cc && mysqli_num_rows($res_cc) > 0) {
                $cc_data = mysqli_fetch_assoc($res_cc);
                $rowc['nom_centro_costo'] = $cc_data['nom_area'];
            } else {
                $rowc['nom_centro_costo'] = 'N/A';
            }
        } else {
            $rowc['nom_centro_costo'] = 'N/A';
        }
        
        $resultado[] = $rowc;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    mysqli_close($con_comp);
    return $resultado;
}

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
        // Limpiar archivos nulos o vacíos del GROUP_CONCAT
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
                                    cant_pedido_detalle, cant_fin_pedido_detalle, 
                                    com_pedido_detalle, req_pedido, est_pedido_detalle
                                ) VALUES (
                                    $id_pedido, $id_producto, '$descripcion', 
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

function verificarItem($id_pedido_detalle, $new_cant_fin){
    include("../_conexion/conexion.php");

    $sql = "SELECT cant_pedido_detalle FROM pedido_detalle WHERE id_pedido_detalle = $id_pedido_detalle";
    $res = mysqli_query($con, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $sql_update = "UPDATE pedido_detalle 
               SET cant_fin_pedido_detalle = $new_cant_fin
               WHERE id_pedido_detalle = $id_pedido_detalle";

        if (mysqli_query($con, $sql_update)) {
            mysqli_close($con);
            return "SI";
        } else {
            $error = mysqli_error($con);
            mysqli_close($con);
            return "ERROR: " . $error;
        }
    } else {
        mysqli_close($con);
        return "ERROR: Item no encontrado";
    }
}

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
function ObtenerItemsParaSalida($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                pd.id_producto,
                pd.prod_pedido_detalle as descripcion,
                pd.cant_pedido_detalle as cantidad,
                p.nom_producto,
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
                ) AS stock_disponible
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

function FinalizarPedido($id_pedido)
{
    include("../_conexion/conexion.php");
    
    // Verificar que todos los items estén listos para finalizar
    $puede_finalizar = verificarPedidoListo($id_pedido, $con);
    
    if (!$puede_finalizar['listo']) {
        mysqli_close($con);
        return [
            'success' => false,
            'tipo' => 'warning',
            'mensaje' => $puede_finalizar['mensaje']
        ];
    }
    
    // Actualizar el pedido a completado (estado = 2)
    $sql_finalizar = "UPDATE pedido SET est_pedido = 2 WHERE id_pedido = $id_pedido";
    
    if (mysqli_query($con, $sql_finalizar)) {
        // Verificar que realmente se actualizó
        $verificar = mysqli_affected_rows($con);
        mysqli_close($con);
        
        if ($verificar > 0) {
            return [
                'success' => true,
                'mensaje' => 'El pedido se ha marcado como completado exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'tipo' => 'warning',
                'mensaje' => 'No se pudo actualizar el estado del pedido (ya podría estar completado)'
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
        'mensaje' => 'El pedido está listo para finalizarse'
    ];
}

function CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, $observacion, $direccion, $plazo_entrega, $porte, $fecha_orden, $items, $id_detraccion = null) 
{
    include("../_conexion/conexion.php");

    // Escapar caracteres especiales
    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';

    $sql = "INSERT INTO compra (
                id_pedido, id_proveedor, id_moneda, id_personal, id_personal_aprueba, 
                obs_compra, denv_compra, plaz_compra, port_compra, id_detraccion, 
                fec_compra, est_compra
            ) VALUES (
                $id_pedido, $proveedor, $moneda, $id_personal, NULL, 
                '$observacion', '$direccion', '$plazo_entrega', '$porte', $id_detraccion_sql, 
                NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_compra = mysqli_insert_id($con);
        
        foreach ($items as $item) {
            $cantidad = floatval($item['cantidad']);
            $id_producto = intval($item['id_producto']);
            $precio_unitario = floatval($item['precio_unitario']);
            $id_detalle = intval($item['id_detalle']);
            
            $sql_detalle = "INSERT INTO compra_detalle (
                                id_compra, id_producto, cant_compra_detalle, prec_compra_detalle, est_compra_detalle
                            ) VALUES (
                                $id_compra, $id_producto, $cantidad, $precio_unitario, 1
                            )";
            
            mysqli_query($con, $sql_detalle);

            $sql_update = "UPDATE pedido_detalle 
                       SET est_pedido_detalle = 2 
                       WHERE id_pedido_detalle = $id_detalle";
            mysqli_query($con, $sql_update);
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $err = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $err;
    }
}

//-----------------------------------------------------------------------
function ObtenerOrdenPorId($id_compra) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT c.*, p.id_pedido, 
            d.nombre_detraccion, d.porcentaje as porcentaje_detraccion
            FROM compra c 
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido 
            LEFT JOIN detraccion d ON c.id_detraccion = d.id_detraccion
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

function ActualizarOrdenCompra($id_compra, $proveedor, $moneda, $observacion, $direccion, $plazo_entrega, $porte, $fecha_orden, $items, $id_detraccion = null) {
    include("../_conexion/conexion.php");
    
    $observacion = mysqli_real_escape_string($con, $observacion);
    $direccion = mysqli_real_escape_string($con, $direccion);
    $plazo_entrega = mysqli_real_escape_string($con, $plazo_entrega);
    $porte = mysqli_real_escape_string($con, $porte);
    $id_detraccion_sql = ($id_detraccion && $id_detraccion > 0) ? $id_detraccion : 'NULL';
    
    $sql = "UPDATE compra SET 
            id_proveedor = $proveedor, 
            id_moneda = $moneda, 
            obs_compra = '$observacion', 
            denv_compra = '$direccion', 
            plaz_compra = '$plazo_entrega', 
            port_compra = '$porte', 
            id_detraccion = $id_detraccion_sql,
            fec_compra = '$fecha_orden' 
            WHERE id_compra = $id_compra";
    
    if (mysqli_query($con, $sql)) {
        foreach ($items as $id_detalle => $item) {
            $id_detalle = intval($id_detalle);
            $precio_unitario = floatval($item['precio_unitario']);
            
            $sql_detalle = "UPDATE compra_detalle 
                           SET prec_compra_detalle = $precio_unitario 
                           WHERE id_compra_detalle = $id_detalle";
            
            if (!mysqli_query($con, $sql_detalle)) {
                $error = mysqli_error($con);
                mysqli_close($con);
                return "ERROR en detalle: " . $error;
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

function ConsultarPedidoAnulado($id_pedido)
{
    include("../_conexion/conexion.php");
    include("../_conexion/conexion_complemento.php"); // AGREGAR

    $sqlc = "SELECT p.*, 
                COALESCE(obp.nom_obra, oba.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo
             FROM pedido p 
             LEFT JOIN obra obp ON p.id_obra = obp.id_obra
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen
             LEFT JOIN obra oba ON a.id_obra = oba.id_obra
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal
             LEFT JOIN producto_tipo pt ON p.id_producto_type = pt.id_producto_tipo
             WHERE p.id_pedido = ?";

    // Si tu versión de BD tiene columna id_producto_tipo tal cual, mantener pt join como antes.
    // Ajuste: usar la misma consulta que en ConsultarPedido (se sobreescribe el alias correcto más arriba)
    $sqlc = "SELECT p.*, 
                COALESCE(obp.nom_obra, oba.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo
             FROM pedido p 
             LEFT JOIN obra obp ON p.id_obra = obp.id_obra
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen
             LEFT JOIN obra oba ON a.id_obra = oba.id_obra
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             WHERE p.id_pedido = ?";
    
    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido);
    mysqli_stmt_execute($stmt);
    $resc = mysqli_stmt_get_result($stmt);
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        // AGREGAR ESTE BLOQUE: Obtener nombre del centro de costo
        if (isset($rowc['id_centro_costo']) && !empty($rowc['id_centro_costo'])) {
            $id_cc = intval($rowc['id_centro_costo']);
            $sql_cc = "SELECT nom_area FROM area WHERE id_area = $id_cc";
            $res_cc = mysqli_query($con_comp, $sql_cc);
            if ($res_cc && mysqli_num_rows($res_cc) > 0) {
                $cc_data = mysqli_fetch_assoc($res_cc);
                $rowc['nom_centro_costo'] = $cc_data['nom_area'];
            } else {
                $rowc['nom_centro_costo'] = 'N/A';
            }
        } else {
            $rowc['nom_centro_costo'] = 'N/A';
        }
        
        $resultado[] = $rowc;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    mysqli_close($con_comp); // AGREGAR
    return $resultado;
}
?>
