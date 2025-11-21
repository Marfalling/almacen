<?php
require_once("m_pedidos.php");

function MostrarCompras()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                p.nom_proveedor,
                per1.nom_personal AS nom_registrado,
                COALESCE(per2.nom_personal, '-') AS nom_aprobado_tecnica,
                COALESCE(per3.nom_personal, '-') AS nom_aprobado_financiera
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            LEFT JOIN {$bd_complemento}.personal per1 ON c.id_personal = per1.id_personal
            /*LEFT JOIN {$bd_complemento}.personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal*/
            LEFT JOIN {$bd_complemento}.personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
            ORDER BY c.id_compra DESC";

    $resc = mysqli_query($con, $sql);

    $resultado = [];
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $rowc['pagado'] = esCompraPagada($rowc['id_compra']);
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

function MostrarComprasFecha($fecha_inicio = null, $fecha_fin = null)
{
    include("../_conexion/conexion.php");

    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $where = "WHERE DATE(c.fec_compra) = CURDATE()";
    }

    $sql = "SELECT 
                c.*,
                pe.cod_pedido,
                p.nom_proveedor,
                per1.nom_personal AS nom_registrado,
                /*COALESCE(per2.nom_personal, '-') AS nom_aprobado_tecnica,*/
                COALESCE(per3.nom_personal, '-') AS nom_aprobado_financiera
            FROM compra c
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor 
            LEFT JOIN {$bd_complemento}.personal per1 ON c.id_personal = per1.id_personal
            /*LEFT JOIN {$bd_complemento}.personal per2 ON c.id_personal_aprueba_tecnica = per2.id_personal*/
            LEFT JOIN {$bd_complemento}.personal per3 ON c.id_personal_aprueba_financiera = per3.id_personal
            $where
            ORDER BY c.id_compra DESC";

    $resc = mysqli_query($con, $sql) or die("Error en consulta: " . mysqli_error($con));

    $resultado = [];
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $rowc['pagado'] = esCompraPagada($rowc['id_compra']);
        $resultado[] = $rowc;
    }

    mysqli_close($con);
    return $resultado;
}

function CompraEsEditable($id_compra)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT c.est_compra, c.id_pedido 
            FROM compra c 
            WHERE c.id_compra = $id_compra";
    
    $resultado = mysqli_query($con, $sql);
    $compra = mysqli_fetch_assoc($resultado);
    
    if (!$compra || $compra['est_compra'] != 1) {
        mysqli_close($con);
        return false;
    }
    
    $id_pedido = $compra['id_pedido'];
    $sql_verificados = "SELECT COUNT(*) as total_verificados 
                        FROM pedido_detalle 
                        WHERE id_pedido = $id_pedido 
                        AND cant_oc_pedido_detalle IS NOT NULL 
                        AND est_pedido_detalle <> 0";
    
    $resultado_verificados = mysqli_query($con, $sql_verificados);
    $row = mysqli_fetch_assoc($resultado_verificados);
    
    mysqli_close($con);
    
    return ($row['total_verificados'] == 0);
}

function AprobarCompra($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT est_compra FROM compra WHERE id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_compra'] == 2) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET est_compra = 2, 
                       id_personal_aprueba = '$id_personal'
                   WHERE id_compra = '$id_compra'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}

function AprobarCompraTecnica($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    //  AGREGAR {$bd_complemento} 
    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = '$id_personal'";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        mysqli_close($con);
        error_log("Error: id_personal $id_personal no existe en la tabla personal");
        return false;
    }

    $sql_check = "SELECT c.est_compra, c.id_pedido, c.id_personal_aprueba_tecnica
                  FROM compra c 
                  WHERE c.id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false;
    }

    if (!empty($row['id_personal_aprueba_tecnica'])) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_tecnica = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        /*if (!empty($row['id_personal_aprueba_financiera'])) {*/
            mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
            verificarYCompletarPedido($row['id_pedido'], $con);
        /*}*/
    }

    mysqli_close($con);
    return $res_update;
}

