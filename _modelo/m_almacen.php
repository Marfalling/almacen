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
        
        // Obtener el nombre de la obra
        $sql_obra = "SELECT nom_subestacion FROM {$bd_complemento}.subestacion WHERE id_subestacion = $id_obra";
        $resultado_obra = mysqli_query($con, $sql_obra);
        $obra = mysqli_fetch_assoc($resultado_obra);
        
        if ($obra) {
            $nom_almacen_principal = strtoupper($obra['nom_subestacion'] . ' ARCE');
            
            // Verificar si ya existe el almacén principal para evitar duplicados
            $sql_verificar_principal = "SELECT COUNT(*) as total FROM almacen 
                                        WHERE nom_almacen = '$nom_almacen_principal' 
                                        AND id_cliente = 9 
                                        AND id_obra = $id_obra";
            $resultado_verificar_principal = mysqli_query($con, $sql_verificar_principal);
            $fila_principal = mysqli_fetch_assoc($resultado_verificar_principal);
            
            // Solo crear si no existe
            if ($fila_principal['total'] == 0) {
                $sql_principal = "INSERT INTO almacen (id_cliente, id_obra, nom_almacen, est_almacen) 
                                  VALUES (9, $id_obra, '$nom_almacen_principal', $est)";
                mysqli_query($con, $sql_principal);
            }
        }
        
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
        WHERE a.id_almacen != 1
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
        AND a.id_almacen != 1
        ORDER BY a.nom_almacen ASC;
    ";

    $res = mysqli_query($con, $sql);
    $resultado = mysqli_fetch_all($res, MYSQLI_ASSOC);

    mysqli_close($con);
    return $resultado;
}

function MostrarAlmacenesActivosConArceBase()
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
/*function ConsultarAlmacenTotal()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
        pro.nom_producto       AS Producto,
        cli.nom_cliente        AS Cliente,
        obr.nom_subestacion    AS Obra,
        alm.nom_almacen        AS Almacen,
        ubi.nom_ubicacion      AS Ubicacion,
        -- STOCK FÍSICO (actual)
            SUM(
                CASE 
                    WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento 
                    WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden <> 5 THEN -mov.cant_movimiento 
                    ELSE 0 
                END
            ) AS Stock_Fisico,

            -- STOCK RESERVADO (pedidos tipo_orden=5)
            (
                SELECT 
                    IFNULL(SUM(mr.cant_movimiento), 0)
                FROM movimiento mr
                WHERE mr.id_producto = mov.id_producto
                  AND mr.id_almacen = mov.id_almacen
                  AND mr.id_ubicacion = mov.id_ubicacion
                  AND mr.tipo_orden = 5
                  AND mr.tipo_movimiento = 2
                  AND mr.est_movimiento = 1
            ) AS Stock_Reservado,

            -- STOCK DISPONIBLE = Físico - Reservado
            (
                SUM(
                    CASE 
                        WHEN mov.tipo_movimiento = 1 THEN mov.cant_movimiento 
                        WHEN mov.tipo_movimiento = 2 AND mov.tipo_orden <> 5 THEN -mov.cant_movimiento
                        ELSE 0 
                    END
                )
                -
                (
                    SELECT 
                        IFNULL(SUM(mr.cant_movimiento), 0)
                    FROM movimiento mr
                    WHERE mr.id_producto = mov.id_producto
                      AND mr.id_almacen = mov.id_almacen
                      AND mr.id_ubicacion = mov.id_ubicacion
                      AND mr.tipo_orden = 5
                      AND mr.tipo_movimiento = 2
                      AND mr.est_movimiento = 1
                )
            ) AS Stock_Disponible
    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
        INNER JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion
    WHERE mov.est_movimiento = 1
      AND pro.id_producto_tipo <> 2  --  EXCLUIR SERVICIOS (tipo_producto_tipo = 2)
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
}*/

