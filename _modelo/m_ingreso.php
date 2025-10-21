<?php
//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
function ObtenerDetalleIngresoPorCompra($id_compra)
{
    include("../_conexion/conexion.php");
    
    // Verificar conexión
    if (!$con) {
        return false;
    }
    
    // Información básica de la compra
    $sql_compra = "SELECT 
                    c.id_compra,
                    c.id_pedido,
                    c.fec_compra,
                    c.est_compra,
                    p.cod_pedido,
                    pr.nom_proveedor,
                    al.nom_almacen,
                    pe1.nom_personal as registrado_por,
                    pe2.nom_personal as aprobado_tecnica_por,
                    pe3.nom_personal as aprobado_financiera_por
                FROM compra c
                INNER JOIN pedido p ON c.id_pedido = p.id_pedido
                INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
                INNER JOIN almacen al ON p.id_almacen = al.id_almacen
                LEFT JOIN {$bd_complemento}.personal pe1 ON c.id_personal = pe1.id_personal
                LEFT JOIN {$bd_complemento}.personal pe2 ON c.id_personal_aprueba_tecnica  = pe2.id_personal
                LEFT JOIN {$bd_complemento}.personal pe3 ON c.id_personal_aprueba_financiera  = pe3.id_personal
                WHERE c.id_compra = '$id_compra'";
                
    $resultado_compra = mysqli_query($con, $sql_compra);
    
    if (!$resultado_compra) {
        error_log("Error en consulta de compra: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $compra = mysqli_fetch_assoc($resultado_compra);
    
    if (!$compra) {
        mysqli_close($con);
        return false;
    }

    // Productos del detalle de compra con información de ingresos - CORREGIDO
    $sql_productos = "SELECT 
                        cd.id_producto,
                        cd.cant_compra_detalle,
                        prod.cod_material,
                        pd.prod_pedido_detalle as nom_producto,
                        um.nom_unidad_medida,
                        COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_ingresada,
                        MAX(i.fec_ingreso) as fecha_ultimo_ingreso
                    FROM compra_detalle cd
                    INNER JOIN compra c ON cd.id_compra = c.id_compra
                    INNER JOIN pedido_detalle pd ON cd.id_producto = pd.id_producto 
                        AND pd.id_pedido = c.id_pedido
                        AND pd.est_pedido_detalle IN (1, 2)
                    INNER JOIN producto prod ON cd.id_producto = prod.id_producto
                    INNER JOIN unidad_medida um ON prod.id_unidad_medida = um.id_unidad_medida
                    LEFT JOIN ingreso i ON i.id_compra = '$id_compra'
                    LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso AND cd.id_producto = id2.id_producto
                    WHERE cd.id_compra = '$id_compra'
                    AND cd.est_compra_detalle = 1
                    GROUP BY cd.id_compra_detalle, cd.id_producto, cd.cant_compra_detalle, 
                             prod.cod_material, pd.prod_pedido_detalle, um.nom_unidad_medida
                    ORDER BY pd.prod_pedido_detalle";
                    
    $resultado_productos = mysqli_query($con, $sql_productos);
    
    if (!$resultado_productos) {
        error_log("Error en consulta de productos: " . mysqli_error($con));
        mysqli_close($con);
        return false;
    }
    
    $productos = array();
    $total_productos = 0;
    $productos_completos = 0;
    $productos_parciales = 0;
    $productos_pendientes = 0;
    
    while ($row = mysqli_fetch_array($resultado_productos, MYSQLI_ASSOC)) {
        $cantidad_ingresada = floatval($row['cantidad_ingresada']);
        $cantidad_pedida = floatval($row['cant_compra_detalle']);
        $porcentaje = $cantidad_pedida > 0 ? ($cantidad_ingresada / $cantidad_pedida) * 100 : 0;
        
        if ($porcentaje >= 100) {
            $productos_completos++;
        } elseif ($porcentaje > 0) {
            $productos_parciales++;
        } else {
            $productos_pendientes++;
        }
        
        $productos[] = $row;
        $total_productos++;
    }

    // Historial de ingresos
        $sql_historial = "SELECT 
                            i.id_ingreso,
                            i.fec_ingreso,
                            COALESCE(p.nom_personal, 'Usuario') as nom_personal,
                            id2.cant_ingreso_detalle as cantidad_individual,
                            prod.nom_producto,
                            prod.cod_material
                        FROM ingreso i
                        LEFT JOIN {$bd_complemento}.personal p ON i.id_personal = p.id_personal
                        INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                        INNER JOIN producto prod ON id2.id_producto = prod.id_producto
                        WHERE i.id_compra = '$id_compra'
                        AND id2.est_ingreso_detalle = 1
                        ORDER BY i.fec_ingreso DESC, prod.nom_producto";
                            
        $resultado_historial = mysqli_query($con, $sql_historial);
        $historial = array();

        if ($resultado_historial) {
            while ($row = mysqli_fetch_array($resultado_historial, MYSQLI_ASSOC)) {
                $historial[] = $row;
            }
        }

    // Resumen
    $resumen = array(
        'total_productos' => $total_productos,
        'productos_completos' => $productos_completos,
        'productos_parciales' => $productos_parciales,
        'productos_pendientes' => $productos_pendientes
    );

    $resultado = array(
        'compra' => $compra,
        'productos' => $productos,
        'historial' => $historial,
        'resumen' => $resumen
    );
    
    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
function MostrarComprasAprobadas()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.id_compra,
                c.id_pedido,
                c.fec_compra,
                c.est_compra,
                p.cod_pedido,
                pr.nom_proveedor,
                pe1.nom_personal as registrado_por,
                pe2.nom_personal as aprobado_tecnica_por,
                pe3.nom_personal as aprobado_financiera_por,
                al.nom_almacen,
                ub.nom_ubicacion,
                mon.nom_moneda,
                -- Calcular productos pendientes de ingreso
                (SELECT COUNT(*) 
                 FROM compra_detalle cd 
                 WHERE cd.id_compra = c.id_compra) as total_productos,
                COALESCE((SELECT COUNT(DISTINCT id.id_producto) 
                         FROM ingreso i 
                         INNER JOIN ingreso_detalle id ON i.id_ingreso = id.id_ingreso 
                         WHERE i.id_compra = c.id_compra), 0) as productos_ingresados
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            INNER JOIN almacen al ON p.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON p.id_ubicacion = ub.id_ubicacion
            INNER JOIN moneda mon ON c.id_moneda = mon.id_moneda
            LEFT JOIN {$bd_complemento}.personal pe1 ON c.id_personal = pe1.id_personal
            LEFT JOIN {$bd_complemento}.personal pe2 ON c.id_personal_aprueba_tecnica = pe2.id_personal
            LEFT JOIN {$bd_complemento}.personal pe3 ON c.id_personal_aprueba_financiera = pe3.id_personal
            WHERE c.est_compra IN (2, 3)  -- Compras aprobadas (2) y cerradas (3)
            ORDER BY c.fec_compra DESC";
            
    $resultado = mysqli_query($con, $sql);
    $compras = array();

    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $compras[] = $row;
    }

    mysqli_close($con);
    return $compras;
}

//-----------------------------------------------------------------------
function ObtenerDetalleCompra($id_compra)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.id_compra,
                c.obs_compra,
                c.denv_compra,
                c.plaz_compra,
                c.port_compra,
                c.fec_compra,
                p.cod_pedido,
                p.nom_pedido,
                pr.nom_proveedor,
                pr.ruc_proveedor,
                al.nom_almacen,
                ub.nom_ubicacion,
                mon.nom_moneda
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            INNER JOIN almacen al ON p.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON p.id_ubicacion = ub.id_ubicacion
            INNER JOIN moneda mon ON c.id_moneda = mon.id_moneda
            WHERE c.id_compra = '$id_compra'";
            
    $resultado = mysqli_query($con, $sql);
    $compra = mysqli_fetch_assoc($resultado);

    // Obtener productos de la compra
    $sql_productos = "SELECT 
                        cd.id_compra_detalle,
                        cd.id_producto,
                        cd.cant_compra_detalle,
                        cd.prec_compra_detalle,
                        prod.nom_producto,
                        prod.cod_material,
                        um.nom_unidad_medida
                      FROM compra_detalle cd
                      INNER JOIN producto prod ON cd.id_producto = prod.id_producto
                      INNER JOIN unidad_medida um ON prod.id_unidad_medida = um.id_unidad_medida
                      WHERE cd.id_compra = '$id_compra'
                      AND cd.est_compra_detalle = 1";
                      
    $resultado_productos = mysqli_query($con, $sql_productos);
    $productos = array();
    
    while ($row = mysqli_fetch_array($resultado_productos, MYSQLI_ASSOC)) {
        $productos[] = $row;
    }
    
    $compra['productos'] = $productos;
    
    mysqli_close($con);
    return $compra;
}

