<?php
function MostrarUsoMaterial($fecha_inicio = null, $fecha_fin = null, $id_personal_filtro = null) 
{
    include("../_conexion/conexion.php");

    $where = "WHERE usm.est_uso_material <> 99";
    
    // Filtro de fechas
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(usm.fec_uso_material) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where .= " AND DATE(usm.fec_uso_material) = CURDATE()";
    }
    
    // Filtro por personal
    if ($id_personal_filtro !== null && $id_personal_filtro > 0) {
        $id_personal_filtro = intval($id_personal_filtro);
        $where .= " AND usm.id_personal = $id_personal_filtro";
    }
    
    $sql = "SELECT usm.*, 
                alm.nom_almacen, 
                o.nom_subestacion as nom_obra,
                c.nom_cliente, 
                u.nom_ubicacion, 
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_completo_solicitante
            FROM uso_material usm 
            LEFT JOIN almacen alm ON usm.id_almacen = alm.id_almacen 
            LEFT JOIN {$bd_complemento}.subestacion o ON alm.id_obra = o.id_subestacion
            LEFT JOIN {$bd_complemento}.cliente c ON alm.id_cliente = c.id_cliente 
            LEFT JOIN ubicacion u ON usm.id_ubicacion = u.id_ubicacion 
            LEFT JOIN {$bd_complemento}.personal per1 ON usm.id_personal = per1.id_personal     -- quien registra
            LEFT JOIN {$bd_complemento}.personal per2 ON usm.id_solicitante = per2.id_personal -- solicitante
            $where
            ORDER BY usm.fec_uso_material DESC";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        error_log("❌ Error en MostrarUsoMaterial: " . mysqli_error($con));
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



function ConsultarUsoMaterial($id_uso_material)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT usm.*, 
                alm.nom_almacen, 
                o.nom_subestacion as nom_obra,
                c.nom_cliente, 
                u.nom_ubicacion, 
                per1.nom_personal AS nom_registrado, 
                per2.nom_personal AS nom_solicitante
            FROM uso_material usm 
            LEFT JOIN almacen alm ON usm.id_almacen = alm.id_almacen 
            LEFT JOIN {$bd_complemento}.subestacion o ON alm.id_obra = o.id_subestacion 
            LEFT JOIN {$bd_complemento}.cliente c ON alm.id_cliente = c.id_cliente 
            LEFT JOIN ubicacion u ON usm.id_ubicacion = u.id_ubicacion 
            LEFT JOIN {$bd_complemento}.personal per1 ON usm.id_personal = per1.id_personal 
            LEFT JOIN {$bd_complemento}.personal per2 ON usm.id_solicitante = per2.id_personal 
            WHERE usm.id_uso_material = $id_uso_material";
    
    $resc = mysqli_query($con, $sql);
    $resultado = array();
    
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    
    mysqli_close($con);
    return $resultado;
}

function ConsultarUsoMaterialDetalle($id_uso_material)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT umd.*, 
                p.nom_producto,
                p.id_unidad_medida,
                um.nom_unidad_medida,
                GROUP_CONCAT(umdd.nom_uso_material_detalle_documento) as archivos,
                COALESCE(
                    (SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    INNER JOIN uso_material usm ON umd.id_uso_material = usm.id_uso_material
                    WHERE mov.id_producto = umd.id_producto 
                    AND mov.id_almacen = usm.id_almacen 
                    AND mov.id_ubicacion = usm.id_ubicacion
                    AND mov.est_movimiento != 0), 0
                ) + umd.cant_uso_material_detalle AS cantidad_disponible_almacen
            FROM uso_material_detalle umd 
            LEFT JOIN uso_material_detalle_documento umdd ON umd.id_uso_material_detalle = umdd.id_uso_material_detalle AND umdd.est_uso_material_detalle_documento = 1
            INNER JOIN producto p ON umd.id_producto = p.id_producto
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            WHERE umd.id_uso_material = $id_uso_material 
            AND umd.est_uso_material_detalle = 1
            GROUP BY umd.id_uso_material_detalle
            ORDER BY umd.id_uso_material_detalle";
    
    $resc = mysqli_query($con, $sql);
    $resultado = array();
    
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    
    mysqli_close($con);
    return $resultado;
}

