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

?>