//-----------------------------------------------------------------------
//-----------------------------------------------------------------------
function ObtenerProductosPendientesIngreso($id_compra)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                cd.id_compra_detalle,
                cd.id_producto,
                cd.cant_compra_detalle,
                pd.prod_pedido_detalle as nom_producto,
                prod.cod_material,
                um.nom_unidad_medida,
                COALESCE((
                    SELECT SUM(id2.cant_ingreso_detalle)
                    FROM ingreso i 
                    INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                    WHERE i.id_compra = '$id_compra'
                    AND id2.id_producto = cd.id_producto
                    AND i.est_ingreso = 1
                    AND id2.est_ingreso_detalle = 1
                ), 0) as cantidad_ingresada,
                (cd.cant_compra_detalle - COALESCE((
                    SELECT SUM(id2.cant_ingreso_detalle)
                    FROM ingreso i 
                    INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                    WHERE i.id_compra = '$id_compra'
                    AND id2.id_producto = cd.id_producto
                    AND i.est_ingreso = 1
                    AND id2.est_ingreso_detalle = 1
                ), 0)) as cantidad_pendiente
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra
            INNER JOIN pedido_detalle pd ON cd.id_producto = pd.id_producto 
                AND pd.id_pedido = c.id_pedido
                AND pd.est_pedido_detalle IN (1, 2)
            INNER JOIN producto prod ON cd.id_producto = prod.id_producto
            INNER JOIN unidad_medida um ON prod.id_unidad_medida = um.id_unidad_medida
            WHERE cd.id_compra = '$id_compra'
            AND cd.est_compra_detalle = 1
            HAVING cantidad_pendiente > 0
            ORDER BY pd.prod_pedido_detalle";
            
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ObtenerProductosPendientesIngreso: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $productos = array();

    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $productos[] = $row;
    }

    mysqli_close($con);
    return $productos;
}

