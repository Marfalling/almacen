<?php
require_once("../_conexion/conexion.php");
/*require_once("../_conexion/conexion_complemento.php"); */

// ============================================
// FUNCIONES PARA OBTENER DATOS DE FILTROS
// ============================================
function obtenerListaProveedores($con) {
    $sql = "SELECT id_proveedor, nom_proveedor FROM proveedor WHERE est_proveedor = 1 ORDER BY nom_proveedor";
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}

// Helper: normalizar filtros (evita inyección simple y valores vacíos)
function _buildFiltroFecha(&$whereClauses, $fecha_inicio, $fecha_fin, $campo_fecha = "DATE(c.fec_compra)") {
    if ($fecha_inicio && $fecha_fin) {
        $fi = $fecha_inicio;
        $ff = $fecha_fin;
        $whereClauses[] = "$campo_fecha BETWEEN '$fi' AND '$ff'";
    }
}
/*function _buildFiltroProveedorCentro(&$whereClauses, $proveedor, $centro_costo) {
    if ($proveedor && is_numeric($proveedor) && intval($proveedor) > 0) {
        $p = intval($proveedor);
        $whereClauses[] = "c.id_proveedor = $p";
    }
    if ($centro_costo && is_numeric($centro_costo) && intval($centro_costo) > 0) {
        $cc = intval($centro_costo);
        // pedido tiene id_centro_costo
        $whereClauses[] = "p.id_centro_costo = $cc";
    }
}*/


function _buildFiltroProveedorCentro(&$whereClauses, $proveedor, $centro_costo) {
    // Proveedor(es)
    if ($proveedor) {
        if (strpos($proveedor, ',') !== false) {
            // Múltiples: "1,2,3"
            $whereClauses[] = "c.id_proveedor IN ($proveedor)";
        } else {
            // Simple: "1"
            $p = intval($proveedor);
            $whereClauses[] = "c.id_proveedor = $p";
        }
    }
    
    // Centro(s) de Costo
    if ($centro_costo) {
        if (strpos($centro_costo, ',') !== false) {
            // Múltiples: "5,8,12"
            $whereClauses[] = "p.id_centro_costo IN ($centro_costo)";
        } else {
            // Simple: "5"
            $cc = intval($centro_costo);
            $whereClauses[] = "p.id_centro_costo = $cc";
        }
    }
}


// ============================================
// DASHBOARD: RESUMEN GENERAL DE ÓRDENES (TOTAL)
// - Considera filtros: proveedor, centro de costo, fecha inicio/fin
// - Devuelve: total_ordenes, ordenes_atendidas, ordenes_pendientes
// ============================================

function obtenerResumenOrdenes($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["c.est_compra != 0"];
    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT 
                c.id_compra,
                c.est_compra,
                
                -- Total de la compra
                COALESCE(
                    SUM(
                        cd.cant_compra_detalle 
                        * cd.prec_compra_detalle 
                        * (1 + (cd.igv_compra_detalle / 100))
                    ), 
                0) AS total_compra,
                
                -- Total pagado
                COALESCE(
                    (SELECT SUM(monto_total_igv) 
                     FROM comprobante 
                     WHERE id_compra = c.id_compra 
                       AND est_comprobante = 3), 
                0) AS total_pagado
                
            FROM compra c
            LEFT JOIN pedido p ON c.id_pedido = p.id_pedido
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra 
                AND cd.est_compra_detalle = 1
            $where_sql
            GROUP BY c.id_compra, c.est_compra";
    
    $result = $con->query($sql);
    
    if (!$result) {
        return ['total_ordenes' => 0, 'ordenes_atendidas' => 0, 'ordenes_pendientes' => 0];
    }

    $total_ordenes = 0;
    $ordenes_atendidas = 0;
    $ordenes_pendientes = 0;

    while ($row = $result->fetch_assoc()) {
        $est_compra = intval($row['est_compra']);
        $total_compra = round(floatval($row['total_compra']), 2);
        $total_pagado = round(floatval($row['total_pagado']), 2);
        
        $total_ordenes++;

        // Verificar si está pagada (esto la hace ATENDIDA)
        $esta_pagada = ($total_pagado >= $total_compra);

        // Clasificar: Atendida si est_compra=3 O si está pagada
        if ($est_compra == 3) {
            $ordenes_atendidas++;
        } else {
            // est_compra: 1=Pendiente, 2=Parcial
            $ordenes_pendientes++;
        }
    }

    return [
        'total_ordenes' => $total_ordenes,
        'ordenes_atendidas' => $ordenes_atendidas,
        'ordenes_pendientes' => $ordenes_pendientes
    ];
}

