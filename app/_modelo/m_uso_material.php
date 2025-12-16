<?php

function GrabarUsoMaterial($id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales) 
{
    include("../_conexion/conexion.php");
    
    // Iniciar transacción
    mysqli_autocommit($con, false);

    try {
        // PRIMERO: Validar TODOS los stocks antes de insertar NADA
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            // Verificar stock disponible
            $sql_stock = "SELECT COALESCE(SUM(CASE
                            WHEN mov.tipo_movimiento = 1 AND mov.est_movimiento != 0 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
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
                mysqli_close($con);
                return [
                    'status' => 'error',
                    'message' => "Stock insuficiente para el producto ID: $id_producto. Stock disponible: " . number_format($stock_actual, 2) . ", Cantidad solicitada: " . number_format($cantidad, 2)
                ];
            }
        }
        
        // SEGUNDO: Generar ID y proceder con inserciones
        $sql_max_id = "SELECT COALESCE(MAX(id_uso_material), 0) + 1 as next_id FROM uso_material";
        $result_id = mysqli_query($con, $sql_max_id);
        $row_id = mysqli_fetch_assoc($result_id);
        $id_uso_material = $row_id['next_id'];

        // Insertar uso de material principal con estado APROBADO (2)
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
        foreach ($materiales as $material) {
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
            
            // Registrar movimiento de salida con tipo_orden = 4 (USO)
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
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'status' => 'success',
            'message' => 'Uso de material registrado correctamente',
            'id_uso_material' => $id_uso_material
        ];
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($con);
        mysqli_close($con);
        
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function GrabarUsoMaterialConArchivos($id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales, $archivos_por_material) 
{
    include("../_conexion/conexion.php");
    
    // Iniciar transacción
    mysqli_autocommit($con, false);

    try {
        // ✅ Validar stocks (igual que antes)
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            
            $sql_stock = "SELECT COALESCE(SUM(CASE
                            WHEN mov.tipo_movimiento = 1 AND mov.est_movimiento != 0 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
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
                mysqli_close($con);
                return [
                    'status' => 'error',
                    'message' => "Stock insuficiente para el producto ID: $id_producto"
                ];
            }
        }
        
        // ✅ Generar ID
        $sql_max_id = "SELECT COALESCE(MAX(id_uso_material), 0) + 1 as next_id FROM uso_material";
        $result_id = mysqli_query($con, $sql_max_id);
        $row_id = mysqli_fetch_assoc($result_id);
        $id_uso_material = $row_id['next_id'];

        // ✅ Insertar uso de material principal
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

        // ✅ Insertar detalles y procesar archivos
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
            
            // ✅ Procesar archivos para este material
            if (isset($archivos_por_material[$index]) && !empty($archivos_por_material[$index])) {
                $directorio = "../_archivos/uso_material/";
                
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                
                foreach ($archivos_por_material[$index] as $archivo) {
                    if ($archivo['error'] === UPLOAD_ERR_OK) {
                        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                        $nuevo_nombre = "uso_" . $id_uso_material . "_detalle_" . $id_detalle . "_" . uniqid() . "." . $extension;
                        $ruta_destino = $directorio . $nuevo_nombre;
                        
                        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                            $sql_doc = "INSERT INTO uso_material_detalle_documento (
                                            id_uso_material_detalle, nom_uso_material_detalle_documento, 
                                            est_uso_material_detalle_documento
                                        ) VALUES ($id_detalle, '$nuevo_nombre', 1)";
                            
                            if (!mysqli_query($con, $sql_doc)) {
                                error_log("⚠️ Error al guardar documento: " . mysqli_error($con));
                            } else {
                                error_log("✅ Documento guardado: $nuevo_nombre");
                            }
                        }
                    }
                }
            }
            
            // ✅ Registrar movimiento
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
        }
        
        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'status' => 'success',
            'message' => 'Uso de material registrado correctamente',
            'id_uso_material' => $id_uso_material
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function MostrarUsoMaterial($id_usuario, $filtro = null) 
{
    include("../_conexion/conexion.php");

    $where = "WHERE usm.est_uso_material <> 99";
    
    // Filtro por usuario si es necesario
    if (!empty($id_usuario)) {
        $where .= " AND usm.id_personal = '$id_usuario'";
    }
    
    // Filtro de búsqueda
    if (!empty($filtro)) {
        $where .= " AND (usm.id_uso_material LIKE '%$filtro%' 
                    OR alm.nom_almacen LIKE '%$filtro%' 
                    OR o.nom_subestacion LIKE '%$filtro%' 
                    OR per2.nom_personal LIKE '%$filtro%')";
    }
    
    $sql = "SELECT usm.*, 
                alm.nom_almacen, 
                o.nom_subestacion as nom_obra,
                c.nom_cliente as nom_cliente, 
                u.nom_ubicacion, 
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, 'Sin solicitante') AS nom_solicitante,
                DATE_FORMAT(usm.fec_uso_material, '%d/%m/%Y') as fecha_formato
            FROM uso_material usm 
            LEFT JOIN almacen alm ON usm.id_almacen = alm.id_almacen 
            LEFT JOIN {$bd_complemento}.subestacion o ON alm.id_obra = o.id_subestacion
            LEFT JOIN {$bd_complemento}.cliente c ON alm.id_cliente = c.id_cliente 
            LEFT JOIN ubicacion u ON usm.id_ubicacion = u.id_ubicacion 
            LEFT JOIN {$bd_complemento}.personal per1 ON usm.id_personal = per1.id_personal     -- quien registra
            LEFT JOIN {$bd_complemento}.personal per2 ON usm.id_solicitante = per2.id_personal -- solicitante
            $where
            ORDER BY usm.fec_uso_material DESC";
    
    $resc = mysqli_query($con, $sql) or die("Error en consulta: " . mysqli_error($con));
    $resultado = array();
    
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    
    mysqli_close($con);
    return $resultado;
}