//-----------------------------------------------------------------------
function ProcesarIngresoProducto($id_compra, $id_producto, $cantidad, $id_personal)
{
    include("../_conexion/conexion.php");
    
    if (!$con) {
        return array('success' => false, 'message' => 'Error de conexión a la base de datos');
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Obtener datos de la compra
        $sql_compra = "SELECT p.id_almacen, p.id_ubicacion 
                      FROM compra c 
                      INNER JOIN pedido p ON c.id_pedido = p.id_pedido 
                      WHERE c.id_compra = '$id_compra'";
        
        $res_compra = mysqli_query($con, $sql_compra);
        
        if (!$res_compra) {
            throw new Exception("Error en consulta de compra: " . mysqli_error($con));
        }
        
        $compra_data = mysqli_fetch_assoc($res_compra);
        
        if (!$compra_data) {
            throw new Exception("No se encontró la compra con ID: $id_compra");
        }
        
        $id_almacen = $compra_data['id_almacen'];
        $id_ubicacion = $compra_data['id_ubicacion'];
        
        // Verificar cantidad disponible
        $sql_disponible = "SELECT 
                            cd.cant_compra_detalle,
                            COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_ingresada,
                            (cd.cant_compra_detalle - COALESCE(SUM(id2.cant_ingreso_detalle), 0)) as cantidad_disponible
                        FROM compra_detalle cd
                        LEFT JOIN ingreso i ON i.id_compra = '$id_compra'
                        LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso AND cd.id_producto = id2.id_producto
                        WHERE cd.id_compra = '$id_compra'
                        AND cd.id_producto = '$id_producto'
                        AND cd.est_compra_detalle = 1
                        GROUP BY cd.id_compra_detalle";
        
        $res_disponible = mysqli_query($con, $sql_disponible);
        
        if (!$res_disponible) {
            throw new Exception("Error al verificar cantidad disponible: " . mysqli_error($con));
        }
        
        $row_disponible = mysqli_fetch_assoc($res_disponible);
        
        if (!$row_disponible) {
            throw new Exception("Producto no encontrado en la compra");
        }
        
        if ($cantidad > $row_disponible['cantidad_disponible']) {
            throw new Exception("Cantidad solicitada ($cantidad) mayor a la disponible ({$row_disponible['cantidad_disponible']})");
        }
        
        // Crear nuevo ingreso
        $fecha_ingreso = date('Y-m-d H:i:s');
        $sql_ingreso = "INSERT INTO ingreso (id_compra, id_almacen, id_ubicacion, id_personal, fec_ingreso, est_ingreso) 
                       VALUES ('$id_compra', '$id_almacen', '$id_ubicacion', '$id_personal', '$fecha_ingreso', 1)";
        
        if (!mysqli_query($con, $sql_ingreso)) {
            throw new Exception("Error al crear el ingreso: " . mysqli_error($con));
        }
        
        $id_ingreso = mysqli_insert_id($con);
        
        // Crear detalle de ingreso
        $sql_detalle = "INSERT INTO ingreso_detalle (id_ingreso, id_producto, cant_ingreso_detalle, est_ingreso_detalle)
                       VALUES ('$id_ingreso', '$id_producto', '$cantidad', 1)";
        
        if (!mysqli_query($con, $sql_detalle)) {
            throw new Exception("Error al crear el detalle del ingreso: " . mysqli_error($con));
        }
        
        // Registrar movimiento
        $fecha_movimiento = date('Y-m-d H:i:s');
        $sql_movimiento = "INSERT INTO movimiento (id_personal, id_orden, id_producto, id_almacen, id_ubicacion, tipo_orden, tipo_movimiento, cant_movimiento, fec_movimiento, est_movimiento)
                          VALUES ('$id_personal', '$id_compra', '$id_producto', '$id_almacen', '$id_ubicacion', 1, 1, '$cantidad', '$fecha_movimiento', 1)";
        
        if (!mysqli_query($con, $sql_movimiento)) {
            throw new Exception("Error al registrar el movimiento: " . mysqli_error($con));
        }
        
        error_log("Nuevo ingreso creado - ID: $id_ingreso, Compra: $id_compra, Producto: $id_producto, Cantidad: $cantidad");
        
        // NUEVO: Verificar si todos los productos de esta compra han sido ingresados completamente
        $sql_check_completo = "SELECT 
                                COUNT(*) as total_productos,
                                SUM(CASE 
                                    WHEN (cd.cant_compra_detalle - COALESCE(ingresado.total_ingresado, 0)) = 0 
                                    THEN 1 ELSE 0 
                                END) as productos_completos
                              FROM compra_detalle cd
                              LEFT JOIN (
                                  SELECT 
                                      id2.id_producto,
                                      SUM(id2.cant_ingreso_detalle) as total_ingresado
                                  FROM ingreso i 
                                  INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                                  WHERE i.id_compra = '$id_compra'
                                  AND i.est_ingreso = 1
                                  AND id2.est_ingreso_detalle = 1
                                  GROUP BY id2.id_producto
                              ) ingresado ON cd.id_producto = ingresado.id_producto
                              WHERE cd.id_compra = '$id_compra'
                              AND cd.est_compra_detalle = 1";
                              
        $res_check_completo = mysqli_query($con, $sql_check_completo);
        
        if (!$res_check_completo) {
            throw new Exception("Error al verificar completitud: " . mysqli_error($con));
        }
        
        $row_completo = mysqli_fetch_assoc($res_check_completo);
        
        // Si todos los productos han sido ingresados, cambiar estado de compra a 3 (Cerrada)
        if ($row_completo && $row_completo['total_productos'] == $row_completo['productos_completos']) {
            $sql_update_compra = "UPDATE compra SET est_compra = 3 WHERE id_compra = '$id_compra'";
            if (!mysqli_query($con, $sql_update_compra)) {
                error_log("Advertencia: No se pudo actualizar estado de compra: " . mysqli_error($con));
            } else {
                error_log(" Compra $id_compra actualizada a estado 3 (Cerrada)");
            }
        }
        
        mysqli_commit($con);
        mysqli_close($con);
        return array('success' => true, 'message' => 'Producto ingresado correctamente', 'id_ingreso' => $id_ingreso);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        error_log("Error en ProcesarIngresoProducto: " . $e->getMessage());
        return array('success' => false, 'message' => $e->getMessage());
    }
}
//-----------------------------------------------------------------------
function ProcesarIngresoTodosProductos($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");
    
    // Obtener todos los productos pendientes
    $productos_pendientes = ObtenerProductosPendientesIngreso($id_compra);
    
    $resultados_exitosos = 0;
    $total_productos = count($productos_pendientes);
    
    foreach ($productos_pendientes as $producto) {
        $resultado = ProcesarIngresoProducto($id_compra, $producto['id_producto'], $producto['cantidad_pendiente'], $id_personal);
        if ($resultado['success']) {
            $resultados_exitosos++;
        }
    }
    
    if ($resultados_exitosos == $total_productos) {
        return array('success' => true, 'message' => "Todos los productos ($total_productos) han sido ingresados correctamente");
    } else {
        return array('success' => false, 'message' => "Solo se ingresaron $resultados_exitosos de $total_productos productos");
    }
}

