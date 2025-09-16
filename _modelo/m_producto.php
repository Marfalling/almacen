<?php
//=======================================================================
// FUNCIONES PARA PRODUCTO
//=======================================================================

//-----------------------------------------------------------------------
function MostrarProducto() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT p.*, 
                    pt.nom_producto_tipo,
                    mt.nom_material_tipo,
                    um.nom_unidad_medida
             FROM producto p
             INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
             ORDER BY p.nom_producto ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarProductoActivos() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT p.*, 
                    pt.nom_producto_tipo,
                    mt.nom_material_tipo,
                    um.nom_unidad_medida
             FROM producto p
             INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
             WHERE p.est_producto = 1
             ORDER BY p.nom_producto ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function GrabarProducto($id_producto_tipo, $id_material_tipo, $id_unidad_medida, $cod_material, $nom_producto, 
                       $nser_producto, $mod_producto, $mar_producto, $det_producto, $fuc_producto, 
                       $fpc_producto, $dcal_producto, $fuo_producto, $fpo_producto, $dope_producto, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un producto con el mismo código de material
    if (!empty($cod_material)) {
        $sqlv = "SELECT COUNT(*) as total FROM producto WHERE cod_material = '$cod_material'";
        $resv = mysqli_query($con, $sqlv);
        $fila = mysqli_fetch_assoc($resv);
        
        if ($fila['total'] > 0) {
            mysqli_close($con);
            return "NO"; // Ya existe
        }
    }
    
    // Preparar valores para fechas (NULL si están vacías)
    $fuc_producto = !empty($fuc_producto) ? "'$fuc_producto'" : "NULL";
    $fpc_producto = !empty($fpc_producto) ? "'$fpc_producto'" : "NULL";
    $fuo_producto = !empty($fuo_producto) ? "'$fuo_producto'" : "NULL";
    $fpo_producto = !empty($fpo_producto) ? "'$fpo_producto'" : "NULL";
    
    // Insertar nuevo producto
    $sqli = "INSERT INTO producto (
                id_producto_tipo, id_material_tipo, id_unidad_medida, cod_material, nom_producto, 
                nser_producto, mod_producto, mar_producto, det_producto, fuc_producto, 
                fpc_producto, dcal_producto, fuo_producto, fpo_producto, dope_producto, est_producto
            ) VALUES (
                $id_producto_tipo, $id_material_tipo, $id_unidad_medida, '$cod_material', '$nom_producto',
                '$nser_producto', '$mod_producto', '$mar_producto', '$det_producto', $fuc_producto,
                $fpc_producto, '$dcal_producto', $fuo_producto, $fpo_producto, '$dope_producto', $est
            )";
    
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ActualizarProducto($id_producto, $id_producto_tipo, $id_material_tipo, $id_unidad_medida, $cod_material, $nom_producto, 
                           $nser_producto, $mod_producto, $mar_producto, $det_producto, $fuc_producto, 
                           $fpc_producto, $dcal_producto, $fuo_producto, $fpo_producto, $dope_producto, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro producto con el mismo código de material
    if (!empty($cod_material)) {
        $sqlv = "SELECT COUNT(*) as total FROM producto WHERE cod_material = '$cod_material' AND id_producto != $id_producto";
        $resv = mysqli_query($con, $sqlv);
        $fila = mysqli_fetch_assoc($resv);
        
        if ($fila['total'] > 0) {
            mysqli_close($con);
            return "NO"; // Ya existe
        }
    }
    
    // Preparar valores para fechas (NULL si están vacías)
    $fuc_producto = !empty($fuc_producto) ? "'$fuc_producto'" : "NULL";
    $fpc_producto = !empty($fpc_producto) ? "'$fpc_producto'" : "NULL";
    $fuo_producto = !empty($fuo_producto) ? "'$fuo_producto'" : "NULL";
    $fpo_producto = !empty($fpo_producto) ? "'$fpo_producto'" : "NULL";
    
    // Actualizar producto
    $sqlu = "UPDATE producto SET 
                id_producto_tipo = $id_producto_tipo,
                id_material_tipo = $id_material_tipo,
                id_unidad_medida = $id_unidad_medida,
                cod_material = '$cod_material',
                nom_producto = '$nom_producto',
                nser_producto = '$nser_producto',
                mod_producto = '$mod_producto',
                mar_producto = '$mar_producto',
                det_producto = '$det_producto',
                fuc_producto = $fuc_producto,
                fpc_producto = $fpc_producto,
                dcal_producto = '$dcal_producto',
                fuo_producto = $fuo_producto,
                fpo_producto = $fpo_producto,
                dope_producto = '$dope_producto',
                est_producto = $est
             WHERE id_producto = $id_producto";
    
    $resu = mysqli_query($con, $sqlu);
    
    mysqli_close($con);
    
    if ($resu) {
        return "SI";
    } else {
        return "ERROR";
    }
}

