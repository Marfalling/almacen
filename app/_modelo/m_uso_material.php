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
                c.nom_cliente, 
                u.nom_ubicacion, 
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, 'Sin solicitante') AS nom_solicitante,
                DATE_FORMAT(usm.fec_uso_material, '%d/%m/%Y') as fecha_formato,
                TIME_FORMAT(usm.fec_uso_material, '%H:%i') as hora_formato
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

?>