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
                    pe2.nom_personal as aprobado_por
                FROM compra c
                INNER JOIN pedido p ON c.id_pedido = p.id_pedido
                INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
                INNER JOIN almacen al ON p.id_almacen = al.id_almacen
                LEFT JOIN personal pe1 ON c.id_personal = pe1.id_personal
                LEFT JOIN personal pe2 ON c.id_personal_aprueba = pe2.id_personal
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
                        i.fec_ingreso,
                        COALESCE(p.nom_personal, 'Usuario') as nom_personal,
                        COUNT(DISTINCT id2.id_ingreso_detalle) as productos_count,
                        COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_total
                    FROM ingreso i
                    LEFT JOIN personal p ON i.id_personal = p.id_personal
                    LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso
                    WHERE i.id_compra = '$id_compra'
                    GROUP BY i.id_ingreso
                    ORDER BY i.fec_ingreso DESC";
                        
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
                pe2.nom_personal as aprobado_por,
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
            LEFT JOIN personal pe1 ON c.id_personal = pe1.id_personal
            LEFT JOIN personal pe2 ON c.id_personal_aprueba = pe2.id_personal
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
function ObtenerProductosPendientesIngreso($id_compra)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                cd.id_compra_detalle,
                cd.id_producto,
                cd.cant_compra_detalle,
                pd.prod_pedido_detalle as nom_producto, -- CAMBIADO: ahora usa la descripción del pedido
                prod.cod_material,
                um.nom_unidad_medida,
                COALESCE(SUM(id2.cant_ingreso_detalle), 0) as cantidad_ingresada,
                (cd.cant_compra_detalle - COALESCE(SUM(id2.cant_ingreso_detalle), 0)) as cantidad_pendiente
            FROM compra_detalle cd
            INNER JOIN compra c ON cd.id_compra = c.id_compra -- JOIN con compra
            INNER JOIN pedido_detalle pd ON cd.id_producto = pd.id_producto 
                AND pd.id_pedido = c.id_pedido -- JOIN con pedido_detalle usando la relación correcta
                AND pd.est_pedido_detalle IN (1, 2) -- Solo detalles activos o en proceso
            INNER JOIN producto prod ON cd.id_producto = prod.id_producto
            INNER JOIN unidad_medida um ON prod.id_unidad_medida = um.id_unidad_medida
            LEFT JOIN ingreso i ON i.id_compra = '$id_compra'
            LEFT JOIN ingreso_detalle id2 ON i.id_ingreso = id2.id_ingreso AND cd.id_producto = id2.id_producto
            WHERE cd.id_compra = '$id_compra'
            AND cd.est_compra_detalle = 1
            GROUP BY cd.id_compra_detalle, cd.id_producto, pd.prod_pedido_detalle
            HAVING cantidad_pendiente > 0
            ORDER BY pd.prod_pedido_detalle"; 
            
    $resultado = mysqli_query($con, $sql);
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
    
    // Verificar conexión
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
        
        // Verificar si ya existe un ingreso para esta compra
        $sql_check_ingreso = "SELECT id_ingreso FROM ingreso WHERE id_compra = '$id_compra'";
        $res_check = mysqli_query($con, $sql_check_ingreso);
        
        if (!$res_check) {
            throw new Exception("Error al verificar ingreso existente: " . mysqli_error($con));
        }
        
        if (mysqli_num_rows($res_check) > 0) {
            $row_ingreso = mysqli_fetch_assoc($res_check);
            $id_ingreso = $row_ingreso['id_ingreso'];
        } else {
            // Crear nuevo ingreso
            $fecha_ingreso = date('Y-m-d H:i:s');
            $sql_ingreso = "INSERT INTO ingreso (id_compra, id_almacen, id_ubicacion, id_personal, fec_ingreso, est_ingreso) 
                           VALUES ('$id_compra', '$id_almacen', '$id_ubicacion', '$id_personal', '$fecha_ingreso', 1)";
            
            if (!mysqli_query($con, $sql_ingreso)) {
                throw new Exception("Error al crear el ingreso: " . mysqli_error($con));
            }
            
            $id_ingreso = mysqli_insert_id($con);
        }
        
        // Verificar si ya existe un detalle de ingreso para este producto
        $sql_check_detalle = "SELECT id_ingreso_detalle, cant_ingreso_detalle 
                             FROM ingreso_detalle 
                             WHERE id_ingreso = '$id_ingreso' AND id_producto = '$id_producto'";
        
        $res_check_detalle = mysqli_query($con, $sql_check_detalle);
        
        if (!$res_check_detalle) {
            throw new Exception("Error al verificar detalle existente: " . mysqli_error($con));
        }
        
        if (mysqli_num_rows($res_check_detalle) > 0) {
            // Actualizar cantidad existente
            $row_detalle = mysqli_fetch_assoc($res_check_detalle);
            $nueva_cantidad = $row_detalle['cant_ingreso_detalle'] + $cantidad;
            $id_ingreso_detalle = $row_detalle['id_ingreso_detalle'];
            
            $sql_update_detalle = "UPDATE ingreso_detalle 
                                  SET cant_ingreso_detalle = '$nueva_cantidad'
                                  WHERE id_ingreso_detalle = '$id_ingreso_detalle'";
            
            if (!mysqli_query($con, $sql_update_detalle)) {
                throw new Exception("Error al actualizar el detalle del ingreso: " . mysqli_error($con));
            }
        } else {
            // Crear nuevo detalle de ingreso
            $sql_detalle = "INSERT INTO ingreso_detalle (id_ingreso, id_producto, cant_ingreso_detalle, est_ingreso_detalle)
                           VALUES ('$id_ingreso', '$id_producto', '$cantidad', 1)";
            
            if (!mysqli_query($con, $sql_detalle)) {
                throw new Exception("Error al crear el detalle del ingreso: " . mysqli_error($con));
            }
        }
        
        // CORRECCIÓN: Registrar movimiento con los tipos correctos
        // Para INGRESO por orden de compra:
        // tipo_orden = 1 (INGRESO)
        // tipo_movimiento = 1 (suma al stock)
        $fecha_movimiento = date('Y-m-d H:i:s');
        $sql_movimiento = "INSERT INTO movimiento (id_personal, id_orden, id_producto, id_almacen, id_ubicacion, tipo_orden, tipo_movimiento, cant_movimiento, fec_movimiento, est_movimiento)
                          VALUES ('$id_personal', '$id_compra', '$id_producto', '$id_almacen', '$id_ubicacion', 1, 1, '$cantidad', '$fecha_movimiento', 1)";
        
        if (!mysqli_query($con, $sql_movimiento)) {
            throw new Exception("Error al registrar el movimiento: " . mysqli_error($con));
        }
        
        // Log para debug
        error_log("Movimiento registrado - Compra: $id_compra, Producto: $id_producto, Cantidad: $cantidad, tipo_orden: 1, tipo_movimiento: 1");
        
        // Verificar si todos los productos han sido ingresados completamente
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
                                  GROUP BY id2.id_producto
                              ) ingresado ON cd.id_producto = ingresado.id_producto
                              WHERE cd.id_compra = '$id_compra'
                              AND cd.est_compra_detalle = 1";
                              
        $res_check_completo = mysqli_query($con, $sql_check_completo);
        
        if (!$res_check_completo) {
            throw new Exception("Error al verificar completitud: " . mysqli_error($con));
        }
        
        $row_completo = mysqli_fetch_assoc($res_check_completo);
        
        // Si todos los productos han sido ingresados completamente, cambiar estado de compra a 3
        if ($row_completo && $row_completo['total_productos'] == $row_completo['productos_completos']) {
            $sql_update_compra = "UPDATE compra SET est_compra = 3 WHERE id_compra = '$id_compra'";
            if (!mysqli_query($con, $sql_update_compra)) {
                // Log el error pero no fallar la transacción
                error_log("Advertencia: No se pudo actualizar estado de compra: " . mysqli_error($con));
            } else {
                error_log("Estado de compra actualizado a 3 (cerrado) para compra: $id_compra");
            }
        }
        
        mysqli_commit($con);
        mysqli_close($con);
        return array('success' => true, 'message' => 'Producto ingresado correctamente');
        
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

?>