function ConsultarDocumentosDetalle($id_uso_material_detalle)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM uso_material_detalle_documento 
            WHERE id_uso_material_detalle = $id_uso_material_detalle 
            AND est_uso_material_detalle_documento = 1";
    
    $result = mysqli_query($con, $sql);
    $documentos = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $documentos[] = array(
            "id_documento" => $row['id_uso_material_detalle_documento'],
            "nombre_archivo" => $row['nom_uso_material_detalle_documento']
        );
    }
    
    mysqli_close($con);
    return $documentos;
}

// ✅ NUEVA FUNCIÓN: Eliminar documento
function EliminarDocumento($id_documento)
{
    include("../_conexion/conexion.php");
    
    // Obtener nombre del archivo antes de eliminar
    $sql_get = "SELECT nom_uso_material_detalle_documento 
                FROM uso_material_detalle_documento 
                WHERE id_uso_material_detalle_documento = $id_documento";
    $result = mysqli_query($con, $sql_get);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        $nombre_archivo = $row['nom_uso_material_detalle_documento'];
        $ruta_archivo = "../_archivos/uso_material/" . $nombre_archivo;
        
        // Eliminar archivo físico
        if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
        }
        
        // Marcar como eliminado en BD
        $sql_delete = "UPDATE uso_material_detalle_documento 
                      SET est_uso_material_detalle_documento = 0 
                      WHERE id_uso_material_detalle_documento = $id_documento";
        
        if (mysqli_query($con, $sql_delete)) {
            mysqli_close($con);
            return [
                'status' => 'success',
                'message' => 'Documento eliminado correctamente'
            ];
        }
    }
    
    mysqli_close($con);
    return [
        'status' => 'error',
        'message' => 'Error al eliminar documento'
    ];
}

function ConsultarUsoMaterialDetalle($id_uso_material) 
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT umd.*, 
                p.nom_producto,
                um.nom_unidad_medida
            FROM uso_material_detalle umd
            LEFT JOIN producto p ON umd.id_producto = p.id_producto
            LEFT JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            WHERE umd.id_uso_material = '$id_uso_material' 
            AND umd.est_uso_material_detalle <> 0
            ORDER BY p.nom_producto";
    
    $resc = mysqli_query($con, $sql) or die("Error en consulta detalle: " . mysqli_error($con));
    $resultado = array();
    
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $id_detalle = $rowc['id_uso_material_detalle'];
        
        // ✅ OBTENER DOCUMENTOS
        $documentos = ConsultarDocumentosDetalle($id_detalle);
        
        $resultado[] = array(
            "id_uso_material_detalle" => $id_detalle,
            "id_uso_material" => $rowc['id_uso_material'],
            "id_producto" => $rowc['id_producto'],
            "nom_producto" => $rowc['nom_producto'] ?: 'Producto sin nombre',
            "cant_uso_material_detalle" => floatval($rowc['cant_uso_material_detalle']),
            "obs_uso_material_detalle" => $rowc['obs_uso_material_detalle'] ?: '',
            "nom_unidad_medida" => $rowc['nom_unidad_medida'] ?: 'UND',
            "est_uso_material_detalle" => $rowc['est_uso_material_detalle'],
            "documentos" => $documentos // ✅ NUEVO
        );
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