// ============================================
// ÓRDENES ATENDIDAS / PENDIENTES POR CENTRO DE COSTO
// - Clasificación: Atendida si est_compra=3 O si está completamente pagada
// - Filtros: proveedor, centro_costo (opcional), fecha_inicio, fecha_fin
// - Devuelve array por centro de costo con claves directas
// ============================================
function obtenerOrdenesPorCentroCosto($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    global $bd_complemento;
    
    $where = ["c.est_compra != 0"];
    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT 
                c.id_compra,
                c.est_compra,
                COALESCE(cc.nom_area, 'SIN CENTRO') AS centro_costo,
                COALESCE(cc.id_area, 0) AS id_area,
                
                -- Total de la compra
                COALESCE(
                    SUM(
                        cd.cant_compra_detalle 
                        * cd.prec_compra_detalle 
                        * (1 + (cd.igv_compra_detalle / 100))
                    ), 
                0) AS total_compra,
                
                -- Total pagado
                COALESCE(
                    (SELECT SUM(monto_total_igv) 
                     FROM comprobante 
                     WHERE id_compra = c.id_compra 
                       AND est_comprobante = 3), 
                0) AS total_pagado
                
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            LEFT JOIN {$bd_complemento}.area cc ON p.id_centro_costo = cc.id_area
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra 
                AND cd.est_compra_detalle = 1
            $where_sql
            GROUP BY c.id_compra, c.est_compra, cc.id_area, cc.nom_area";
    
    $result = $con->query($sql);
    
    if (!$result) {
        return [];
    }

    // Agrupar por centro de costo
    $centros = [];

    while ($row = $result->fetch_assoc()) {
        $id_area = intval($row['id_area']);
        $centro_costo = $row['centro_costo'];
        $est_compra = intval($row['est_compra']);
        $total_compra = round(floatval($row['total_compra']), 2);
        $total_pagado = round(floatval($row['total_pagado']), 2);

        // Inicializar centro si no existe
        if (!isset($centros[$id_area])) {
            $centros[$id_area] = [
                'centro_costo' => $centro_costo,
                'id_area' => $id_area,
                'total_ordenes' => 0,
                'atendidas' => 0,
                'pendientes' => 0
            ];
        }

        $centros[$id_area]['total_ordenes']++;

        // MISMA LÓGICA que obtenerResumenOrdenes()
        $esta_pagada = ($total_pagado >= $total_compra);

        // Clasificar: Atendida si est_compra=3 O si está pagada
        if ($est_compra == 3) {
            $centros[$id_area]['atendidas']++;
        } else {
            $centros[$id_area]['pendientes']++;
        }
    }

    // Convertir a array indexado y filtrar
    $datos = [];
    foreach ($centros as $centro) {
        if ($centro['total_ordenes'] > 0) {
            $datos[] = $centro;
        }
    }

    // Ordenar por nombre de centro
    usort($datos, function($a, $b) {
        return strcmp($a['centro_costo'], $b['centro_costo']);
    });

    return $datos;
}