//-----------------------------------------------------------------------
function VerificarCantidadDisponible($id_compra, $id_producto)
{
    include("../_conexion/conexion.php");
    
    if (!$con) {
        error_log("Error de conexión en VerificarCantidadDisponible");
        return 0;
    }

    $sql = "SELECT 
                cd.cant_compra_detalle,
                COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_ingresada,
                (cd.cant_compra_detalle - COALESCE(SUM(id2.cant_ingreso_detalle), 0)) as cantidad_disponible
            FROM compra_detalle cd
            LEFT JOIN ingreso i ON i.id_compra = '$id_compra'
            LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso AND cd.id_producto = id2.id_producto
            WHERE cd.id_compra = '$id_compra'
            AND cd.id_producto = '$id_producto'
            AND cd.est_compra_detalle = 1
            GROUP BY cd.id_compra_detalle";
            
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en VerificarCantidadDisponible: " . mysqli_error($con));
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return $row ? floatval($row['cantidad_disponible']) : 0;
}
//-----------------------------------------------------------------------
// NUEVAS FUNCIONES PARA INGRESOS DIRECTOS
//-----------------------------------------------------------------------
/**
 * Anula un ingreso directo verificando disponibilidad de stock
 * @param int $id_ingreso ID del ingreso a anular
 * @param int $id_personal ID del personal que ejecuta la anulación
 * @return array Resultado de la operación
 */