function GrabarUsoMaterial($id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales, $archivos_subidos) 
{
    include("../_conexion/conexion.php");
    $id_personal = $_SESSION['id_personal'];
    // Iniciar transacción ANTES de hacer cualquier cosa
    mysqli_autocommit($con, false);

    try {
        // PRIMERO: Validar TODOS los stocks antes de insertar NADA
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            // Verificar stock disponible
            $sql_stock = "SELECT COALESCE(SUM(CASE
                            WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END), 0) AS stock_actual
                        FROM movimiento mov
                        WHERE mov.id_producto = $id_producto 
                        AND mov.id_almacen = $id_almacen 
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0";
            
            $result_stock = mysqli_query($con, $sql_stock);
            $row_stock = mysqli_fetch_assoc($result_stock);
            $stock_actual = floatval($row_stock['stock_actual']);
            
            if ($stock_actual < $cantidad) {
                // Cerrar conexión y devolver error SIN hacer rollback porque no hemos insertado nada
                mysqli_close($con);
                return "Stock insuficiente para el producto. Stock disponible: " . number_format($stock_actual, 2) . ", Cantidad solicitada: " . number_format($cantidad, 2);
            }
        }
        
        // SEGUNDO: Si todos los stocks están OK, generar ID y proceder con inserciones
        $sql_max_id = "SELECT COALESCE(MAX(id_uso_material), 0) + 1 as next_id FROM uso_material";
        $result_id = mysqli_query($con, $sql_max_id);
        $row_id = mysqli_fetch_assoc($result_id);
        $id_uso_material = $row_id['next_id'];

        // Insertar uso de material principal con estado APROBADO (2) directamente
        $sql = "INSERT INTO uso_material (
                    id_uso_material, id_almacen, id_ubicacion, id_personal, 
                    id_solicitante, fec_uso_material, est_uso_material
                ) VALUES (
                    $id_uso_material, $id_almacen, $id_ubicacion, $id_personal, 
                    $id_solicitante, NOW(), 2
                )";

        if (!mysqli_query($con, $sql)) {
            throw new Exception('Error al insertar uso de material: ' . mysqli_error($con));
        }

        // Insertar detalles del uso de material y movimientos
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            
            // Insertar detalle
            $sql_detalle = "INSERT INTO uso_material_detalle (
                                id_uso_material, id_producto, cant_uso_material_detalle, 
                                obs_uso_material_detalle, est_uso_material_detalle
                            ) VALUES (
                                $id_uso_material, $id_producto, $cantidad, 
                                '$observaciones', 1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                throw new Exception('Error al insertar detalle de uso: ' . mysqli_error($con));
            }

            $id_detalle = mysqli_insert_id($con);
            
            // Registrar movimiento de salida con tipo_orden = 4 (USO) INMEDIATAMENTE
            $sql_movimiento = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                              ) VALUES (
                                $id_personal, $id_uso_material, $id_producto, $id_almacen, 
                                $id_ubicacion, 4, 2, 
                                $cantidad, NOW(), 1
                              )";
            
            if (!mysqli_query($con, $sql_movimiento)) {
                throw new Exception('Error al registrar movimiento: ' . mysqli_error($con));
            }
            
            // Guardar archivos si existen
            if (isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                foreach ($archivos_subidos[$index]['name'] as $key => $archivo_nombre) {
                    if (!empty($archivo_nombre)) {
                        $extension = pathinfo($archivo_nombre, PATHINFO_EXTENSION);
                        $nuevo_nombre = "uso_material_" . $id_uso_material . "_detalle_" . $id_detalle . "_" . $key . "_" . uniqid() . "." . $extension;
                        
                        $ruta_destino = "../_archivos/uso_material/" . $nuevo_nombre;
                        
                        if (move_uploaded_file($archivos_subidos[$index]['tmp_name'][$key], $ruta_destino)) {
                            $sql_doc = "INSERT INTO uso_material_detalle_documento (
                                            id_uso_material_detalle, nom_uso_material_detalle_documento, 
                                            est_uso_material_detalle_documento
                                        ) VALUES ($id_detalle, '$nuevo_nombre', 1)";
                            
                            if (!mysqli_query($con, $sql_doc)) {
                                // No fallar por archivos, solo registrar error
                                error_log("Error al guardar documento: " . mysqli_error($con));
                            }
                        }
                    }
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        return "SI";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($con);
        mysqli_close($con);
        return $e->getMessage();
    }
}