// ============================================
// ÓRDENES PAGADAS / PENDIENTES POR CENTRO DE COSTO
// - Determinación de pago basada en vouchers (fg=1 y fg=2) según tipo de afectación
// - Agrupa por centro de costo (pedido -> id_centro_costo)
// - Filtros: proveedor, centro_costo, fecha inicio/fin
// ============================================
function obtenerPagosPorCentroCosto($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    global $bd_complemento;

    $where = ["c.est_compra != 0"];

    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = "WHERE " . implode(" AND ", $where);

    $sql = "
    SELECT
        COALESCE(cc.nom_area, 'SIN CENTRO') AS centro_costo,
        cc.id_area,
        COUNT(DISTINCT c.id_compra) AS total_ordenes,

        SUM(CASE WHEN t.total_pagado >= t.total_a_pagar THEN 1 ELSE 0 END) AS pagadas,
        SUM(CASE WHEN t.total_pagado <  t.total_a_pagar THEN 1 ELSE 0 END) AS pendientes_pago,

        SUM(t.total_a_pagar) AS monto_total,
        SUM(t.total_pagado) AS monto_pagado,
        SUM(GREATEST(t.total_a_pagar - t.total_pagado, 0)) AS monto_pendiente

    FROM compra c
    INNER JOIN pedido p ON c.id_pedido = p.id_pedido
    LEFT JOIN {$bd_complemento}.area cc ON cc.id_area = p.id_centro_costo

    INNER JOIN (
        SELECT
            c2.id_compra,

            -- ========================================
            -- 1. SUBTOTAL E IGV
            -- ========================================
            COALESCE(cd_sum.subtotal, 0) AS subtotal,
            COALESCE(cd_sum.total_igv, 0) AS total_igv,
            (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) AS total_con_igv,

            -- ========================================
            -- 2. IDENTIFICAR TIPO DE AFECTACIÓN
            -- ========================================
            CASE 
                WHEN c2.id_detraccion IS NOT NULL THEN 'detraccion'
                WHEN c2.id_retencion IS NOT NULL THEN 'retencion'
                WHEN c2.id_percepcion IS NOT NULL THEN 'percepcion'
                ELSE 'ninguna'
            END AS tipo_afectacion,

            COALESCE(
                d_det.porcentaje, 
                d_ret.porcentaje, 
                d_per.porcentaje, 
                0
            ) AS porcentaje,

            -- ========================================
            -- 3. CALCULAR MONTO DE AFECTACIÓN
            -- ========================================
            (
                (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                * COALESCE(d_det.porcentaje, d_ret.porcentaje, d_per.porcentaje, 0) 
                / 100
            ) AS monto_afectacion,

            -- ========================================
            -- 4. TOTAL A PAGAR (según tipo)
            -- ========================================
            CASE 
                -- PERCEPCIÓN: suma la afectación
                WHEN c2.id_percepcion IS NOT NULL THEN
                    (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0))
                    + ((COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                       * COALESCE(d_per.porcentaje, 0) / 100)
                
                -- DETRACCIÓN/RETENCIÓN/NINGUNA: resta la afectación (o 0)
                ELSE
                    (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0))
                    - ((COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                       * COALESCE(d_det.porcentaje, d_ret.porcentaje, 0) / 100)
            END AS total_a_pagar,

            -- ========================================
            -- 5. TOTAL PAGADO (lógica de vouchers)
            -- ========================================
            COALESCE(pagos.total_pagado, 0) AS total_pagado

        FROM compra c2
        
        -- Joins para afectaciones
        LEFT JOIN detraccion d_det ON d_det.id_detraccion = c2.id_detraccion
        LEFT JOIN detraccion d_ret ON d_ret.id_detraccion = c2.id_retencion
        LEFT JOIN detraccion d_per ON d_per.id_detraccion = c2.id_percepcion

        -- Subtotal e IGV
        LEFT JOIN (
            SELECT 
                cd.id_compra,
                SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) AS subtotal,
                SUM((cd.cant_compra_detalle * cd.prec_compra_detalle) * (cd.igv_compra_detalle / 100)) AS total_igv
            FROM compra_detalle cd
            WHERE cd.est_compra_detalle = 1
            GROUP BY cd.id_compra
        ) cd_sum ON cd_sum.id_compra = c2.id_compra

        -- ========================================
        -- CÁLCULO DE PAGOS POR COMPROBANTE
        -- ========================================
        LEFT JOIN (
            SELECT 
                comp.id_compra,
                SUM(
                    -- Si es PERCEPCIÓN (id=13 o id_percepcion de compra)
                    CASE 
                        WHEN comp.id_detraccion = 13 OR comp.id_detraccion = c_inner.id_percepcion THEN
                            -- Solo cuenta FG=1 pagado
                            CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 1
                                      AND cp.est_comprobante_pago = 1
                                ) THEN comp.total_pagar
                                ELSE 0
                            END
                        
                        -- Si NO es percepción (detracción/retención/ninguna)
                        ELSE
                            -- FG=1 (pago al proveedor)
                            (CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 1
                                      AND cp.est_comprobante_pago = 1
                                ) THEN comp.total_pagar
                                ELSE 0
                            END)
                            +
                            -- FG=2 (pago a SUNAT)
                            (CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 2
                                      AND cp.est_comprobante_pago = 1
                                ) THEN (comp.monto_total_igv - comp.total_pagar)
                                ELSE 0
                            END)
                    END
                ) AS total_pagado
            FROM comprobante comp
            INNER JOIN compra c_inner ON c_inner.id_compra = comp.id_compra
            GROUP BY comp.id_compra
        ) pagos ON pagos.id_compra = c2.id_compra

    ) AS t ON t.id_compra = c.id_compra

    $where_sql
    GROUP BY cc.id_area, cc.nom_area
    ORDER BY cc.nom_area ASC
    ";

    $result = $con->query($sql);
    $datos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['total_ordenes']     = intval($row['total_ordenes']);
            $row['pagadas']           = intval($row['pagadas']);
            $row['pendientes_pago']   = intval($row['pendientes_pago']);
            $row['monto_total']       = round(floatval($row['monto_total']), 2);
            $row['monto_pagado']      = round(floatval($row['monto_pagado']), 2);
            $row['monto_pendiente']   = round(floatval($row['monto_pendiente']), 2);
            $datos[] = $row;
        }
    }

    return $datos;
}