function AnularIngresoDirecto($id_ingreso, $id_personal)
{
    include("../_conexion/conexion.php");
    
    try {
        // Iniciar transacción
        mysqli_autocommit($con, false);
        
        // 1. Verificar que el ingreso existe y no está anulado
        $sql_verificar = "SELECT i.*, a.nom_almacen, u.nom_ubicacion 
                         FROM ingreso i 
                         INNER JOIN almacen a ON i.id_almacen = a.id_almacen
                         INNER JOIN ubicacion u ON i.id_ubicacion = u.id_ubicacion
                         WHERE i.id_ingreso = '$id_ingreso' AND i.est_ingreso = 1";
        
        $res_verificar = mysqli_query($con, $sql_verificar);
        
        if (mysqli_num_rows($res_verificar) == 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                'success' => false,
                'message' => 'El ingreso no existe o ya está anulado.'
            ];
        }
        
        $ingreso_info = mysqli_fetch_array($res_verificar, MYSQLI_ASSOC);
        
        // 2. Obtener todos los productos del ingreso
        $sql_productos = "SELECT id.*, p.nom_producto, p.cod_material
                         FROM ingreso_detalle id
                         INNER JOIN producto p ON id.id_producto = p.id_producto
                         WHERE id.id_ingreso = '$id_ingreso' AND id.est_ingreso_detalle = 1";
        
        $res_productos = mysqli_query($con, $sql_productos);
        $productos_ingreso = [];
        
        while ($producto = mysqli_fetch_array($res_productos, MYSQLI_ASSOC)) {
            $productos_ingreso[] = $producto;
        }
        
        // 3. Verificar disponibilidad de stock para cada producto
        $productos_sin_stock = [];
        
        foreach ($productos_ingreso as $producto) {
            $sql_stock = "SELECT 
                            COALESCE(SUM(
                                CASE 
                                    WHEN tipo_movimiento = 1 THEN cant_movimiento 
                                    WHEN tipo_movimiento = 2 THEN -cant_movimiento 
                                    ELSE 0 
                                END
                            ), 0) as stock_actual
                         FROM movimiento 
                         WHERE id_producto = '" . $producto['id_producto'] . "' 
                         AND id_almacen = '" . $ingreso_info['id_almacen'] . "' 
                         AND id_ubicacion = '" . $ingreso_info['id_ubicacion'] . "' 
                         AND est_movimiento = 1";
            
            $res_stock = mysqli_query($con, $sql_stock);
            $stock_data = mysqli_fetch_array($res_stock, MYSQLI_ASSOC);
            $stock_actual = floatval($stock_data['stock_actual']);
            
            if ($stock_actual < $producto['cant_ingreso_detalle']) {
                $productos_sin_stock[] = [
                    'producto' => $producto['nom_producto'],
                    'codigo' => $producto['cod_material'],
                    'cantidad_necesaria' => $producto['cant_ingreso_detalle'],
                    'stock_disponible' => $stock_actual
                ];
            }
        }
        
        // 4. Si hay productos sin stock suficiente, cancelar operación
        if (!empty($productos_sin_stock)) {
            mysqli_rollback($con);
            mysqli_close($con);
            
            $mensaje_error = "No se puede anular el ingreso. Los siguientes productos no tienen stock suficiente:\n\n";
            foreach ($productos_sin_stock as $item) {
                $mensaje_error .= "• {$item['producto']} ({$item['codigo']}): ";
                $mensaje_error .= "Necesario: {$item['cantidad_necesaria']}, Disponible: {$item['stock_disponible']}\n";
            }
            
            return [
                'success' => false,
                'message' => $mensaje_error
            ];
        }
        
        // 5. Proceder con la anulación
        // 5a. Anular movimientos originales del ingreso
        $sql_anular_movimientos = "UPDATE movimiento 
                                   SET est_movimiento = 0 
                                   WHERE id_orden = '$id_ingreso' 
                                   AND tipo_orden = 1 
                                   AND est_movimiento = 1";
        
        if (!mysqli_query($con, $sql_anular_movimientos)) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                'success' => false,
                'message' => 'Error al anular los movimientos del ingreso.'
            ];
        }
        
        // 5b. Anular detalles del ingreso
        $sql_anular_detalles = "UPDATE ingreso_detalle 
                               SET est_ingreso_detalle = 0 
                               WHERE id_ingreso = '$id_ingreso'";
        
        if (!mysqli_query($con, $sql_anular_detalles)) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                'success' => false,
                'message' => 'Error al anular los detalles del ingreso.'
            ];
        }
        
        // 5c. Anular el ingreso principal
        $sql_anular_ingreso = "UPDATE ingreso 
                              SET est_ingreso = 0 
                              WHERE id_ingreso = '$id_ingreso'";
        
        if (!mysqli_query($con, $sql_anular_ingreso)) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                'success' => false,
                'message' => 'Error al anular el ingreso principal.'
            ];
        }
        
        // 6. Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        
        return [
            'success' => true,
            'message' => "Ingreso ING-$id_ingreso anulado exitosamente. Se han actualizado " . count($productos_ingreso) . " productos en el inventario."
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        return [
            'success' => false,
            'message' => 'Error inesperado: ' . $e->getMessage()
        ];
    }
}


/**
 * Obtiene el stock actual de un producto en un almacén y ubicación específicos
 * @param int $id_producto ID del producto
 * @param int $id_almacen ID del almacén
 * @param int $id_ubicacion ID de la ubicación
 * @return float Stock actual
 */
function ObtenerStockActual($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                COALESCE(SUM(
                    CASE 
                        WHEN tipo_movimiento = 1 THEN cant_movimiento 
                        WHEN tipo_movimiento = 2 THEN -cant_movimiento 
                        ELSE 0 
                    END
                ), 0) as stock_actual
            FROM movimiento 
            WHERE id_producto = '$id_producto' 
            AND id_almacen = '$id_almacen' 
            AND id_ubicacion = '$id_ubicacion' 
            AND est_movimiento = 1";
    
    $resultado = mysqli_query($con, $sql);
    
    if ($resultado) {
        $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        mysqli_close($con);
        return floatval($row['stock_actual']);
    }
    
    mysqli_close($con);
    return 0;
}

function ProcesarIngresoDirecto($id_almacen, $id_ubicacion, $id_personal, $productos)
{
    include("../_conexion/conexion.php");
    
    if (!$con) {
        return array('success' => false, 'message' => 'Error de conexión a la base de datos');
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Crear nuevo ingreso SIN orden de compra (id_compra = NULL)
        $fecha_ingreso = date('Y-m-d H:i:s');
        
        $sql_ingreso = "INSERT INTO ingreso (
                            id_compra, id_almacen, id_ubicacion, id_personal, 
                            fec_ingreso, est_ingreso
                        ) VALUES (
                            NULL, '$id_almacen', '$id_ubicacion', '$id_personal', 
                            '$fecha_ingreso', 1
                        )";
        
        if (!mysqli_query($con, $sql_ingreso)) {
            throw new Exception("Error al crear el ingreso: " . mysqli_error($con));
        }
        
        $id_ingreso = mysqli_insert_id($con);
        
        // Procesar cada producto
        foreach ($productos as $producto) {
            $id_producto = intval($producto['id_producto']);
            $cantidad = floatval($producto['cantidad']);
            
            if ($cantidad <= 0) {
                throw new Exception("La cantidad debe ser mayor a 0 para todos los productos");
            }
            
            // Crear detalle de ingreso
            $sql_detalle = "INSERT INTO ingreso_detalle (
                                id_ingreso, id_producto, cant_ingreso_detalle, est_ingreso_detalle
                            ) VALUES (
                                '$id_ingreso', '$id_producto', '$cantidad', 1
                            )";
            
            if (!mysqli_query($con, $sql_detalle)) {
                throw new Exception("Error al crear el detalle del ingreso: " . mysqli_error($con));
            }
            
            // Registrar movimiento de stock
            $fecha_movimiento = date('Y-m-d H:i:s');
            $sql_movimiento = "INSERT INTO movimiento (
                                    id_personal, id_orden, id_producto, id_almacen, 
                                    id_ubicacion, tipo_orden, tipo_movimiento, 
                                    cant_movimiento, fec_movimiento, est_movimiento
                               ) VALUES (
                                    '$id_personal', '$id_ingreso', '$id_producto', '$id_almacen', 
                                    '$id_ubicacion', 1, 1, 
                                    '$cantidad', '$fecha_movimiento', 1
                               )";
            
            if (!mysqli_query($con, $sql_movimiento)) {
                throw new Exception("Error al registrar el movimiento: " . mysqli_error($con));
            }
        }
        
        mysqli_commit($con);
        mysqli_close($con);
        return array('success' => true, 'message' => 'Ingreso directo procesado correctamente', 'id_ingreso' => $id_ingreso);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        mysqli_close($con);
        error_log("Error en ProcesarIngresoDirecto: " . $e->getMessage());
        return array('success' => false, 'message' => $e->getMessage());
    }
}

