<?php
function GrabarPedido($id_producto_tipo, $id_almacen, $nom_pedido, $solicitante, $fecha_necesidad, 
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

    // Insertar pedido principal - USA EL ID_PRODUCTO_TIPO CORRECTO
    $sql = "INSERT INTO pedido (
                id_producto_tipo, id_almacen, id_ubicacion, id_personal, 
                cod_pedido, nom_pedido, ot_pedido, cel_pedido, lug_pedido, 
                acl_pedido, fec_req_pedido, fec_pedido, est_pedido
            ) VALUES (
                $id_producto_tipo, $id_almacen, 1, $id_personal, 
                '$cod_pedido', '$nom_pedido', '$num_ot', '$contacto', '$lugar_entrega', 
                '$aclaraciones', '$fecha_necesidad', NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_pedido = mysqli_insert_id($con);
        
        // Insertar detalles del pedido
        foreach ($materiales as $index => $material) {
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $id_unidad = intval($material['unidad']); // Este es el ID de la unidad
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst = mysqli_real_escape_string($con, $material['sst']);
            $ma = mysqli_real_escape_string($con, $material['ma']);
            $ca = mysqli_real_escape_string($con, $material['ca']);
            
            // OBTENER EL NOMBRE DE LA UNIDAD POR SU ID
            $sql_unidad = "SELECT nom_unidad_medida FROM unidad_medida WHERE id_unidad_medida = $id_unidad";
            $resultado_unidad = mysqli_query($con, $sql_unidad);
            $unidad_data = mysqli_fetch_assoc($resultado_unidad);
            $nombre_unidad = $unidad_data ? $unidad_data['nom_unidad_medida'] : '';
            
            $requisitos = "SST: $sst | MA: $ma | CA: $ca";
            
            // GUARDAR TANTO EL ID COMO EL NOMBRE PARA FACILITAR LA EDICIÓN
            $comentario_detalle = "Unidad: $nombre_unidad | Unidad ID: $id_unidad | Obs: $observaciones";
            
            $sql_detalle = "INSERT INTO pedido_detalle (
                                id_pedido, id_producto, prod_pedido_detalle, 
                                cant_pedido_detalle, cant_fin_pedido_detalle, 
                                com_pedido_detalle, req_pedido, est_pedido_detalle
                            ) VALUES (
                                $id_pedido, 1, '$descripcion', 
                                $cantidad, $cantidad, 
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

    $sqlc = "SELECT p.*, ob.nom_obra, c.nom_cliente, pr.nom_personal, pr.ape_personal, a.nom_almacen, u.nom_ubicacion
             FROM pedido p 
             INNER JOIN almacen a ON p.id_almacen = a.id_almacen
                INNER JOIN obra ob ON a.id_obra = ob.id_obra
                INNER JOIN cliente c ON a.id_cliente = c.id_cliente
             INNER JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             INNER JOIN personal pr ON p.id_personal = pr.id_personal
             ORDER BY p.fec_pedido DESC";
    $resc = mysqli_query($con, $sqlc);

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

    $sqlc = "SELECT p.*, ob.nom_obra, c.nom_cliente, u.nom_ubicacion, a.nom_almacen, pr.nom_personal, pr.ape_personal
             FROM pedido p 
             INNER JOIN almacen a ON p.id_almacen = a.id_almacen
                INNER JOIN obra ob ON a.id_obra = ob.id_obra
                INNER JOIN cliente c ON a.id_cliente = c.id_cliente
             INNER JOIN ubicacion u ON p.id_ubicacion = u.id_ubicacion
             INNER JOIN personal pr ON p.id_personal = pr.id_personal
             WHERE p.id_pedido = '$id_pedido'";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarPedidoDetalle($id_pedido)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT pd.*, 
             GROUP_CONCAT(pdd.nom_pedido_detalle_documento) as archivos
             FROM pedido_detalle pd 
             LEFT JOIN pedido_detalle_documento pdd ON pd.id_pedido_detalle = pdd.id_pedido_detalle
             WHERE pd.id_pedido = $id_pedido AND pd.est_pedido_detalle = 1
             GROUP BY pd.id_pedido_detalle
             ORDER BY pd.id_pedido_detalle";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ActualizarPedido($id_pedido, $nom_pedido, $fecha_necesidad, $num_ot, 
                         $contacto, $lugar_entrega, $aclaraciones, $materiales, $archivos_subidos) 
{
    include("../_conexion/conexion.php");

    // Actualizar pedido principal
    $sql = "UPDATE pedido SET 
            nom_pedido = '$nom_pedido',
            fec_req_pedido = '$fecha_necesidad',
            ot_pedido = '$num_ot',
            cel_pedido = '$contacto',
            lug_pedido = '$lugar_entrega',
            acl_pedido = '$aclaraciones'
            WHERE id_pedido = $id_pedido";

    if (mysqli_query($con, $sql)) {
        // Marcar detalles existentes como inactivos
        $sql_inactivos = "UPDATE pedido_detalle SET est_pedido_detalle = 0 WHERE id_pedido = $id_pedido";
        mysqli_query($con, $sql_inactivos);
        
        // Insertar nuevos detalles
        foreach ($materiales as $index => $material) {
            $descripcion = mysqli_real_escape_string($con, $material['descripcion']);
            $cantidad = floatval($material['cantidad']);
            $unidad = mysqli_real_escape_string($con, $material['unidad']);
            $observaciones = mysqli_real_escape_string($con, $material['observaciones']);
            $sst = mysqli_real_escape_string($con, $material['sst']);
            $ma = mysqli_real_escape_string($con, $material['ma']);
            $ca = mysqli_real_escape_string($con, $material['ca']);
            
            $requisitos = "SST: $sst | MA: $ma | CA: $ca";
            
            $sql_detalle = "INSERT INTO pedido_detalle (
                                id_pedido, id_producto, prod_pedido_detalle, 
                                cant_pedido_detalle, cant_fin_pedido_detalle, 
                                com_pedido_detalle, req_pedido, est_pedido_detalle
                            ) VALUES (
                                $id_pedido, 1, '$descripcion', 
                                $cantidad, $cantidad, 
                                'Unidad: $unidad | Obs: $observaciones', '$requisitos', 1
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
?>