function AprobarCompraFinanciera($id_compra, $id_personal)
{
    include("../_conexion/conexion.php");

    //  AGREGAR {$bd_complemento} 
    $sql_validar = "SELECT id_personal FROM {$bd_complemento}.personal WHERE id_personal = '$id_personal'";
    $res_validar = mysqli_query($con, $sql_validar);
    
    if (!$res_validar || mysqli_num_rows($res_validar) == 0) {
        mysqli_close($con);
        error_log("Error: id_personal $id_personal no existe en la tabla personal");
        return false;
    }

    $sql_check = "SELECT c.est_compra, c.id_pedido, c.id_personal_aprueba_financiera 
                  FROM compra c 
                  WHERE c.id_compra = '$id_compra'";
    $res_check = mysqli_query($con, $sql_check);
    $row = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if (!$row || $row['est_compra'] == 0 || $row['est_compra'] == 3) {
        mysqli_close($con);
        return false;
    }

    if (!empty($row['id_personal_aprueba_financiera'])) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE compra 
                   SET id_personal_aprueba_financiera = '$id_personal'
                   WHERE id_compra = '$id_compra'";
    $res_update = mysqli_query($con, $sql_update);

    if ($res_update) {
        mysqli_query($con, "UPDATE compra SET est_compra = 2 WHERE id_compra = '$id_compra'");
        verificarYCompletarPedido($row['id_pedido'], $con);
    }

    mysqli_close($con);
    return $res_update;
}

/**
 * Verificar si todas las órdenes de compra están aprobadas
 * y actualizar el estado del pedido
 */
function verificarYCompletarPedido($id_pedido, $con = null)
{
    $cerrar_conexion = false;
    
    if ($con === null) {
        include("../_conexion/conexion.php");
        $cerrar_conexion = true;
    }
    
    // función unificada maneja TODO
    ActualizarEstadoPedidoUnificado($id_pedido, $con);
    
    if ($cerrar_conexion) {
        mysqli_close($con);
    }
}

// Nueva función para consultar UNA compra específica por su ID
function ConsultarCompraPorId($id_compra)
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra);
    
    $sql = "SELECT c.*, 
                p.nom_proveedor,
                p.ruc_proveedor,
                m.nom_moneda,
                CASE 
                    WHEN m.id_moneda = 1 THEN 'S/.'
                    WHEN m.id_moneda = 2 THEN 'US$'
                    ELSE 'S/.'
                END as sim_moneda,
                per.nom_personal,
                /*per_tec.nom_personal AS nom_aprobado_tecnica,*/
                per_fin.nom_personal AS nom_aprobado_financiera,
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(cli.nom_cliente, 'N/A') AS nom_cliente,
                ped.cod_pedido,
                ped.fec_req_pedido,
                ped.ot_pedido,
                ped.lug_pedido,
                ped.cel_pedido,
                ped.acl_pedido,
                COALESCE(alm.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(ub.nom_ubicacion, 'N/A') AS nom_ubicacion,
                det.nombre_detraccion,
                det.porcentaje as porcentaje_detraccion,
                ret.nombre_detraccion as nombre_retencion,
                ret.porcentaje as porcentaje_retencion,
                perc.nombre_detraccion as nombre_percepcion,
                perc.porcentaje as porcentaje_percepcion
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            /*LEFT JOIN {$bd_complemento}.personal per_tec ON c.id_personal_aprueba_tecnica = per_tec.id_personal*/
            LEFT JOIN {$bd_complemento}.personal per_fin ON c.id_personal_aprueba_financiera = per_fin.id_personal
            LEFT JOIN pedido ped ON c.id_pedido = ped.id_pedido
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON ped.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen alm 
                ON ped.id_almacen = alm.id_almacen AND alm.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON alm.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente cli 
                ON alm.id_cliente = cli.id_cliente AND cli.act_cliente = 1
            LEFT JOIN ubicacion ub 
                ON ped.id_ubicacion = ub.id_ubicacion AND ub.est_ubicacion = 1
            LEFT JOIN detraccion det ON c.id_detraccion = det.id_detraccion
            LEFT JOIN detraccion ret ON c.id_retencion = ret.id_detraccion
            LEFT JOIN detraccion perc ON c.id_percepcion = perc.id_detraccion
            WHERE c.id_compra = $id_compra";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ConsultarCompraPorId SQL: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $compra = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return $compra ? array($compra) : array();
}

