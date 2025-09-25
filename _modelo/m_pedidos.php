<?php
function GrabarPedido($id_producto_tipo, $id_almacen, $id_ubicacion, $nom_pedido, $solicitante, $fecha_necesidad, 
                     $num_ot, $contacto, $lugar_entrega, $aclaraciones, $id_personal, 
                     $materiales, $archivos_subidos) 
{
    include("../_conexion/conexion.php");

    // Obtener información de la obra desde el almacén seleccionado
    $sql_obra = "SELECT id_obra FROM almacen WHERE id_almacen = $id_almacen";
    $resultado_obra = mysqli_query($con, $sql_obra);
    $almacen_data = mysqli_fetch_assoc($resultado_obra);
    $id_obra = $almacen_data['id_obra'];
    
    // Obtener nombre de la obra para generar el código
    $sql_obra_nom = "SELECT nom_obra FROM obra WHERE id_obra = $id_obra";
    $resultado_obra_nom = mysqli_query($con, $sql_obra_nom);
    $obra = mysqli_fetch_assoc($resultado_obra_nom);
    
    // Obtener el siguiente número de pedido para este almacén
    $sql_num = "SELECT COUNT(*) + 1 as siguiente FROM pedido p 
                WHERE p.id_almacen = $id_almacen";
    $resultado_num = mysqli_query($con, $sql_num);
    $num_pedido = mysqli_fetch_assoc($resultado_num)['siguiente'];
    
    $cod_pedido = $obra['nom_obra'] . " " . $num_pedido;

    // Insertar pedido principal - AHORA INCLUYE EL id_ubicacion RECIBIDO COMO PARÁMETRO
    $sql = "INSERT INTO pedido (
                id_producto_tipo, id_almacen, id_ubicacion, id_personal, 
                cod_pedido, nom_pedido, ot_pedido, cel_pedido, lug_pedido, 
                acl_pedido, fec_req_pedido, fec_pedido, est_pedido
            ) VALUES (
                $id_producto_tipo, $id_almacen, $id_ubicacion, $id_personal, 
                '$cod_pedido', '$nom_pedido', '$num_ot', '$contacto', '$lugar_entrega', 
                '$aclaraciones', '$fecha_necesidad', NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_pedido = mysqli_insert_id($con);
        
        foreach ($materiales as $index => $material) {
            $id_producto = intval($material['id_producto']);
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_unidad = intval($material['unidad']); // Este es el ID de la unidad
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst_descripcion = mysqli_real_escape_string($con, $material['sst_descripcion']); 
            
            // OBTENER EL NOMBRE DE LA UNIDAD POR SU ID
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = mysqli_fetch_assoc($resultado_unidad);
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            // GUARDAR SOLO LA DESCRIPCIÓN DE SST
            $requisitos = $sst_descripcion;
            
            // GUARDAR TANTO EL ID COMO EL NOMBRE PARA FACILITAR LA EDICIÓN
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            // MODIFICACIÓN: cant_fin_pedido_detalle ahora es NULL
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
                            $nuevo_nombre = "pedido_" . $id_pedido . "_detalle_" . $id_detalle . "_" . $key . "_" . uniqid() . "." . $extension;
                            
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
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}
//-----------------------------------------------------------------------
function MostrarPedidos()
{
    include("../_conexion/conexion.php");

    // VERSIÓN CORREGIDA: Usar LEFT JOIN en lugar de INNER JOIN
    // para evitar que se pierdan pedidos por datos faltantes
    $sqlc = "SELECT p.*, 
                COALESCE(ob.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM compra comp 
                        WHERE comp.id_pedido = p.id_pedido 
                        AND comp.est_compra <> 0
                    ) THEN 2 
                    ELSE p.est_pedido
                END AS est_pedido_calc,
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
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen AND a.est_almacen = 1
             LEFT JOIN obra ob ON a.id_obra = ob.id_obra AND ob.est_obra = 1
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente AND c.est_cliente = 1
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion AND u.est_ubicacion = 1
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal AND pr.est_personal = 1
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo AND pt.est_producto_tipo = 1
             WHERE p.est_pedido = 1
             ORDER BY p.fec_pedido DESC";

    $resc = mysqli_query($con, $sqlc);
    
    // Agregar manejo de errores
    if (!$resc) {
        error_log("Error en MostrarPedidos(): " . mysqli_error($con));
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


//-----------------------------------------------------------------------
function ConsultarPedido($id_pedido)
{
    include("../_conexion/conexion.php");

    // CORREGIDO: Usar LEFT JOIN para evitar pérdida de datos
    $sqlc = "SELECT p.*, 
                COALESCE(ob.nom_obra, 'N/A') as nom_obra,
                COALESCE(c.nom_cliente, 'N/A') as nom_cliente,
                COALESCE(u.nom_ubicacion, 'N/A') as nom_ubicacion,
                COALESCE(a.nom_almacen, 'N/A') as nom_almacen,
                COALESCE(pr.nom_personal, 'Sin asignar') as nom_personal,
                COALESCE(pr.ape_personal, '') as ape_personal,
                COALESCE(pt.nom_producto_tipo, 'N/A') as nom_producto_tipo
             FROM pedido p 
             LEFT JOIN almacen a ON p.id_almacen = a.id_almacen
             LEFT JOIN obra ob ON a.id_obra = ob.id_obra
             LEFT JOIN cliente c ON a.id_cliente = c.id_cliente
             LEFT JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             LEFT JOIN personal pr ON p.id_personal = pr.id_personal
             LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             WHERE p.id_pedido = ? AND p.est_pedido = 1";
    
    // Usar prepared statement para seguridad
    $stmt = mysqli_prepare($con, $sqlc);
    mysqli_stmt_bind_param($stmt, "i", $id_pedido);
    mysqli_stmt_execute($stmt);
    $resc = mysqli_stmt_get_result($stmt);
    
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
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
function ActualizarPedido($id_pedido, $id_ubicacion, $nom_pedido, $fecha_necesidad, $num_ot, 
                         $contacto, $lugar_entrega, $aclaraciones, $materiales, $archivos_subidos) 
{
    include("../_conexion/conexion.php");

    // Actualizar pedido principal - AHORA INCLUYE id_ubicacion
    $sql = "UPDATE pedido SET 
            id_ubicacion = $id_ubicacion,
            nom_pedido = '$nom_pedido',
            fec_req_pedido = '$fecha_necesidad',
            ot_pedido = '$num_ot',
            cel_pedido = '$contacto',
            lug_pedido = '$lugar_entrega',
            acl_pedido = '$aclaraciones'
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
            $unidad_data = mysqli_fetch_assoc($resultado_unidad);
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
                }
            }
            
            // MANEJO MEJORADO DE ARCHIVOS - REEMPLAZAR ARCHIVOS EXISTENTES
            if (isset($archivos_subidos[$index]) && !empty($archivos_subidos[$index]['name'][0])) {
                
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
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
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

function ConsultarCompra($id_pedido){
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.*,
                p.nom_proveedor
            FROM compra c
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            WHERE id_pedido = $id_pedido";
    $resc = mysqli_query($con, $sql);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

function CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, $observacion, $direccion, $plazo_entrega, $porte, $fecha_orden, $items) 
{
    include("../_conexion/conexion.php");

    $sql = "INSERT INTO compra (
                id_pedido, id_proveedor, id_moneda, id_personal, id_personal_aprueba, obs_compra, denv_compra, plaz_compra, port_compra, fec_compra, est_compra
            ) VALUES (
                $id_pedido, $proveedor, $moneda, $id_personal, NULL, '$observacion', '$direccion', '$plazo_entrega', '$porte', NOW(), 1
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
        mysqli_close($con);
        return "ERROR: " . mysqli_error($con);
    }
}

//-----------------------------------------------------------------------
function ActualizarVerificacionPedido($id_pedido, $id_personal_verifica, $nuevo_estado) {
    include("../_conexion/conexion.php");

    $sql = "UPDATE pedido 
            SET id_personal_verifica = ?, 
                est_pedido = ? 
            WHERE id_pedido = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $id_personal_verifica, $nuevo_estado, $id_pedido);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $ok ? "SI" : "NO";
}
?>