function ConsultarUsoMaterialCompleto($id_uso_material)
{
    include("../_conexion/conexion.php");
    
    error_log("Ejecutando ConsultarUsoMaterialCompleto para ID: " . $id_uso_material);

    $sql = "SELECT usm.*, 
                alm.nom_almacen, 
                u.nom_ubicacion, 
                per1.nom_personal AS nom_registrado, 
                per2.nom_personal AS nom_solicitante,
                DATE_FORMAT(usm.fec_uso_material, '%d/%m/%Y') as fecha_formato
            FROM uso_material usm 
            LEFT JOIN almacen alm ON usm.id_almacen = alm.id_almacen 
            LEFT JOIN ubicacion u ON usm.id_ubicacion = u.id_ubicacion 
            LEFT JOIN {$bd_complemento}.personal per1 ON usm.id_personal = per1.id_personal 
            LEFT JOIN {$bd_complemento}.personal per2 ON usm.id_solicitante = per2.id_personal 
            WHERE usm.id_uso_material = $id_uso_material";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        error_log("Error en consulta principal: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $resultado = array();
    
    if ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado = $rowc;
        
        // Obtener detalles de materiales CON DOCUMENTOS
        $sql_detalle = "SELECT umd.*, 
                            p.nom_producto,
                            p.cod_material,
                            um.nom_unidad_medida
                        FROM uso_material_detalle umd
                        LEFT JOIN producto p ON umd.id_producto = p.id_producto
                        LEFT JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
                        WHERE umd.id_uso_material = $id_uso_material 
                        AND umd.est_uso_material_detalle <> 0
                        ORDER BY p.nom_producto";
        
        $resc_detalle = mysqli_query($con, $sql_detalle);
        
        if (!$resc_detalle) {
            error_log("Error en consulta detalle: " . mysqli_error($con));
            mysqli_close($con);
            return $resultado;
        }
        
        $materiales = array();
        
        while ($row_detalle = mysqli_fetch_array($resc_detalle, MYSQLI_ASSOC)) {
            $id_detalle = $row_detalle['id_uso_material_detalle'];
            
            // ✅ OBTENER DOCUMENTOS
            $documentos = ConsultarDocumentosDetalle($id_detalle);
            
            $materiales[] = array(
                "id_uso_material_detalle" => intval($id_detalle),
                "id_producto" => intval($row_detalle['id_producto']),
                "nom_producto" => $row_detalle['nom_producto'] ?: 'Producto sin nombre',
                "cod_material" => $row_detalle['cod_material'] ?: '',
                "cant_uso_material_detalle" => floatval($row_detalle['cant_uso_material_detalle']),
                "obs_uso_material_detalle" => $row_detalle['obs_uso_material_detalle'] ?: '',
                "nom_unidad_medida" => $row_detalle['nom_unidad_medida'] ?: 'UND',
                "est_uso_material_detalle" => intval($row_detalle['est_uso_material_detalle']),
                "documentos" => $documentos // ✅ NUEVO
            );
        }
        
        $resultado['materiales'] = $materiales;
    }
    
    mysqli_close($con);
    return $resultado;
}