function ConsultarCompra($id_pedido)
{
    include("../_conexion/conexion.php");
    
    $id_pedido = intval($id_pedido);
    
    $sql = "SELECT c.*, 
                p.nom_proveedor,
                p.ruc_proveedor,
                m.nom_moneda,
                per.nom_personal,
                /*per_tec.nom_personal AS nom_aprobado_tecnica,*/
                per_fin.nom_personal AS nom_aprobado_financiera,
                COALESCE(obp.nom_subestacion, oba.nom_subestacion, 'N/A') AS nom_obra,
                COALESCE(cli.nom_cliente, 'N/A') AS nom_cliente,
                ped.cod_pedido,
                ped.fec_req_pedido,
                ped.ot_pedido,
                ped.lug_pedido,
                ped.cel_pedido,
                ped.acl_pedido,
                COALESCE(alm.nom_almacen, 'N/A') AS nom_almacen,
                COALESCE(ub.nom_ubicacion, 'N/A') AS nom_ubicacion
            FROM compra c
            INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            INNER JOIN moneda m ON c.id_moneda = m.id_moneda
            LEFT JOIN {$bd_complemento}.personal per ON c.id_personal = per.id_personal
            /*LEFT JOIN {$bd_complemento}.personal per_tec ON c.id_personal_aprueba_tecnica = per_tec.id_personal*/
            LEFT JOIN {$bd_complemento}.personal per_fin ON c.id_personal_aprueba_financiera = per_fin.id_personal
            LEFT JOIN pedido ped ON c.id_pedido = ped.id_pedido
            LEFT JOIN {$bd_complemento}.subestacion obp 
                ON ped.id_obra = obp.id_subestacion AND obp.act_subestacion = 1
            LEFT JOIN almacen alm 
                ON ped.id_almacen = alm.id_almacen AND alm.est_almacen = 1
            LEFT JOIN {$bd_complemento}.subestacion oba 
                ON alm.id_obra = oba.id_subestacion AND oba.act_subestacion = 1
            LEFT JOIN {$bd_complemento}.cliente cli 
                ON alm.id_cliente = cli.id_cliente AND cli.act_cliente = 1
            LEFT JOIN ubicacion ub 
                ON ped.id_ubicacion = ub.id_ubicacion AND ub.est_ubicacion = 1
            WHERE c.id_pedido = $id_pedido
            ORDER BY c.id_compra DESC";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ConsultarCompra SQL: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $compras = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        $row['pagado'] = esCompraPagada($row['id_compra']);
        $compras[] = $row;
    }
    
    mysqli_close($con);
    return $compras;
}

