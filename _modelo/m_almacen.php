<?php
//-----------------------------------------------------------------------
function GrabarAlmacen($id_cliente, $id_obra, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un almacén con el mismo nombre para el mismo cliente y obra
    $sql_verificar = "SELECT COUNT(*) as total FROM almacen WHERE nom_almacen = '$nom' AND id_cliente = $id_cliente AND id_obra = $id_obra";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo almacén
    $sql = "INSERT INTO almacen (id_cliente, id_obra, nom_almacen, est_almacen) 
            VALUES ($id_cliente, $id_obra, '$nom', $est)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
//-----------------------------------------------------------------------
function MostrarAlmacenes()
{
    include("../_conexion/conexion.php"); // conexión principal

    $sql = "
        SELECT 
            a.id_almacen,
            a.nom_almacen,
            c.nom_cliente,
            s.nom_subestacion AS nom_obra,
            a.est_almacen
        FROM almacen a
        LEFT JOIN {$bd_complemento}.cliente c 
               ON a.id_cliente = c.id_cliente
        LEFT JOIN {$bd_complemento}.subestacion s 
               ON a.id_obra = s.id_subestacion
        ORDER BY a.nom_almacen ASC;
    ";

    $res = mysqli_query($con, $sql);
    $resultado = mysqli_fetch_all($res, MYSQLI_ASSOC);

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
function MostrarAlmacenesActivos()
{
    include("../_conexion/conexion.php"); // conexión principal

    $sql = "
        SELECT 
            a.id_almacen,
            a.nom_almacen,
            c.nom_cliente,
            s.nom_subestacion AS nom_obra,
            a.est_almacen
        FROM almacen a
        LEFT JOIN {$bd_complemento}.cliente c 
               ON a.id_cliente = c.id_cliente
        LEFT JOIN {$bd_complemento}.subestacion s 
               ON a.id_obra = s.id_subestacion
        WHERE a.est_almacen = 1
        ORDER BY a.nom_almacen ASC;
    ";

    $res = mysqli_query($con, $sql);
    $resultado = mysqli_fetch_all($res, MYSQLI_ASSOC);

    mysqli_close($con);
    return $resultado;
}
//-----------------------------------------------------------------------
function ActualizarAlmacen($id_almacen, $id_cliente, $id_obra, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe un almacén con el mismo nombre para el mismo cliente y obra (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM almacen 
                      WHERE nom_almacen = '$nom' 
                      AND id_cliente = $id_cliente 
                      AND id_obra = $id_obra 
                      AND id_almacen != $id_almacen";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro almacén con el mismo nombre
    }
    
    // Actualizar almacén
    $sql = "UPDATE almacen SET 
            id_cliente = $id_cliente, 
            id_obra = $id_obra, 
            nom_almacen = '$nom', 
            est_almacen = $est 
            WHERE id_almacen = $id_almacen";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
//-----------------------------------------------------------------------
function ConsultarAlmacen($id_almacen)
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT a.*, c.nom_cliente, o.nom_obra 
             FROM almacen a 
                LEFT JOIN {$bd_complemento}.cliente c ON a.id_cliente = c.id_cliente
                LEFT JOIN {$bd_complemento}.obra o ON a.id_obra = o.id_obra
             WHERE a.id_almacen = $id_almacen";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}
//-----------------------------------------------------------------------
function ConsultarAlmacenTotal()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
        pro.nom_producto       AS Producto,
        cli.nom_cliente        AS Cliente,
        obr.nom_subestacion    AS Obra,
        alm.nom_almacen        AS Almacen,
        ubi.nom_ubicacion      AS Ubicacion,
        SUM(CASE 
                WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento 
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento 
                ELSE 0 
            END) AS Cantidad
    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
        INNER JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion
    WHERE mov.est_movimiento = 1
    GROUP BY 
        pro.id_producto,
        cli.id_cliente,
        alm.id_almacen,
        ubi.id_ubicacion
    ORDER BY 
        pro.nom_producto,
        cli.nom_cliente,
        alm.nom_almacen,
        obr.nom_subestacion,
        ubi.nom_ubicacion;
    ";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}
function ConsultarAlmacenArce()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
        pro.nom_producto       AS Producto,
        pti.nom_producto_tipo  AS Tipo_Producto,
        mti.nom_material_tipo  AS Tipo_Material,
        umi.nom_unidad_medida  AS Unidad_Medida,
        SUM(CASE 
                WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento 
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento 
                ELSE 0 
            END) AS Cantidad
    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
        INNER JOIN producto_tipo  pti ON pro.id_producto_tipo = pti.id_producto_tipo
        INNER JOIN material_tipo mti ON pro.id_material_tipo  = mti.id_material_tipo
        INNER JOIN unidad_medida  umi ON pro.id_unidad_medida = umi.id_unidad_medida
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
        INNER JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion
    WHERE mov.est_movimiento = 1
    GROUP BY 
        pro.id_producto
    ORDER BY 
        pro.nom_producto
    ";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}
function ConsultarAlmacenClientes($id_cliente)
{
    include("../_conexion/conexion.php");

    $filtro_cliente = "";
    if (!empty($id_cliente)) {
        $filtro_cliente = " AND cli.id_cliente = $id_cliente ";
    }

    $sqlc = "SELECT 
        pro.nom_producto       AS Producto,
        pti.nom_producto_tipo  AS Tipo_Producto,
        mti.nom_material_tipo  AS Tipo_Material,
        umi.nom_unidad_medida  AS Unidad_Medida,
        alm.nom_almacen       AS Almacen,
        obr.nom_subestacion   AS Obra,
        ubi.nom_ubicacion      AS Ubicacion,
        SUM(CASE 
                WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento 
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento 
                ELSE 0 
            END) AS Cantidad
    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
        INNER JOIN producto_tipo  pti ON pro.id_producto_tipo = pti.id_producto_tipo
        INNER JOIN material_tipo mti ON pro.id_material_tipo  = mti.id_material_tipo
        INNER JOIN unidad_medida  umi ON pro.id_unidad_medida = umi.id_unidad_medida
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
        INNER JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion
    WHERE mov.est_movimiento = 1
    $filtro_cliente
    GROUP BY 
        pro.id_producto,
        alm.id_almacen,
        ubi.id_ubicacion
    ORDER BY 
        pro.nom_producto,
        alm.nom_almacen,
        obr.nom_subestacion,
        ubi.nom_ubicacion
    ";
    $resc = mysqli_query($con, $sqlc);

    $resultado = array();

    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    
    return $resultado;
}