// ============================================
// ÓRDENES PAGADAS / PENDIENTES POR PROVEEDOR
// - Determinación de pago basada en vouchers (fg=1 y fg=2) según tipo de afectación
// - Agrupa por proveedor
// - Filtros: proveedor, centro_costo, fecha inicio/fin
// ============================================
function obtenerPagosPorProveedor($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {

    $where = ["c.est_compra != 0"];

    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = "WHERE " . implode(" AND ", $where);

    $sql = "
    SELECT
        pr.nom_proveedor AS proveedor,
        pr.id_proveedor,

        COUNT(DISTINCT c.id_compra) AS total_ordenes,

        SUM(CASE WHEN t.total_pagado >= t.total_a_pagar THEN 1 ELSE 0 END) AS pagadas,
        SUM(CASE WHEN t.total_pagado <  t.total_a_pagar THEN 1 ELSE 0 END) AS pendientes_pago,

        SUM(t.total_a_pagar) AS monto_total,
        SUM(t.total_pagado) AS monto_pagado,
        SUM(GREATEST(t.total_a_pagar - t.total_pagado, 0)) AS monto_pendiente

    FROM compra c
    INNER JOIN pedido p ON p.id_pedido = c.id_pedido
    INNER JOIN proveedor pr ON pr.id_proveedor = c.id_proveedor

    INNER JOIN (
        SELECT
            c2.id_compra,
            c2.id_proveedor,

            -- ========================================
            -- 1. SUBTOTAL E IGV
            -- ========================================
            COALESCE(cd_sum.subtotal, 0) AS subtotal,
            COALESCE(cd_sum.total_igv, 0) AS total_igv,
            (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) AS total_con_igv,

            -- ========================================
            -- 2. IDENTIFICAR TIPO DE AFECTACIÓN
            -- ========================================
            CASE 
                WHEN c2.id_detraccion IS NOT NULL THEN 'detraccion'
                WHEN c2.id_retencion IS NOT NULL THEN 'retencion'
                WHEN c2.id_percepcion IS NOT NULL THEN 'percepcion'
                ELSE 'ninguna'
            END AS tipo_afectacion,

            COALESCE(
                d_det.porcentaje, 
                d_ret.porcentaje, 
                d_per.porcentaje, 
                0
            ) AS porcentaje,

            -- ========================================
            -- 3. CALCULAR MONTO DE AFECTACIÓN
            -- ========================================
            (
                (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                * COALESCE(d_det.porcentaje, d_ret.porcentaje, d_per.porcentaje, 0) 
                / 100
            ) AS monto_afectacion,

            -- ========================================
            -- 4. TOTAL A PAGAR (según tipo)
            -- ========================================
            CASE 
                -- PERCEPCIÓN: suma la afectación
                WHEN c2.id_percepcion IS NOT NULL THEN
                    (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0))
                    + ((COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                       * COALESCE(d_per.porcentaje, 0) / 100)
                
                -- DETRACCIÓN/RETENCIÓN/NINGUNA: resta la afectación (o 0)
                ELSE
                    (COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0))
                    - ((COALESCE(cd_sum.subtotal, 0) + COALESCE(cd_sum.total_igv, 0)) 
                       * COALESCE(d_det.porcentaje, d_ret.porcentaje, 0) / 100)
            END AS total_a_pagar,

            -- ========================================
            -- 5. TOTAL PAGADO (lógica de vouchers)
            -- ========================================
            COALESCE(pagos.total_pagado, 0) AS total_pagado

        FROM compra c2
        
        -- Joins para afectaciones
        LEFT JOIN detraccion d_det ON d_det.id_detraccion = c2.id_detraccion
        LEFT JOIN detraccion d_ret ON d_ret.id_detraccion = c2.id_retencion
        LEFT JOIN detraccion d_per ON d_per.id_detraccion = c2.id_percepcion

        -- Subtotal e IGV
        LEFT JOIN (
            SELECT 
                cd.id_compra,
                SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) AS subtotal,
                SUM((cd.cant_compra_detalle * cd.prec_compra_detalle) * (cd.igv_compra_detalle / 100)) AS total_igv
            FROM compra_detalle cd
            WHERE cd.est_compra_detalle = 1
            GROUP BY cd.id_compra
        ) cd_sum ON cd_sum.id_compra = c2.id_compra

        -- ========================================
        -- CÁLCULO DE PAGOS POR COMPROBANTE
        -- ========================================
        LEFT JOIN (
            SELECT 
                comp.id_compra,
                SUM(
                    -- Si es PERCEPCIÓN (id=13 o id_percepcion de compra)
                    CASE 
                        WHEN comp.id_detraccion = 13 OR comp.id_detraccion = c_inner.id_percepcion THEN
                            -- Solo cuenta FG=1 pagado
                            CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 1
                                      AND cp.est_comprobante_pago = 1
                                ) THEN comp.total_pagar
                                ELSE 0
                            END
                        
                        -- Si NO es percepción (detracción/retención/ninguna)
                        ELSE
                            -- FG=1 (pago al proveedor)
                            (CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 1
                                      AND cp.est_comprobante_pago = 1
                                ) THEN comp.total_pagar
                                ELSE 0
                            END)
                            +
                            -- FG=2 (pago a SUNAT)
                            (CASE 
                                WHEN EXISTS (
                                    SELECT 1 FROM comprobante_pago cp
                                    WHERE cp.id_comprobante = comp.id_comprobante
                                      AND cp.fg_comprobante_pago = 2
                                      AND cp.est_comprobante_pago = 1
                                ) THEN GREATEST(comp.monto_total_igv - comp.total_pagar, 0)
                                ELSE 0
                            END)
                    END
                ) AS total_pagado
            FROM comprobante comp
            INNER JOIN compra c_inner ON c_inner.id_compra = comp.id_compra
            GROUP BY comp.id_compra
        ) pagos ON pagos.id_compra = c2.id_compra

    ) t ON t.id_compra = c.id_compra

    $where_sql

    GROUP BY pr.id_proveedor, pr.nom_proveedor
    ORDER BY monto_total DESC
    ";

    $result = $con->query($sql);
    $datos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['total_ordenes']     = intval($row['total_ordenes']);
            $row['pagadas']           = intval($row['pagadas']);
            $row['pendientes_pago']   = intval($row['pendientes_pago']);
            $row['monto_total']       = round(floatval($row['monto_total']), 2);
            $row['monto_pagado']      = round(floatval($row['monto_pagado']), 2);
            $row['monto_pendiente']   = round(floatval($row['monto_pendiente']), 2);
            $datos[] = $row;
        }
    }

    return $datos;
}