function ObtenerProductoPorId($id) {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT p.*, 
                    pt.nom_producto_tipo,
                    mt.nom_material_tipo,
                    um.nom_unidad_medida
             FROM producto p
             INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
             INNER JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
             INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
             WHERE p.id_producto = $id";
    $resc = mysqli_query($con, $sqlc);
    $resultado = mysqli_fetch_array($resc, MYSQLI_ASSOC);
    mysqli_close($con);
    return $resultado;
}


//-----------------------------------------------------------------------11/09/2025

//-----------------------------------------------------------------------
function NumeroRegistrosTotalProductos($tipoPedido = 0) {
    include("../_conexion/conexion.php");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de pedido si se especifica
    if ($tipoPedido > 0) {
        $whereClause .= " AND p.id_producto_tipo = $tipoPedido";
    }
    
    $sql = "SELECT COUNT(*) as total 
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    
    return $row['total'];
}

function NumeroRegistrosFiltradosModalProductos($search, $tipoPedido = 0) {
    include("../_conexion/conexion.php");
    mysqli_set_charset($con, "utf8");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de pedido si se especifica
    if ($tipoPedido > 0) {
        $whereClause .= " AND p.id_producto_tipo = $tipoPedido";
    }
    
    if (!empty($search)) {
        $searchTerm = mysqli_real_escape_string($con, $search);
        $whereClause .= " AND (
            p.cod_material LIKE '%$searchTerm%' OR
            p.nom_producto LIKE '%$searchTerm%' OR
            pt.nom_producto_tipo LIKE '%$searchTerm%' OR
            um.nom_unidad_medida LIKE '%$searchTerm%' OR
            p.mar_producto LIKE '%$searchTerm%' OR
            p.mod_producto LIKE '%$searchTerm%'
        )";
    }
    
    $sql = "SELECT COUNT(*) as total 
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    
    return $row['total'];
}

function MostrarProductoMejoradoModal($limit, $offset, $search, $orderColumn, $orderDirection, $startIndex, $tipoPedido = 0) {
    include("../_conexion/conexion.php");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de pedido si se especifica
    if ($tipoPedido > 0) {
        $whereClause .= " AND p.id_producto_tipo = $tipoPedido";
    }
    
    if (!empty($search)) {
        $searchTerm = mysqli_real_escape_string($con, $search);
        $whereClause .= " AND (
            p.cod_material LIKE '%$searchTerm%' OR
            p.nom_producto LIKE '%$searchTerm%' OR
            pt.nom_producto_tipo LIKE '%$searchTerm%' OR
            um.nom_unidad_medida LIKE '%$searchTerm%' OR
            p.mar_producto LIKE '%$searchTerm%' OR
            p.mod_producto LIKE '%$searchTerm%'
        )";
    }
    
    $columnMap = [
        'cod_material' => 'p.cod_material',
        'nom_producto' => 'p.nom_producto',
        'nom_producto_tipo' => 'pt.nom_producto_tipo',
        'nom_unidad_medida' => 'um.nom_unidad_medida',
        'mar_producto' => 'p.mar_producto',
        'mod_producto' => 'p.mod_producto'
    ];
    
    $orderBy = isset($columnMap[$orderColumn]) ? $columnMap[$orderColumn] : 'p.cod_material';
    $orderDirection = ($orderDirection === 'desc') ? 'DESC' : 'ASC';
    
    $sql = "SELECT 
                p.id_producto,
                p.cod_material,
                p.nom_producto,
                pt.nom_producto_tipo,
                um.nom_unidad_medida,
                um.id_unidad_medida,
                p.mar_producto,
                p.mod_producto,
                p.det_producto
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause
            ORDER BY $orderBy $orderDirection
            LIMIT $limit OFFSET $offset";
    
    $resultado = mysqli_query($con, $sql);
    $data = [];
    $counter = $startIndex;
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $boton = '<button type="button" class="btn btn-success btn-select-producto" 
                    onclick="seleccionarProducto(' . $fila['id_producto'] . ', \'' . 
                    htmlspecialchars($fila['nom_producto'], ENT_QUOTES) . '\', ' . 
                    $fila['id_unidad_medida'] . ', \'' . 
                    htmlspecialchars($fila['nom_unidad_medida'], ENT_QUOTES) . '\')" 
                    title="Seleccionar producto">
                    <i class="fa fa-check"></i>
                 </button>';
        
        $data[] = [
            $fila['cod_material'] ?: 'N/A',
            $fila['nom_producto'] ?: 'N/A',
            $fila['nom_producto_tipo'] ?: 'N/A',
            $fila['nom_unidad_medida'] ?: 'N/A',
            $fila['mar_producto'] ?: 'N/A',
            $fila['mod_producto'] ?: 'N/A',
            $boton
        ];
        $counter++;
    }
    
    mysqli_close($con);
    return $data;
}