//-----------------------------------------------------------------------
function ObtenerProductosMateriales()
{
    include("../_conexion/conexion.php");
    
    // Productos tipo MATERIAL: CONSUMIBLES y HERRAMIENTAS 
    $sql = "SELECT 
                p.id_producto,
                p.cod_material,
                p.nom_producto,
                p.mar_producto,
                p.mod_producto,
                um.nom_unidad_medida,
                pt.nom_producto_tipo,
                mt.nom_material_tipo,
                CONCAT(
                    p.nom_producto,
                    CASE WHEN p.mar_producto IS NOT NULL AND p.mar_producto != '' 
                        THEN CONCAT(' - ', p.mar_producto) 
                        ELSE '' 
                    END,
                    CASE WHEN p.mod_producto IS NOT NULL AND p.mod_producto != '' 
                        THEN CONCAT(' (', p.mod_producto, ')') 
                        ELSE '' 
                    END
                ) as descripcion_completa
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            WHERE p.est_producto = 1 
            AND pt.nom_producto_tipo = 'MATERIAL'
            AND mt.nom_material_tipo IN ('CONSUMIBLES', 'HERRAMIENTAS')
            AND mt.est_material_tipo = 1
            ORDER BY p.nom_producto, p.mar_producto";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ObtenerProductosMateriales: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $productos = array();
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $productos[] = $row;
    }
    
    mysqli_close($con);
    return $productos;
}

//-----------------------------------------------------------------------
function MostrarIngresosDirectos()
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                i.id_ingreso,
                i.fec_ingreso,
                i.fpag_ingreso,
                i.est_ingreso,
                a.nom_almacen,
                u.nom_ubicacion,
                p.nom_personal,
                COUNT(id2.id_ingreso_detalle) as total_productos,
                COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_total,
                'INGRESO DIRECTO' as tipo_ingreso
            FROM ingreso i
            INNER JOIN almacen a ON i.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON i.id_ubicacion = u.id_ubicacion
            LEFT JOIN {$bd_complemento}.personal p ON i.id_personal = p.id_personal
            LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso AND id2.est_ingreso_detalle = 1
            WHERE i.id_compra IS NULL  -- Solo ingresos directos
            AND i.est_ingreso = 1
            GROUP BY i.id_ingreso
            ORDER BY i.fec_ingreso DESC";
    
    $resultado = mysqli_query($con, $sql);
    $ingresos = array();
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $ingresos[] = $row;
    }
    
    mysqli_close($con);
    return $ingresos;
}