function ActualizarUsoMaterial($id_uso_material, $id_almacen, $id_ubicacion, $id_solicitante, $id_personal, $materiales) 
{
    include("../_conexion/conexion.php");
    
    error_log("🔧 ActualizarUsoMaterial - Iniciando");
    error_log("   - ID Uso Material: $id_uso_material");
    error_log("   - ID Almacén: $id_almacen");
    error_log("   - ID Ubicación: $id_ubicacion");
    error_log("   - ID Solicitante: $id_solicitante");
    error_log("   - ID Personal: $id_personal");
    error_log("   - Materiales: " . print_r($materiales, true));
    
    // ✅ Validar que $materiales sea un array
    if (!is_array($materiales)) {
        error_log("❌ ERROR: \$materiales no es un array: " . gettype($materiales));
        return [
            'status' => 'error',
            'message' => 'Formato de materiales inválido'
        ];
    }
    
    // ✅ Obtener datos actuales del uso de material
    $sql_actual = "SELECT id_almacen, id_personal, id_ubicacion as ubicacion_anterior 
                   FROM uso_material 
                   WHERE id_uso_material = $id_uso_material";
    $result_actual = mysqli_query($con, $sql_actual);
    
    if (!$result_actual) {
        error_log("❌ Error al consultar uso actual: " . mysqli_error($con));
        mysqli_close($con);
        return [
            'status' => 'error',
            'message' => 'Error al consultar datos actuales'
        ];
    }
    
    $row_actual = mysqli_fetch_assoc($result_actual);
    
    if (!$row_actual) {
        error_log("❌ No se encontró el uso de material ID: $id_uso_material");
        mysqli_close($con);
        return [
            'status' => 'error',
            'message' => 'Uso de material no encontrado'
        ];
    }
    
    // ✅ USAR el id_almacen de la base de datos (NO cambiar almacén)
    $id_almacen_bd = $row_actual['id_almacen'];
    $id_personal_bd = $row_actual['id_personal'];
    $ubicacion_anterior = $row_actual['ubicacion_anterior'];
    
    error_log("   - Almacén BD: $id_almacen_bd");
    error_log("   - Personal BD: $id_personal_bd");
    error_log("   - Ubicación anterior: $ubicacion_anterior");

    // ✅ Iniciar transacción
    mysqli_autocommit($con, false);

    try {
        // Actualizar uso de material principal (solo ubicación y solicitante)
        $sql = "UPDATE uso_material SET 
                id_almacen = $id_almacen,
                id_ubicacion = $id_ubicacion,
                id_solicitante = $id_solicitante
                WHERE id_uso_material = $id_uso_material";

        error_log("📝 SQL Update principal: $sql");

        if (!mysqli_query($con, $sql)) {
            throw new Exception('Error al actualizar uso de material: ' . mysqli_error($con));
        }
        
        // ✅ Desactivar TODOS los movimientos anteriores
        $sql_desactivar_movimientos = "UPDATE movimiento SET est_movimiento = 0 
                                      WHERE id_orden = $id_uso_material 
                                      AND tipo_orden = 4 
                                      AND est_movimiento = 1";
        
        error_log("📝 SQL Desactivar movimientos: $sql_desactivar_movimientos");
        
        if (!mysqli_query($con, $sql_desactivar_movimientos)) {
            throw new Exception('Error al desactivar movimientos anteriores: ' . mysqli_error($con));
        }
        
        // ✅ Obtener todos los detalles existentes
        $sql_existentes = "SELECT id_uso_material_detalle, id_producto, cant_uso_material_detalle
                          FROM uso_material_detalle 
                          WHERE id_uso_material = $id_uso_material 
                          AND est_uso_material_detalle = 1";
        $result_existentes = mysqli_query($con, $sql_existentes);
        
        $detalles_existentes = array();
        while ($row = mysqli_fetch_assoc($result_existentes)) {
            $detalles_existentes[$row['id_uso_material_detalle']] = $row;
        }
        
        error_log("📦 Detalles existentes en BD: " . count($detalles_existentes));
        
        // ✅ Procesar cada material de la lista final
        $detalles_procesados = array();
        
        foreach ($materiales as $index => $material) {
            error_log("🔄 Procesando material índice $index");
            
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $id_detalle = isset($material['id_detalle']) ? intval($material['id_detalle']) : 0;
            
            error_log("   - ID Producto: $id_producto");
            error_log("   - Cantidad: $cantidad");
            error_log("   - Observaciones: $observaciones");
            error_log("   - ID Detalle: $id_detalle");
            
            // ✅ Verificar stock disponible (usando el almacén de la BD)
            $sql_stock = "SELECT COALESCE(SUM(CASE
                            WHEN mov.tipo_movimiento = 1 AND mov.est_movimiento != 0 THEN
                                CASE
                                    WHEN mov.tipo_orden = 3 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                                    WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                                    ELSE 0
                                END
                            WHEN mov.tipo_movimiento = 2 AND mov.est_movimiento != 0 THEN -mov.cant_movimiento
                            ELSE 0
                        END), 0) AS stock_actual
                        FROM movimiento mov
                        WHERE mov.id_producto = $id_producto 
                        AND mov.id_almacen = $id_almacen_bd 
                        AND mov.id_ubicacion = $id_ubicacion
                        AND mov.est_movimiento != 0";
            
            $result_stock = mysqli_query($con, $sql_stock);
            $row_stock = mysqli_fetch_assoc($result_stock);
            $stock_actual = floatval($row_stock['stock_actual']);
            
            error_log("   - Stock actual disponible: $stock_actual");
            
            if ($stock_actual < $cantidad) {
                throw new Exception("Stock insuficiente para el producto ID: $id_producto. Stock disponible: " . number_format($stock_actual, 2) . ", Cantidad solicitada: " . number_format($cantidad, 2));
            }
            
            // ✅ Decidir si ACTUALIZAR o INSERTAR
            if ($id_detalle > 0 && isset($detalles_existentes[$id_detalle])) {
                // ACTUALIZAR detalle existente
                $sql_detalle = "UPDATE uso_material_detalle SET 
                                cant_uso_material_detalle = $cantidad,
                                obs_uso_material_detalle = '$observaciones'
                                WHERE id_uso_material_detalle = $id_detalle";
                
                error_log("📝 SQL Update detalle: $sql_detalle");
                
                if (!mysqli_query($con, $sql_detalle)) {
                    throw new Exception('Error al actualizar detalle: ' . mysqli_error($con));
                }
                
                $id_detalle_actual = $id_detalle;
                $detalles_procesados[] = $id_detalle;
                error_log("✅ Detalle ACTUALIZADO ID: $id_detalle_actual");
                
            } else {
                // INSERTAR nuevo detalle
                $sql_detalle = "INSERT INTO uso_material_detalle (
                                    id_uso_material, id_producto, cant_uso_material_detalle, 
                                    obs_uso_material_detalle, est_uso_material_detalle
                                ) VALUES (
                                    $id_uso_material, $id_producto, $cantidad, 
                                    '$observaciones', 1
                                )";
                
                error_log("📝 SQL Insert detalle: $sql_detalle");
                
                if (!mysqli_query($con, $sql_detalle)) {
                    throw new Exception('Error al insertar detalle: ' . mysqli_error($con));
                }
                
                $id_detalle_actual = mysqli_insert_id($con);
                error_log("✅ Detalle INSERTADO ID: $id_detalle_actual");
            }
            
            // ✅ Crear NUEVO movimiento de salida (usando datos de la BD)
            $sql_movimiento = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal_bd, $id_uso_material, $id_producto, $id_almacen_bd, 
                                $id_ubicacion, 4, 2, 
                                $cantidad, NOW(), 1
                            )";
            
            error_log("📝 SQL Insert movimiento: $sql_movimiento");
            
            if (!mysqli_query($con, $sql_movimiento)) {
                throw new Exception('Error al registrar nuevo movimiento: ' . mysqli_error($con));
            }
            
            $id_movimiento = mysqli_insert_id($con);
            error_log("✅ Movimiento creado ID: $id_movimiento");
        }
        
        // ✅ NO eliminamos detalles que no están en la lista
        // La función solo trabaja con los materiales que vienen en el array
        
        error_log("📊 Resumen:");
        error_log("   - Detalles procesados: " . count($detalles_procesados));
        error_log("   - Detalles existentes: " . count($detalles_existentes));
        
        mysqli_commit($con);
        mysqli_close($con);
        
        error_log("✅ Transacción completada exitosamente");
        
        return [
            'status' => 'success',
            'message' => 'Uso de material actualizado correctamente',
            'id_uso_material' => $id_uso_material
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        
        error_log("❌ Error en transacción: " . $e->getMessage());
        
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
?>