//-----------------------------------------------------------------------
// FUNCIONES PARA MODAL DE PRODUCTOS CON STOCK
//-----------------------------------------------------------------------

function NumeroRegistrosTotalProductosConStock($id_almacen, $id_ubicacion, $tipoMaterial = 0) {
    include("../_conexion/conexion.php");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de material si se especifica
    if ($tipoMaterial > 0) {
        $whereClause .= " AND p.id_material_tipo = $tipoMaterial";
    }
    
    $sql = "SELECT COUNT(*) as total 
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    
    return $row['total'];
}

function NumeroRegistrosFiltradosProductosConStock($search, $id_almacen, $id_ubicacion, $tipoMaterial = 0) {
    include("../_conexion/conexion.php");
    mysqli_set_charset($con, "utf8");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de material si se especifica
    if ($tipoMaterial > 0) {
        $whereClause .= " AND p.id_material_tipo = $tipoMaterial";
    }
    
    if (!empty($search)) {
        $searchTerm = mysqli_real_escape_string($con, $search);
        $whereClause .= " AND (
            p.cod_material LIKE '%$searchTerm%' OR
            p.nom_producto LIKE '%$searchTerm%' OR
            pt.nom_producto_tipo LIKE '%$searchTerm%' OR
            um.nom_unidad_medida LIKE '%$searchTerm%' OR
            p.mar_producto LIKE '%$searchTerm%' OR
            p.mod_producto LIKE '%$searchTerm%'
        )";
    }
    
    $sql = "SELECT COUNT(*) as total 
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause";
    
    $resultado = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    
    return $row['total'];
}

function MostrarProductosConStock($limit, $offset, $search, $orderColumn, $orderDirection, $id_almacen, $id_ubicacion, $tipoMaterial = 0) {
    include("../_conexion/conexion.php");
    
    $whereClause = "WHERE p.est_producto = 1";
    
    // Agregar filtro por tipo de material si se especifica
    if ($tipoMaterial > 0) {
        $whereClause .= " AND p.id_material_tipo = $tipoMaterial";
    }
    
    if (!empty($search)) {
        $searchTerm = mysqli_real_escape_string($con, $search);
        $whereClause .= " AND (
            p.cod_material LIKE '%$searchTerm%' OR
            p.nom_producto LIKE '%$searchTerm%' OR
            pt.nom_producto_tipo LIKE '%$searchTerm%' OR
            um.nom_unidad_medida LIKE '%$searchTerm%' OR
            p.mar_producto LIKE '%$searchTerm%' OR
            p.mod_producto LIKE '%$searchTerm%'
        )";
    }
    
    $columnMap = [
        'cod_material' => 'p.cod_material',
        'nom_producto' => 'p.nom_producto',
        'nom_producto_tipo' => 'pt.nom_producto_tipo',
        'nom_unidad_medida' => 'um.nom_unidad_medida',
        'mar_producto' => 'p.mar_producto',
        'mod_producto' => 'p.mod_producto',
        'stock_disponible' => 'stock_disponible'
    ];
    
    $orderBy = isset($columnMap[$orderColumn]) ? $columnMap[$orderColumn] : 'p.nom_producto';
    $orderDirection = ($orderDirection === 'desc') ? 'DESC' : 'ASC';
    
    $sql = "SELECT 
                p.id_producto,
                p.cod_material,
                p.nom_producto,
                pt.nom_producto_tipo,
                um.nom_unidad_medida,
                p.mar_producto,
                p.mod_producto,
                COALESCE(
                    (SELECT SUM(CASE
                        WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento
                        WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                        ELSE 0
                    END)
                    FROM movimiento mov
                    WHERE mov.id_producto = p.id_producto 
                    AND mov.id_almacen = $id_almacen 
                    AND mov.id_ubicacion = $id_ubicacion
                    AND mov.est_movimiento = 1), 0
                ) AS stock_disponible
            FROM producto p
            INNER JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            INNER JOIN unidad_medida um ON p.id_unidad_medida = um.id_unidad_medida
            $whereClause
            ORDER BY $orderBy $orderDirection
            LIMIT $limit OFFSET $offset";
    
    $resultado = mysqli_query($con, $sql);
    $data = [];
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $data[] = $fila;
    }
    
    mysqli_close($con);
    return $data;
}
?>