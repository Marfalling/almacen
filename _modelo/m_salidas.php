<?php
//=======================================================================
// MODELO: m_salidas.php
//=======================================================================

function GrabarSalida($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                     $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                     $fec_req_salida, $obs_salida, $id_personal_encargado, 
                     $id_personal_recibe, $id_personal, $materiales, $id_pedido = null) 
{
    // VALIDACIONES ANTES DE PROCESAR
    $errores_validacion = ValidarSalidaAntesDeProcesar(
        $id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
        $id_almacen_destino, $id_ubicacion_destino, $materiales
    );
    
    if (!empty($errores_validacion)) {
        return "ERROR DE VALIDACIÓN: " . implode(" | ", $errores_validacion);
    }
    
    include("../_conexion/conexion.php");

    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);

    // ✅ NUEVO: Preparar el ID del pedido
    $id_pedido_sql = ($id_pedido && $id_pedido > 0) ? intval($id_pedido) : "NULL";

    // Insertar salida principal
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
        
        // Insertar detalles de salida y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // Verificar stock una vez más antes de cada inserción (por seguridad)
            $stock_actual = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen);
            if ($cantidad > $stock_actual) {
                // Rollback: eliminar la salida creada
                mysqli_query($con, "DELETE FROM salida WHERE id_salida = $id_salida");
                mysqli_close($con);
                return "ERROR: Stock insuficiente para '{$descripcion}'. Stock disponible: {$stock_actual}, solicitado: {$cantidad}";
            }
            
            // Insertar detalle de salida
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Generar 2 movimientos para el traslado
                
                // 1. Movimiento de SALIDA en almacén origen (resta stock)
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
                
                // 2. Movimiento de INGRESO en almacén destino (suma stock)
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
        }

         // Si proviene de un pedido, liberar compromisos de stock
        if ($id_pedido && $id_pedido > 0) {
            $sql_liberar = "UPDATE movimiento 
                            SET est_movimiento = 0 
                            WHERE tipo_orden = 5 
                              AND id_orden = $id_pedido 
                              AND est_movimiento = 1";
            mysqli_query($con, $sql_liberar);
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
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
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             WHERE s.est_salida = 1
             ORDER BY s.fec_salida DESC";

    $resc = mysqli_query($con, $sqlc);
    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
function MostrarSalidasFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    // Condición por defecto → solo fecha actual
    if ($fecha_inicio && $fecha_fin) {
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
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             WHERE s.est_salida = 1
             $whereFecha
             ORDER BY s.fec_salida DESC";

    $resc = mysqli_query($con, $sqlc);
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

    $sqlc = "SELECT s.*, 
                mt.nom_material_tipo,
                ao.nom_almacen as nom_almacen_origen,
                ad.nom_almacen as nom_almacen_destino,
                uo.nom_ubicacion as nom_ubicacion_origen,
                ud.nom_ubicacion as nom_ubicacion_destino,
                pr.nom_personal, 
                pe.nom_personal as nom_encargado,
                prec.nom_personal as nom_recibe
             FROM salida s 
             INNER JOIN material_tipo mt ON s.id_material_tipo = mt.id_material_tipo
             INNER JOIN almacen ao ON s.id_almacen_origen = ao.id_almacen
             INNER JOIN almacen ad ON s.id_almacen_destino = ad.id_almacen
             INNER JOIN ubicacion uo ON s.id_ubicacion_origen = uo.id_ubicacion
             INNER JOIN ubicacion ud ON s.id_ubicacion_destino = ud.id_ubicacion
             INNER JOIN {$bd_complemento}.personal pr ON s.id_personal = pr.id_personal
             LEFT JOIN {$bd_complemento}.personal pe ON s.id_personal_encargado = pe.id_personal
             LEFT JOIN {$bd_complemento}.personal prec ON s.id_personal_recibe = prec.id_personal
             WHERE s.id_salida = $id_salida";
    
    $resc = mysqli_query($con, $sqlc);
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
                um.nom_unidad_medida,
                COALESCE(
                    (SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    INNER JOIN salida sal ON sd.id_salida = sal.id_salida
                    WHERE mov.id_producto = sd.id_producto 
                    AND mov.id_almacen = sal.id_almacen_origen 
                    AND mov.id_ubicacion = sal.id_ubicacion_origen
                    AND mov.est_movimiento = 1), 0
                ) AS cantidad_disponible_origen
             FROM salida_detalle sd 
             INNER JOIN producto p ON sd.id_producto = p.id_producto
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
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
function ActualizarSalida($id_salida, $id_almacen_origen, $id_ubicacion_origen,
                         $id_almacen_destino, $id_ubicacion_destino, $ndoc_salida, 
                         $fec_req_salida, $obs_salida, $id_personal_encargado, 
                         $id_personal_recibe, $materiales) 
{
    include("../_conexion/conexion.php");


    $ndoc_salida = mysqli_real_escape_string($con, $ndoc_salida);
    $fec_req_salida = mysqli_real_escape_string($con, $fec_req_salida);
    $obs_salida = mysqli_real_escape_string($con, $obs_salida);

    // Actualizar salida principal
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

    if (mysqli_query($con, $sql)) {
        
        // Eliminar movimientos anteriores relacionados con esta salida
        $sql_del_mov = "UPDATE movimiento SET est_movimiento = 0 
                        WHERE id_orden = $id_salida AND tipo_orden = 2";
        mysqli_query($con, $sql_del_mov);
        
        // Eliminar detalles anteriores
        $sql_del_det = "UPDATE salida_detalle SET est_salida_detalle = 0 
                        WHERE id_salida = $id_salida";
        mysqli_query($con, $sql_del_det);
        
        // Insertar nuevos detalles y movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            
            // Insertar nuevo detalle
            $sql_detalle = "INSERT INTO salida_detalle (
                                id_salida, id_producto, prod_salida_detalle, 
                                cant_salida_detalle, est_salida_detalle
                            ) VALUES (
                                $id_salida, $id_producto, '$descripcion', 
                                $cantidad, 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Obtener el ID del personal que registra
                $sql_personal = "SELECT id_personal FROM salida WHERE id_salida = $id_salida";
                $res_personal = mysqli_query($con, $sql_personal);
                $row_personal = mysqli_fetch_assoc($res_personal);
                $id_personal = $row_personal['id_personal'];
                
                // Generar nuevos movimientos
                
                // 1. Movimiento de SALIDA en almacén origen (resta stock)
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
                
                // 2. Movimiento de INGRESO en almacén destino (suma stock)
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
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}

//-----------------------------------------------------------------------
function ObtenerStockDisponible($id_producto, $id_almacen, $id_ubicacion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT COALESCE(
                SUM(CASE
                    WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0
            ) AS stock_disponible
            FROM movimiento mov
            WHERE mov.id_producto = $id_producto 
            AND mov.id_almacen = $id_almacen 
            AND mov.id_ubicacion = $id_ubicacion
            AND mov.est_movimiento = 1";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return floatval($row['stock_disponible']);
}


//=======================================================================
// VALIDACIONES PARA SALIDAS - Modelo actualizado m_salidas.php
//=======================================================================

function ValidarSalidaAntesDeProcesar($id_material_tipo, $id_almacen_origen, $id_ubicacion_origen, 
                                     $id_almacen_destino, $id_ubicacion_destino, $materiales) 
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
        $descripcion = $material['descripcion'];
        
        // Obtener stock actual del producto en la ubicación origen
        $stock_disponible = ObtenerStockDisponible($id_producto, $id_almacen_origen, $id_ubicacion_origen);
        
        if ($stock_disponible <= 0) {
            $errores[] = "El producto '{$descripcion}' no tiene stock disponible en la ubicación origen seleccionada.";
        } elseif ($cantidad > $stock_disponible) {
            $errores[] = "La cantidad solicitada para '{$descripcion}' ({$cantidad}) excede el stock disponible ({$stock_disponible}).";
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
                COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0) AS stock_fisico,

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
                COALESCE(SUM(CASE
                    WHEN mov.tipo_movimiento = 1 AND mov.tipo_orden = 1 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                    WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden = 2 AND mov.est_movimiento = 1 THEN -mov.cant_movimiento
                    ELSE 0
                END), 0)
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
    $sql .= " AND mt.id_material_tipo != 1";

    // Filtrar por tipo de material si se especifica y no es "NA"
    if ($tipoMaterial > 0 && $tipoMaterial != 1) {
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