//-----------------------------------------------------------------------
function ObtenerDetalleIngresoDirecto($id_ingreso)
{
    include("../_conexion/conexion.php");
    
    // Información básica del ingreso directo - INCLUIR AMBOS ESTADOS
    $sql_ingreso = "SELECT 
                        i.id_ingreso,
                        i.fec_ingreso,
                        i.fpag_ingreso,
                        i.est_ingreso,  -- IMPORTANTE: mantener este campo
                        a.nom_almacen,
                        u.nom_ubicacion,
                        p.nom_personal,
                        c.nom_cliente,
                        o.nom_subestacion as nom_obra
                    FROM ingreso i
                    INNER JOIN almacen a ON i.id_almacen = a.id_almacen
                    INNER JOIN ubicacion u ON i.id_ubicacion = u.id_ubicacion
                    INNER JOIN {$bd_complemento}.subestacion o ON a.id_obra = o.id_subestacion
                    INNER JOIN {$bd_complemento}.cliente c ON a.id_cliente = c.id_cliente
                    LEFT JOIN {$bd_complemento}.personal p ON i.id_personal = p.id_personal
                    WHERE i.id_ingreso = '$id_ingreso' 
                    AND i.id_compra IS NULL";  
    // ELIMINAR: AND i.est_ingreso = 1  -- No filtrar por estado aquí
    
    $resultado_ingreso = mysqli_query($con, $sql_ingreso);
    
    if (!$resultado_ingreso) {
        mysqli_close($con);
        return false;
    }
    
    $ingreso = mysqli_fetch_assoc($resultado_ingreso);
    
    if (!$ingreso) {
        mysqli_close($con);
        return false;
    }
    
    // Productos del ingreso directo - INCLUIR TODOS LOS DETALLES
    $sql_productos = "SELECT 
                    id2.id_ingreso_detalle,
                    id2.cant_ingreso_detalle,
                    id2.est_ingreso_detalle,  
                    prod.id_producto,
                    prod.cod_material,
                    prod.nom_producto,
                    prod.mar_producto,
                    prod.mod_producto,
                    um.nom_unidad_medida,
                    mt.nom_material_tipo
                 FROM ingreso_detalle id2
                 INNER JOIN producto prod ON id2.id_producto = prod.id_producto
                 INNER JOIN unidad_medida um ON prod.id_unidad_medida = um.id_unidad_medida
                 INNER JOIN material_tipo mt ON prod.id_material_tipo = mt.id_material_tipo
                 WHERE id2.id_ingreso = '$id_ingreso'
                 ORDER BY prod.nom_producto";
    
    
    $resultado_productos = mysqli_query($con, $sql_productos);
    $productos = array();
    
    while ($row = mysqli_fetch_array($resultado_productos, MYSQLI_ASSOC)) {
        $productos[] = $row;
    }
    
    $resultado = array(
        'ingreso' => $ingreso,
        'productos' => $productos
    );
    
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------

function MostrarTodosLosIngresos()
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                'COMPRA' as tipo,
                c.id_compra as id_orden,
                NULL as id_ingreso,
                c.id_pedido,
                c.fec_compra as fecha,
                c.est_compra as estado,
                p.cod_pedido,
                pr.nom_proveedor as origen,
                pe1.nom_personal as registrado_por,
                pe2.nom_personal as aprobado_tecnica_por,
                pe3.nom_personal as aprobado_financiera_por,
                al.nom_almacen,
                ub.nom_ubicacion,
                mon.nom_moneda,
                (SELECT COUNT(*) FROM compra_detalle cd WHERE cd.id_compra = c.id_compra AND cd.est_compra_detalle = 1) as total_productos,
                COALESCE((SELECT SUM(cd.cant_compra_detalle) 
                         FROM compra_detalle cd 
                         WHERE cd.id_compra = c.id_compra 
                         AND cd.est_compra_detalle = 1), 0) as cantidad_total_pedida,
                COALESCE((SELECT SUM(id2.cant_ingreso_detalle) 
                         FROM ingreso i 
                         INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso 
                         WHERE i.id_compra = c.id_compra 
                         AND i.est_ingreso = 1
                         AND id2.est_ingreso_detalle = 1), 0) as cantidad_total_ingresada,
                COALESCE((SELECT COUNT(DISTINCT id2.id_producto) 
                         FROM ingreso i 
                         INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso 
                         WHERE i.id_compra = c.id_compra
                         AND i.est_ingreso = 1
                         AND id2.est_ingreso_detalle = 1), 0) as productos_ingresados
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            INNER JOIN almacen al ON p.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON p.id_ubicacion = ub.id_ubicacion
            INNER JOIN moneda mon ON c.id_moneda = mon.id_moneda
            LEFT JOIN {$bd_complemento}.personal pe1 ON c.id_personal = pe1.id_personal
            LEFT JOIN {$bd_complemento}.personal pe2 ON c.id_personal_aprueba_tecnica = pe2.id_personal
            LEFT JOIN {$bd_complemento}.personal pe3 ON c.id_personal_aprueba_financiera = pe3.id_personal
            WHERE c.est_compra IN (1, 2, 3, 4)  -- ✅ INCLUYE ESTADO 1 (Pendiente)
            AND c.id_personal_aprueba_tecnica IS NOT NULL
            AND c.id_personal_aprueba_financiera IS NOT NULL
            
            UNION ALL
            
            SELECT 
                'DIRECTO' as tipo,
                NULL as id_orden,
                i.id_ingreso,
                NULL as id_pedido,
                i.fec_ingreso as fecha,
                i.est_ingreso as estado,
                CAST(NULL AS CHAR) as cod_pedido, 
                CONCAT(cl.nom_cliente, ' - ', ob.nom_subestacion) as origen,
                pe.nom_personal as registrado_por,
                NULL as aprobado_tecnica_por,
                NULL as aprobado_financiera_por,
                al.nom_almacen,
                ub.nom_ubicacion,
                'N/A' as nom_moneda,
                COALESCE((SELECT COUNT(*) FROM ingreso_detalle id WHERE id.id_ingreso = i.id_ingreso), 0) as total_productos,
                0 as cantidad_total_pedida,
                COALESCE((SELECT SUM(id2.cant_ingreso_detalle) FROM ingreso_detalle id2 WHERE id2.id_ingreso = i.id_ingreso), 0) as cantidad_total_ingresada,
                COALESCE((SELECT COUNT(*) FROM ingreso_detalle id WHERE id.id_ingreso = i.id_ingreso), 0) as productos_ingresados
            FROM ingreso i
            INNER JOIN almacen al ON i.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON i.id_ubicacion = ub.id_ubicacion
            INNER JOIN {$bd_complemento}.subestacion ob ON al.id_obra = ob.id_subestacion
            INNER JOIN {$bd_complemento}.cliente cl ON al.id_cliente = cl.id_cliente
            LEFT JOIN {$bd_complemento}.personal pe ON i.id_personal = pe.id_personal
            WHERE i.id_compra IS NULL
            AND i.est_ingreso IN (0, 1)
            
            ORDER BY fecha DESC";
    
    $resultado = mysqli_query($con, $sql);
    $todos_ingresos = array();
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $todos_ingresos[] = $row;
    }
    
    mysqli_close($con);
    return $todos_ingresos;
}