function ConsultarCompraDetalle($id_compra)
{
    include("../_conexion/conexion.php");
    
    $id_compra = intval($id_compra);
    
    // JOIN directo por id_pedido_detalle 
    $sql = "SELECT 
                cd.id_compra_detalle,
                cd.id_compra,
                cd.id_producto,
                cd.id_pedido_detalle,
                cd.cant_compra_detalle,
                cd.prec_compra_detalle,
                cd.igv_compra_detalle,
                cd.hom_compra_detalle,
                cd.est_compra_detalle,
                pd.prod_pedido_detalle,
                pd.com_pedido_detalle,
                pd.req_pedido,
                pr.nom_producto,
                pr.cod_material,
                um.nom_unidad_medida
            FROM compra_detalle cd
            LEFT JOIN pedido_detalle pd ON cd.id_pedido_detalle = pd.id_pedido_detalle
            LEFT JOIN producto pr ON cd.id_producto = pr.id_producto
            LEFT JOIN unidad_medida um ON pr.id_unidad_medida = um.id_unidad_medida
            WHERE cd.id_compra = $id_compra
            AND cd.est_compra_detalle = 1
            ORDER BY cd.id_compra_detalle ASC";
    
    $resc = mysqli_query($con, $sql);
    
    if (!$resc) {
        error_log("Error en ConsultarCompraDetalle SQL: " . mysqli_error($con));
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

function AnularPedido($id_pedido, $id_personal)
{
    include("../_conexion/conexion.php");

    $sql_check = "SELECT est_pedido FROM pedido WHERE id_pedido = '$id_pedido'";
    $res_check = mysqli_query($con, $sql_check);
    $row_check = mysqli_fetch_array($res_check, MYSQLI_ASSOC);

    if ($row_check && $row_check['est_pedido'] == 0) {
        mysqli_close($con);
        return false;
    }

    $sql_update = "UPDATE pedido 
                   SET est_pedido = 0
                   WHERE id_pedido = '$id_pedido'";

    $res_update = mysqli_query($con, $sql_update);

    mysqli_close($con);
    return $res_update;
}

function ObtenerComprasProximasVencer($dias_anticipacion = 3)
{
    include("../_conexion/conexion.php");
    
    $sql = "SELECT 
                c.id_compra,
                c.fec_compra,
                c.plaz_compra,
                c.est_compra,
                DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY) as fecha_vencimiento,
                DATEDIFF(DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY), CURDATE()) as dias_restantes,
                p.nom_proveedor,
                pe.cod_pedido,
                -- Calcular total de la compra
                (
                    SELECT COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle * (1 + (cd.igv_compra_detalle / 100))), 0)
                    FROM compra_detalle cd
                    WHERE cd.id_compra = c.id_compra
                    AND cd.est_compra_detalle = 1
                ) as total_compra,
                -- Calcular total pagado (comprobantes con estado 3)
                (
                    SELECT COALESCE(SUM(total_pagar), 0)
                    FROM comprobante
                    WHERE id_compra = c.id_compra
                    AND est_comprobante = 3
                ) as monto_pagado
            FROM compra c
            LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
            LEFT JOIN pedido pe ON c.id_pedido = pe.id_pedido
            WHERE c.est_compra IN (1, 2, 3)
            AND c.plaz_compra IS NOT NULL 
            AND c.plaz_compra >= 1
            AND DATEDIFF(DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY), CURDATE()) BETWEEN 0 AND $dias_anticipacion
            HAVING (total_compra - monto_pagado) > 0
            ORDER BY fecha_vencimiento ASC";
    
    $resultado = mysqli_query($con, $sql);
    
    if (!$resultado) {
        error_log("Error en ObtenerComprasProximasVencer: " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $compras = array();
    while ($row = mysqli_fetch_assoc($resultado)) {
        // Calcular el saldo pendiente
        $total_compra = floatval($row['total_compra']);
        $monto_pagado = floatval($row['monto_pagado']);
        $row['saldo'] = round($total_compra - $monto_pagado, 2);
        
        $compras[] = $row;
    }
    
    mysqli_close($con);
    return $compras;
}

function esCompraPagada($id_compra)
{
    include("../_conexion/conexion.php");

    $id_compra = intval($id_compra);

    // ================================================================
    // 1. CALCULAR TOTAL REAL DE LA COMPRA (cantidad * precio * IGV)
    // ================================================================
    $sql_total_compra = "
        SELECT 
            COALESCE(
                SUM(
                    cd.cant_compra_detalle 
                    * cd.prec_compra_detalle 
                    * (1 + (cd.igv_compra_detalle / 100))
                ), 
            0) AS total_compra
        FROM compra_detalle cd
        WHERE cd.id_compra = $id_compra
          AND cd.est_compra_detalle = 1
    ";

    $res_total = mysqli_query($con, $sql_total_compra);
    $row_total = mysqli_fetch_assoc($res_total);
    $total_compra = round(floatval($row_total['total_compra']), 2);


    // ================================================================
    // 2. CALCULAR TOTAL PAGADO (comprobantes con estado = 3)
    // ================================================================
    $sql_total_pagado = "
        SELECT COALESCE(SUM(monto_total_igv), 0) AS total_pagado
        FROM comprobante
        WHERE id_compra = $id_compra
          AND est_comprobante = 3
    ";

    $res_pagado = mysqli_query($con, $sql_total_pagado);
    $row_pagado = mysqli_fetch_assoc($res_pagado);
    $total_pagado = round(floatval($row_pagado['total_pagado']), 2);


    // ================================================================
    // 3. RETORNAR SI LA COMPRA ESTÁ PAGADA
    // ================================================================
    return $total_pagado >= $total_compra;
}