function ActualizarUsoMaterial($id_uso_material, $id_ubicacion, $id_solicitante, $materiales, $archivos_subidos) 
{
    include("../_conexion/conexion.php");
    
    // Obtener datos actuales del uso de material
    $sql_actual = "SELECT id_almacen, id_personal, id_ubicacion as ubicacion_anterior FROM uso_material WHERE id_uso_material = $id_uso_material";
    $result_actual = mysqli_query($con, $sql_actual);
    $row_actual = mysqli_fetch_assoc($result_actual);
    $id_almacen = $row_actual['id_almacen'];
    $id_personal = $row_actual['id_personal'];
    $ubicacion_anterior = $row_actual['ubicacion_anterior'];

    // Iniciar transacción
    mysqli_autocommit($con, false);

    try {
        // Actualizar uso de material principal
        $sql = "UPDATE uso_material SET 
                id_ubicacion = $id_ubicacion,
                id_solicitante = $id_solicitante
                WHERE id_uso_material = $id_uso_material";

        if (!mysqli_query($con, $sql)) {
            throw new Exception('Error al actualizar uso de material: ' . mysqli_error($con));
        }
        
        // Marcar como inactivos los movimientos anteriores
        $sql_desactivar_movimientos = "UPDATE movimiento SET est_movimiento = 0 
                                      WHERE id_orden = $id_uso_material 
                                      AND tipo_orden = 4 
                                      AND est_movimiento = 1";
        
        if (!mysqli_query($con, $sql_desactivar_movimientos)) {
            throw new Exception('Error al desactivar movimientos anteriores: ' . mysqli_error($con));
        }
        
        // Obtener todos los detalles existentes
        $sql_existentes = "SELECT umd.id_uso_material_detalle, umd.id_producto, umd.cant_uso_material_detalle
                          FROM uso_material_detalle umd 
                          WHERE umd.id_uso_material = $id_uso_material 
                          AND umd.est_uso_material_detalle = 1";
        $result_existentes = mysqli_query($con, $sql_existentes);
        
        $detalles_existentes = array();
        while ($row = mysqli_fetch_assoc($result_existentes)) {
            $detalles_existentes[$row['id_uso_material_detalle']] = $row;
        }
        
        // Array para trackear qué detalles se están usando
        $detalles_utilizados = array();
        
        // Procesar cada material
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $id_detalle = isset($material['id_detalle']) ? intval($material['id_detalle']) : 0;
            
            // Verificar stock disponible (ahora que los movimientos anteriores están desactivados)
            $sql_stock = "SELECT COALESCE(SUM(CASE
                            WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden != 3 THEN mov.cant_movimiento
                            WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                            ELSE 0
                        END), 0) AS stock_actual
                        FROM movimiento mov
                        WHERE mov.id_producto = $id_producto 
                        AND mov.id_almacen = $id_almacen 
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0";
            
            $result_stock = mysqli_query($con, $sql_stock);
            $row_stock = mysqli_fetch_assoc($result_stock);
            $stock_actual = $row_stock['stock_actual'];
            
            if ($stock_actual < $cantidad) {
                throw new Exception("Stock insuficiente para el producto. Stock disponible: $stock_actual, Cantidad solicitada: $cantidad");
            }
            
            if ($id_detalle > 0 && isset($detalles_existentes[$id_detalle])) {
                // Actualizar detalle existente
                $sql_detalle = "UPDATE uso_material_detalle SET 
                                cant_uso_material_detalle = $cantidad,
                                obs_uso_material_detalle = '$observaciones'
                                WHERE id_uso_material_detalle = $id_detalle";
                
                if (!mysqli_query($con, $sql_detalle)) {
                    throw new Exception('Error al actualizar detalle: ' . mysqli_error($con));
                }
                
                $id_detalle_actual = $id_detalle;
                $detalles_utilizados[] = $id_detalle;
            } else {
                // Insertar nuevo detalle
                $sql_detalle = "INSERT INTO uso_material_detalle (
                                    id_uso_material, id_producto, cant_uso_material_detalle, 
                                    obs_uso_material_detalle, est_uso_material_detalle
                                ) VALUES (
                                    $id_uso_material, $id_producto, $cantidad, 
                                    '$observaciones', 1
                                )";
                
                if (!mysqli_query($con, $sql_detalle)) {
                    throw new Exception('Error al insertar detalle: ' . mysqli_error($con));
                }
                
                $id_detalle_actual = mysqli_insert_id($con);
            }
            
            // Crear nuevo movimiento de salida con tipo_orden = 4 (USO)
            $sql_movimiento = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_uso_material, $id_producto, $id_almacen, 
                                $id_ubicacion, 4, 2, 
                                $cantidad, NOW(), 1
                            )";
            
            if (!mysqli_query($con, $sql_movimiento)) {
                throw new Exception('Error al registrar nuevo movimiento: ' . mysqli_error($con));
            }
            
            // Guardar archivos si existen
            if (isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                foreach ($archivos_subidos[$index]['name'] as $key => $archivo_nombre) {
                    if (!empty($archivo_nombre)) {
                        $extension = pathinfo($archivo_nombre, PATHINFO_EXTENSION);
                        $nuevo_nombre = "uso_material_" . $id_uso_material . "_detalle_" . $id_detalle_actual . "_" . $key . "_" . uniqid() . "." . $extension;
                        
                        $ruta_destino = "../_archivos/uso_material/" . $nuevo_nombre;
                        
                        if (move_uploaded_file($archivos_subidos[$index]['tmp_name'][$key], $ruta_destino)) {
                            $sql_doc = "INSERT INTO uso_material_detalle_documento (
                                            id_uso_material_detalle, nom_uso_material_detalle_documento, 
                                            est_uso_material_detalle_documento
                                        ) VALUES ($id_detalle_actual, '$nuevo_nombre', 1)";
                            mysqli_query($con, $sql_doc);
                        }
                    }
                }
            }
        }
        
        // Marcar como inactivos los detalles que ya no existen
        if (!empty($detalles_existentes)) {
            $detalles_a_eliminar = array_diff(array_keys($detalles_existentes), $detalles_utilizados);
            if (!empty($detalles_a_eliminar)) {
                $ids_eliminar = implode(',', $detalles_a_eliminar);
                $sql_eliminar = "UPDATE uso_material_detalle SET est_uso_material_detalle = 0 
                                WHERE id_uso_material_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar);
                
                // Marcar como inactivos los documentos de esos detalles
                $sql_eliminar_docs = "UPDATE uso_material_detalle_documento SET est_uso_material_detalle_documento = 0 
                                     WHERE id_uso_material_detalle IN ($ids_eliminar)";
                mysqli_query($con, $sql_eliminar_docs);
            }
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        return "SI";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($con);
        mysqli_close($con);
        return $e->getMessage();
    }
}

function AnularUsoMaterial($id_uso_material)
{
    include("../_conexion/conexion.php");

    // Iniciar transacción
    mysqli_autocommit($con, false);
    
    try {
        // Marcar como inactivos los movimientos del uso de material
        $sql_desactivar_movimientos = "UPDATE movimiento SET est_movimiento = 0 
                                      WHERE id_orden = $id_uso_material 
                                      AND tipo_orden = 4 
                                      AND est_movimiento = 1";
        
        if (!mysqli_query($con, $sql_desactivar_movimientos)) {
            throw new Exception('Error al desactivar movimientos del uso de material: ' . mysqli_error($con));
        }

        // Anular uso de material
        $sql = "UPDATE uso_material SET est_uso_material = 0 WHERE id_uso_material = $id_uso_material";
        
        if (!mysqli_query($con, $sql)) {
            throw new Exception('Error al anular el uso de material: ' . mysqli_error($con));
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        return "SI";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($con);
        mysqli_close($con);
        return $e->getMessage();
    }
}
?>