//******************************************/
function MostrarIngresosFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    $whereCompras = "";
    $whereDirectos = "";

    if ($fecha_inicio && $fecha_fin) {
        $whereCompras   = " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin' ";
        $whereDirectos  = " AND DATE(i.fec_ingreso) BETWEEN '$fecha_inicio' AND '$fecha_fin' ";
    } else {
        $whereCompras   = " AND DATE(c.fec_compra) = CURDATE() ";
        $whereDirectos  = " AND DATE(i.fec_ingreso) = CURDATE() ";
    }

        $sql = "SELECT 
                'COMPRA' AS tipo,
                c.id_compra AS id_orden,
                (
                    SELECT GROUP_CONCAT(CONCAT('I00', i2.id_ingreso) ORDER BY i2.id_ingreso SEPARATOR ', ')
                    FROM ingreso i2
                    WHERE i2.id_compra = c.id_compra
                    AND i2.est_ingreso = 1
                ) AS cod_ingreso,
                NULL as id_ingreso,
                c.id_pedido,
                c.fec_compra AS fecha,
                c.est_compra AS estado,
                p.cod_pedido,
                pr.nom_proveedor AS origen,
                pe1.nom_personal AS registrado_por,
                pe2.nom_personal AS aprobado_tecnica_por,
                pe3.nom_personal AS aprobado_financiera_por,
                al.nom_almacen,
                ub.nom_ubicacion,
                mon.nom_moneda,
                (SELECT COUNT(*) 
                FROM compra_detalle cd 
                WHERE cd.id_compra = c.id_compra 
                AND cd.est_compra_detalle = 1) AS total_productos,
                COALESCE((
                    SELECT SUM(cd.cant_compra_detalle) 
                    FROM compra_detalle cd 
                    WHERE cd.id_compra = c.id_compra 
                    AND cd.est_compra_detalle = 1
                ), 0) AS cantidad_total_pedida,
                COALESCE((
                    SELECT SUM(id2.cant_ingreso_detalle) 
                    FROM ingreso i 
                    INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso 
                    WHERE i.id_compra = c.id_compra 
                    AND i.est_ingreso = 1
                    AND id2.est_ingreso_detalle = 1
                ), 0) AS cantidad_total_ingresada,
                COALESCE((
                    SELECT COUNT(DISTINCT id2.id_producto) 
                    FROM ingreso i 
                    INNER JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso 
                    WHERE i.id_compra = c.id_compra
                    AND i.est_ingreso = 1
                    AND id2.est_ingreso_detalle = 1
                ), 0) AS productos_ingresados
            FROM compra c
            -- 🔹 quitamos el LEFT JOIN ingreso para evitar duplicados
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            INNER JOIN almacen al ON p.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON p.id_ubicacion = ub.id_ubicacion
            INNER JOIN moneda mon ON c.id_moneda = mon.id_moneda
            LEFT JOIN {$bd_complemento}.personal pe1 ON c.id_personal = pe1.id_personal
            LEFT JOIN {$bd_complemento}.personal pe2 ON c.id_personal_aprueba_tecnica = pe2.id_personal
            LEFT JOIN {$bd_complemento}.personal pe3 ON c.id_personal_aprueba_financiera = pe3.id_personal
            WHERE c.est_compra IN (1, 2, 3, 4) $whereCompras  -- ✅ mantenido igual
            AND c.id_personal_aprueba_tecnica IS NOT NULL     -- ✅ mantenido igual
            AND c.id_personal_aprueba_financiera IS NOT NULL -- ✅ mantenido igual



            UNION ALL

            SELECT 
                'DIRECTO' as tipo,
                NULL as id_orden,
                CONCAT('I00', i.id_ingreso) as cod_ingreso,
                i.id_ingreso,
                NULL as id_pedido,
                i.fec_ingreso as fecha,
                i.est_ingreso as estado,
                CAST(NULL AS CHAR) as cod_pedido, 
                CONCAT(cl.nom_cliente, ' - ', ob.nom_subestacion) as origen,
                pe.nom_personal as registrado_por,
                NULL as aprobado_tecnica_por,
                NULL as aprobado_financiera_por,
                al.nom_almacen,
                ub.nom_ubicacion,
                'N/A' as nom_moneda,
                COALESCE((SELECT COUNT(*) FROM ingreso_detalle id WHERE id.id_ingreso = i.id_ingreso), 0) as total_productos,
                0 as cantidad_total_pedida,
                COALESCE((SELECT SUM(id2.cant_ingreso_detalle) FROM ingreso_detalle id2 WHERE id2.id_ingreso = i.id_ingreso), 0) as cantidad_total_ingresada,
                COALESCE((SELECT COUNT(*) FROM ingreso_detalle id WHERE id.id_ingreso = i.id_ingreso), 0) as productos_ingresados
            FROM ingreso i
            INNER JOIN almacen al ON i.id_almacen = al.id_almacen
            INNER JOIN ubicacion ub ON i.id_ubicacion = ub.id_ubicacion
            INNER JOIN {$bd_complemento}.subestacion ob ON al.id_obra = ob.id_subestacion
            INNER JOIN {$bd_complemento}.cliente cl ON al.id_cliente = cl.id_cliente
            LEFT JOIN {$bd_complemento}.personal pe ON i.id_personal = pe.id_personal
            WHERE i.id_compra IS NULL
              AND i.est_ingreso IN (0, 1)
              $whereDirectos

            ORDER BY fecha DESC";

    $resultado = mysqli_query($con, $sql);
    $todos_ingresos = array();

    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $todos_ingresos[] = $row;
    }

    mysqli_close($con);
    return $todos_ingresos;
}