// ============================================
// ÓRDENES VENCIDAS POR PROVEEDOR POR MES
// - Filtros: proveedor (múltiple), centro_costo (múltiple), fecha_inicio/fin
// - Vencida: (fec_compra + plaz_compra) < HOY Y NO está pagada
// - Usa esCompraPagada() de m_compras.php para validar el estado de pago
// ============================================
function obtenerOrdenesVencidasPorProveedorMes($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null, $año = null) {
    // Asegurar que la función esCompraPagada() esté disponible
    if (!function_exists('esCompraPagada')) {
        require_once("m_compras.php");
    }
    
    if (!$año) $año = date('Y');

    // ✅ CONSTRUIR FILTROS CON HELPER
    $where = [
        "YEAR(c.fec_compra) = $año",
        "c.plaz_compra IS NOT NULL",
        "c.plaz_compra != ''",
        "c.plaz_compra != '0'",
        "DATEDIFF(CURDATE(), DATE_ADD(c.fec_compra, INTERVAL CAST(c.plaz_compra AS UNSIGNED) DAY)) > 0"
    ];

    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");

    $where_sql = "WHERE " . implode(" AND ", $where);

    // ✅ OBTENER TODAS LAS COMPRAS VENCIDAS (candidatas)
    $sql = "
        SELECT
            c.id_compra,
            COALESCE(pr.nom_proveedor, 'SIN PROVEEDOR') AS proveedor,
            pr.id_proveedor,
            MONTH(c.fec_compra) AS mes
        FROM compra c
        INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
        INNER JOIN pedido p ON c.id_pedido = p.id_pedido
        $where_sql
        ORDER BY pr.id_proveedor, MONTH(c.fec_compra)
    ";
    
    $result = $con->query($sql);
    
    if (!$result || $result->num_rows == 0) {
        return [];
    }

    // ✅ FILTRAR SOLO LAS QUE NO ESTÁN PAGADAS
    $agrupado = [];
    
    while ($row = $result->fetch_assoc()) {
        $id_compra = intval($row['id_compra']);
        $id_proveedor = intval($row['id_proveedor']);
        $proveedor = $row['proveedor'];
        $mes = intval($row['mes']);
        
        // ✅ VERIFICAR SI NO ESTÁ PAGADA
        if (!esCompraPagada($id_compra)) {
            // Inicializar estructura si no existe
            if (!isset($agrupado[$id_proveedor])) {
                $agrupado[$id_proveedor] = [
                    'proveedor' => $proveedor,
                    'id_proveedor' => $id_proveedor,
                    'meses' => []
                ];
            }
            
            if (!isset($agrupado[$id_proveedor]['meses'][$mes])) {
                $agrupado[$id_proveedor]['meses'][$mes] = 0;
            }
            
            $agrupado[$id_proveedor]['meses'][$mes]++;
        }
    }

    // ✅ CONVERTIR A FORMATO ESPERADO
    $datos = [];
    foreach ($agrupado as $id_prov => $info) {
        foreach ($info['meses'] as $mes => $cantidad) {
            $datos[] = [
                'proveedor' => $info['proveedor'],
                'id_proveedor' => $info['id_proveedor'],
                'mes' => $mes,
                'ordenes_vencidas' => $cantidad
            ];
        }
    }

    // ✅ ORDENAR POR PROVEEDOR Y MES
    usort($datos, function($a, $b) {
        $cmp_prov = strcmp($a['proveedor'], $b['proveedor']);
        if ($cmp_prov !== 0) return $cmp_prov;
        return $a['mes'] - $b['mes'];
    });

    return $datos;
}
// ============================================
// CARDS DE RESUMEN (conservadas)
// ============================================
function obtenerTotalProductos($con) {
    $sql = "SELECT COUNT(*) as total FROM producto WHERE est_producto = 1";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalAlmacenes($con) {
    $sql = "SELECT COUNT(*) as total FROM almacen WHERE est_almacen = 1";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalProveedores($con) {
    $sql = "SELECT COUNT(*) as total FROM proveedor WHERE est_proveedor = 1";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalPedidos($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["est_pedido IN (0, 1)"];
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(fec_pedido) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    $where_sql = "WHERE " . implode(" AND ", $where);
    $sql = "SELECT COUNT(*) as total FROM pedido $where_sql";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalCompras($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["est_compra != 0"];
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    $where_sql = "WHERE " . implode(" AND ", $where);
    $sql = "SELECT COUNT(*) as total FROM compra $where_sql";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalIngresos($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["est_ingreso != 0"];
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(fec_ingreso) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    $where_sql = "WHERE " . implode(" AND ", $where);
    $sql = "SELECT COUNT(*) as total FROM ingreso $where_sql";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalSalidas($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["est_salida != 0"];
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(fec_salida) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    $where_sql = "WHERE " . implode(" AND ", $where);
    $sql = "SELECT COUNT(*) as total FROM salida $where_sql";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerTotalDevoluciones($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = ["est_devolucion != 0"];
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(fec_devolucion) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    $where_sql = "WHERE " . implode(" AND ", $where);
    $sql = "SELECT COUNT(*) as total FROM devolucion $where_sql";
    $result = $con->query($sql);
    return $result ? intval($result->fetch_assoc()['total']) : 0;
}

function obtenerListaCentros($con) {
    global $bd_complemento;

    $sql = "SELECT id_area, nom_area 
            FROM {$bd_complemento}.area 
            ORDER BY nom_area ASC";

    $result = $con->query($sql);
    $datos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }

    return $datos;
}