function ConsultarAlmacenTotal()
{
    include("../_conexion/conexion.php");

    $sqlc = "SELECT 
        pro.nom_producto       AS Producto,
        cli.nom_cliente        AS Cliente,
        COALESCE(obr.nom_subestacion, 'SIN OBRA') AS Obra,
        alm.nom_almacen        AS Almacen,
        ubi.nom_ubicacion      AS Ubicacion,
        
        -- STOCK DISPONIBLE (stock real utilizable)
        COALESCE(
            SUM(CASE
                -- INGRESOS: Incluye devoluciones SOLO si es ARCE (id_cliente = 9) Y está CONFIRMADO (est_movimiento = 1)
                WHEN mov.tipo_movimiento = 1 THEN
                    CASE
                        -- ARCE: Cuenta devoluciones SOLO si están confirmadas (est_movimiento = 1)
                        WHEN mov.tipo_orden = 3 AND cli.id_cliente = 9 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        -- Otros ingresos normales (NO devoluciones)
                        WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                        -- Otros clientes con devoluciones: NO cuenta
                        ELSE 0
                    END
                -- SALIDAS: Siempre restan
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                ELSE 0
            END), 0
        ) AS Stock_Disponible,
        
        -- STOCK EN DEVOLUCIÓN (solo en BASE)
        CASE 
            WHEN ubi.id_ubicacion = 1 THEN  -- ⬅️ SOLO si es BASE
                COALESCE(
                    (
                        SELECT SUM(md.cant_movimiento)
                        FROM movimiento md
                        WHERE md.id_producto = mov.id_producto
                          AND md.id_almacen = mov.id_almacen
                          AND md.id_ubicacion = 1  -- BASE
                          AND md.tipo_orden = 3    -- Devoluciones
                          AND md.tipo_movimiento = 1  -- Ingresos a BASE
                          AND md.est_movimiento = 2   -- Pendiente
                    ), 0
                )
            ELSE 0  -- ⬅️ En OBRA siempre es 0
        END AS Stock_Devolucion

    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
    INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
    LEFT JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion

    WHERE mov.est_movimiento != 0
    AND pro.id_producto_tipo <> 2  -- EXCLUIR SERVICIOS
    
   GROUP BY 
    pro.id_producto,
    pro.nom_producto,
    cli.id_cliente,
    cli.nom_cliente,
    alm.id_almacen,
    alm.nom_almacen,
    obr.id_subestacion,
    obr.nom_subestacion,
    ubi.id_ubicacion,
    ubi.nom_ubicacion

    
    HAVING Stock_Disponible > 0 OR Stock_Devolucion > 0
    
    ORDER BY 
        pro.nom_producto,
        cli.nom_cliente,
        alm.nom_almacen,
        COALESCE(obr.nom_subestacion, 'SIN OBRA'),
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
        
        -- STOCK DISPONIBLE (stock real utilizable)
        COALESCE(
            SUM(CASE
                -- INGRESOS: Incluye devoluciones SOLO si es ARCE (id_cliente = 9) Y está CONFIRMADO (est_movimiento = 1)
                WHEN mov.tipo_movimiento = 1 THEN
                    CASE
                        -- ARCE: Cuenta devoluciones SOLO si están confirmadas (est_movimiento = 1)
                        WHEN mov.tipo_orden = 3 AND cli.id_cliente = 9 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        -- Otros ingresos normales (NO devoluciones)
                        WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                        -- Otros clientes con devoluciones: NO cuenta
                        ELSE 0
                    END
                -- SALIDAS: Siempre restan
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                ELSE 0
            END), 0
        ) AS Stock_Disponible,
        
        -- STOCK EN DEVOLUCIÓN (solo en BASE)
        COALESCE(
            (
                SELECT SUM(md.cant_movimiento)
                FROM movimiento md
                WHERE md.id_producto = pro.id_producto
                  AND (
                      -- De TODOS los almacenes: solo BASE
                      (md.id_almacen != 3 AND md.id_ubicacion = 1)
                      OR
                      -- De ARCE: todas las ubicaciones pero solo BASE para devoluciones
                      (md.id_almacen = 3 AND md.id_ubicacion = 1)
                  )
                  AND md.tipo_orden = 3         -- Devoluciones
                  AND md.tipo_movimiento = 1    -- Ingresos
                  AND md.est_movimiento = 2     -- Pendiente
            ), 0
        ) AS Stock_Devolucion
        
    FROM movimiento mov
    INNER JOIN producto pro ON mov.id_producto = pro.id_producto
        INNER JOIN producto_tipo pti ON pro.id_producto_tipo = pti.id_producto_tipo
        INNER JOIN material_tipo mti ON pro.id_material_tipo = mti.id_material_tipo
        INNER JOIN unidad_medida umi ON pro.id_unidad_medida = umi.id_unidad_medida
    INNER JOIN {$bd_complemento}.personal per ON mov.id_personal = per.id_personal
    INNER JOIN almacen alm ON mov.id_almacen = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente cli ON alm.id_cliente = cli.id_cliente
        LEFT JOIN {$bd_complemento}.subestacion obr ON alm.id_obra = obr.id_subestacion
    INNER JOIN ubicacion ubi ON mov.id_ubicacion = ubi.id_ubicacion
    
    WHERE mov.est_movimiento != 0
      AND pro.id_producto_tipo <> 2  -- EXCLUIR SERVICIOS
      AND (
          -- ✅ De TODOS los almacenes: solo BASE (ubicación 1)
          mov.id_ubicacion = 1
          OR
          -- ✅ Del almacén ARCE (id=3): TODAS las ubicaciones
          mov.id_almacen = 3
      )
      
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
        COALESCE(obr.nom_subestacion, 'SIN OBRA') AS Obra,
        ubi.nom_ubicacion      AS Ubicacion,
        -- STOCK DISPONIBLE (stock real utilizable)
        COALESCE(
            SUM(CASE
                -- INGRESOS: Incluye devoluciones SOLO si es ARCE (id_cliente = 9) Y está CONFIRMADO (est_movimiento = 1)
                WHEN mov.tipo_movimiento = 1 THEN
                    CASE
                        -- ARCE: Cuenta devoluciones SOLO si están confirmadas (est_movimiento = 1)
                        WHEN mov.tipo_orden = 3 AND cli.id_cliente = 9 AND mov.est_movimiento = 1 THEN mov.cant_movimiento
                        -- Otros ingresos normales (NO devoluciones)
                        WHEN mov.tipo_orden != 3 THEN mov.cant_movimiento
                        -- Otros clientes con devoluciones: NO cuenta
                        ELSE 0
                    END
                -- SALIDAS: Siempre restan
                WHEN mov.tipo_movimiento = 2 THEN -mov.cant_movimiento
                ELSE 0
            END), 0
        ) AS Stock_Disponible,
        
        -- STOCK EN DEVOLUCIÓN (solo en BASE)
        CASE 
            WHEN ubi.id_ubicacion = 1 THEN  -- ⬅️ SOLO si es BASE
                COALESCE(
                    (
                        SELECT SUM(md.cant_movimiento)
                        FROM movimiento md
                        WHERE md.id_producto = mov.id_producto
                          AND md.id_almacen = mov.id_almacen
                          AND md.id_ubicacion = 1  -- BASE
                          AND md.tipo_orden = 3    -- Devoluciones
                          AND md.tipo_movimiento = 1  -- Ingresos a BASE
                          AND md.est_movimiento = 2   -- Pendiente
                    ), 0
                )
            ELSE 0  -- ⬅️ En OBRA siempre es 0
        END AS Stock_Devolucion
    FROM movimiento mov
    INNER JOIN producto   pro ON mov.id_producto   = pro.id_producto
        INNER JOIN producto_tipo  pti ON pro.id_producto_tipo = pti.id_producto_tipo
        INNER JOIN material_tipo mti ON pro.id_material_tipo  = mti.id_material_tipo
        INNER JOIN unidad_medida  umi ON pro.id_unidad_medida = umi.id_unidad_medida
    INNER JOIN {$bd_complemento}.personal   per ON mov.id_personal   = per.id_personal
    INNER JOIN almacen    alm ON mov.id_almacen    = alm.id_almacen
        INNER JOIN {$bd_complemento}.cliente    cli ON alm.id_cliente    = cli.id_cliente
        LEFT JOIN {$bd_complemento}.subestacion obr ON alm.id_obra       = obr.id_subestacion
    INNER JOIN ubicacion  ubi ON mov.id_ubicacion  = ubi.id_ubicacion
    WHERE mov.est_movimiento != 0
      AND pro.id_producto_tipo <> 2  -- EXCLUIR SERVICIOS
    $filtro_cliente
    GROUP BY 
        pro.id_producto,
        alm.id_almacen,
        ubi.id_ubicacion
    ORDER BY 
        pro.nom_producto,
        alm.nom_almacen,
        COALESCE(obr.nom_subestacion, 'SIN OBRA'),
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