<?php
/*require_once("../_conexion/conexion.php");
/*require_once("../_conexion/conexion_complemento.php"); */
// ============================================
// FUNCIONES PARA OBTENER DATOS DE FILTROS
// ============================================

/*function obtenerListaProveedores($con) {
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

// ============================================
// DASHBOARD 3.a: RESUMEN GENERAL DE ÓRDENES DE COMPRA
// ============================================
function obtenerResumenOrdenes($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE c.est_compra != 0";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN c.est_compra = 3 THEN 1 ELSE 0 END) as ordenes_atendidas,
            SUM(CASE WHEN c.est_compra IN (1, 2) THEN 1 ELSE 0 END) as ordenes_pendientes
            FROM compra c
            $where";
    
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return ['total_ordenes' => 0, 'ordenes_atendidas' => 0, 'ordenes_pendientes' => 0];
}

// ============================================
// DASHBOARD 3.b: ORDENES DE COMPRA POR ALMACÉN
// ============================================
function obtenerOrdenesPorAlmacen($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE c.est_compra != 0";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            a.nom_almacen as almacen,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN c.est_compra = 3 THEN 1 ELSE 0 END) as ordenes_atendidas,
            SUM(CASE WHEN c.est_compra IN (1, 2) THEN 1 ELSE 0 END) as ordenes_pendientes
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN almacen a ON p.id_almacen = a.id_almacen
            $where
            GROUP BY a.id_almacen, a.nom_almacen
            HAVING total_ordenes > 0
            ORDER BY a.nom_almacen";
    
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}

// ============================================
// DASHBOARD 3.c: PAGOS POR ALMACÉN (SIN AGRUPAR POR MONEDA)
// ============================================
/*function obtenerPagosPorAlmacen($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE c.est_compra IN (2, 3)";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            a.nom_almacen as almacen,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN i.fpag_ingreso IS NOT NULL THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN i.fpag_ingreso IS NULL THEN 1 ELSE 0 END) as pendientes_pago,
            COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle), 0) as monto_total_soles,
            COALESCE(SUM(CASE WHEN c.id_moneda = 2 THEN cd.cant_compra_detalle * cd.prec_compra_detalle ELSE 0 END), 0) as monto_total_dolares
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN almacen a ON p.id_almacen = a.id_almacen
            LEFT JOIN ingreso i ON c.id_compra = i.id_compra AND i.est_ingreso = 1
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra AND cd.est_compra_detalle = 1
            $where
            GROUP BY a.id_almacen, a.nom_almacen
            HAVING total_ordenes > 0
            ORDER BY a.nom_almacen";
    
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}

// ============================================
// DASHBOARD 3.d: PAGOS POR PROVEEDOR (SIN AGRUPAR POR MONEDA)
// ============================================
function obtenerPagosPorProveedor($con, $fecha_inicio = null, $fecha_fin = null, $proveedor = null) {
    $where = "WHERE c.est_compra IN (2, 3)";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    if ($proveedor) {
        $where .= " AND pr.id_proveedor = $proveedor";
    }
    
    $sql = "SELECT 
            pr.nom_proveedor as proveedor,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN i.fpag_ingreso IS NOT NULL THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN i.fpag_ingreso IS NULL THEN 1 ELSE 0 END) as pendientes_pago,
            COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle), 0) as monto_total_soles,
            COALESCE(SUM(CASE WHEN c.id_moneda = 2 THEN cd.cant_compra_detalle * cd.prec_compra_detalle ELSE 0 END), 0) as monto_total_dolares
            FROM compra c
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            LEFT JOIN ingreso i ON c.id_compra = i.id_compra AND i.est_ingreso = 1
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra AND cd.est_compra_detalle = 1
            $where
            GROUP BY pr.id_proveedor, pr.nom_proveedor
            HAVING total_ordenes > 0
            ORDER BY monto_total_soles DESC";
    
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}*/

// ============================================
// DASHBOARD 3.c: PAGOS POR ALMACÉN (ACTUALIZADO)
// ============================================
/*function obtenerPagosPorAlmacen($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE comp.est_comprobante IN (1, 3)"; // 1=Pendiente, 3=Pagado
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(comp.fec_emision) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            a.nom_almacen as almacen,
            
            -- Total de comprobantes (órdenes)
            COUNT(DISTINCT comp.id_comprobante) as total_ordenes,
            
            -- Comprobantes por estado
            SUM(CASE WHEN comp.est_comprobante = 3 THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN comp.est_comprobante = 1 THEN 1 ELSE 0 END) as pendientes_pago,
            
            -- Montos en soles (id_moneda = 1)
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_total_soles,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 AND comp.est_comprobante = 3 THEN comp.total_pagar ELSE 0 END), 0) as monto_pagado_soles,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 AND comp.est_comprobante = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_pendiente_soles,
            
            -- Montos en dólares (id_moneda = 2)
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 THEN comp.total_pagar ELSE 0 END), 0) as monto_total_dolares,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 AND comp.est_comprobante = 3 THEN comp.total_pagar ELSE 0 END), 0) as monto_pagado_dolares,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 AND comp.est_comprobante = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_pendiente_dolares,
            
            -- Porcentaje de pago
            ROUND((SUM(CASE WHEN comp.est_comprobante = 3 THEN 1 ELSE 0 END) * 100.0) / 
                  COUNT(DISTINCT comp.id_comprobante), 2) as porcentaje_pagado
            
            FROM comprobante comp
            INNER JOIN compra c ON comp.id_compra = c.id_compra
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN almacen a ON p.id_almacen = a.id_almacen
            $where
            GROUP BY a.id_almacen, a.nom_almacen
            HAVING total_ordenes > 0
            ORDER BY a.nom_almacen";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calcular totales generales (suma de ambas monedas para referencia)
            $row['total_comprobantes_general'] = $row['monto_total_soles'] + $row['monto_total_dolares'];
            $row['total_pagado_general'] = $row['monto_pagado_soles'] + $row['monto_pagado_dolares'];
            $row['total_pendiente_general'] = $row['monto_pendiente_soles'] + $row['monto_pendiente_dolares'];
            
            $datos[] = $row;
        }
    }
    
    return $datos;
}

// ============================================
// DASHBOARD 3.d: PAGOS POR PROVEEDOR (ACTUALIZADO)
// ============================================
function obtenerPagosPorProveedor($con, $fecha_inicio = null, $fecha_fin = null, $proveedor = null) {
    $where = "WHERE comp.est_comprobante IN (1, 3)"; // 1=Pendiente, 3=Pagado
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(comp.fec_emision) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    if ($proveedor) {
        $where .= " AND pr.id_proveedor = $proveedor";
    }
    
    $sql = "SELECT 
            pr.nom_proveedor as proveedor,
            pr.id_proveedor,
            
            -- Total de comprobantes (órdenes)
            COUNT(DISTINCT comp.id_comprobante) as total_ordenes,
            
            -- Comprobantes por estado
            SUM(CASE WHEN comp.est_comprobante = 3 THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN comp.est_comprobante = 1 THEN 1 ELSE 0 END) as pendientes_pago,
            
            -- Montos en soles (id_moneda = 1)
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_total_soles,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 AND comp.est_comprobante = 3 THEN comp.total_pagar ELSE 0 END), 0) as monto_pagado_soles,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 1 AND comp.est_comprobante = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_pendiente_soles,
            
            -- Montos en dólares (id_moneda = 2)
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 THEN comp.total_pagar ELSE 0 END), 0) as monto_total_dolares,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 AND comp.est_comprobante = 3 THEN comp.total_pagar ELSE 0 END), 0) as monto_pagado_dolares,
            COALESCE(SUM(CASE WHEN comp.id_moneda = 2 AND comp.est_comprobante = 1 THEN comp.total_pagar ELSE 0 END), 0) as monto_pendiente_dolares,
            
            -- Porcentaje de pago
            ROUND((SUM(CASE WHEN comp.est_comprobante = 3 THEN 1 ELSE 0 END) * 100.0) / 
                  COUNT(DISTINCT comp.id_comprobante), 2) as porcentaje_pagado
            
            FROM comprobante comp
            INNER JOIN proveedor_cuenta pc ON comp.id_cuenta_proveedor = pc.id_proveedor_cuenta
            INNER JOIN proveedor pr ON pc.id_proveedor = pr.id_proveedor
            $where
            GROUP BY pr.id_proveedor, pr.nom_proveedor
            HAVING total_ordenes > 0
            ORDER BY monto_total_soles DESC, monto_total_dolares DESC";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calcular totales generales (suma de ambas monedas para referencia)
            $row['total_comprobantes_general'] = $row['monto_total_soles'] + $row['monto_total_dolares'];
            $row['total_pagado_general'] = $row['monto_pagado_soles'] + $row['monto_pagado_dolares'];
            $row['total_pendiente_general'] = $row['monto_pendiente_soles'] + $row['monto_pendiente_dolares'];
            
            $datos[] = $row;
        }
    }
    
    return $datos;
}

// ============================================
// DASHBOARD 3.e: ORDENES VENCIDAS POR PROVEEDOR POR MES
// ============================================
function obtenerOrdenesVencidasPorProveedorMes($con, $año = null) {
    if (!$año) {
        $año = date('Y');
    }
    
    $sql = "SELECT 
            pr.nom_proveedor as proveedor,
            MONTH(c.fec_compra) as mes,
            COUNT(DISTINCT c.id_compra) as ordenes_vencidas
            FROM compra c
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            WHERE YEAR(c.fec_compra) = $año
            AND c.est_compra IN (1, 2, 3)
            AND c.plaz_compra IS NOT NULL
            AND c.plaz_compra != ''
            AND c.plaz_compra != '0'
            AND DATEDIFF(CURDATE(), DATE_ADD(c.fec_compra, INTERVAL CAST(c.plaz_compra AS UNSIGNED) DAY)) > 0
            GROUP BY pr.id_proveedor, pr.nom_proveedor, MONTH(c.fec_compra)
            ORDER BY pr.nom_proveedor, mes";
    
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}

// ============================================
// CARDS DE RESUMEN
// ============================================

// ESTOS NO CAMBIAN CON FILTROS (totales generales)
function obtenerTotalProductos($con) {
    $sql = "SELECT COUNT(*) as total FROM producto WHERE est_producto = 1";
    $result = $con->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}

function obtenerTotalAlmacenes($con) {
    $sql = "SELECT COUNT(*) as total FROM almacen WHERE est_almacen = 1";
    $result = $con->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}

function obtenerTotalProveedores($con) {
    $sql = "SELECT COUNT(*) as total FROM proveedor WHERE est_proveedor = 1";
    $result = $con->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}

// ESTOS SÍ CAMBIAN CON FILTROS
function obtenerTotalPedidos($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE est_pedido IN (0, 1)";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(fec_pedido) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT COUNT(*) as total FROM pedido $where";
    $result = $con->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}

function obtenerTotalCompras($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "WHERE est_compra != 0";
    
    if ($fecha_inicio && $fecha_fin) {
        $where .= " AND DATE(fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT COUNT(*) as total FROM compra $where";
    $result = $con->query($sql);
    return $result ? $result->fetch_assoc()['total'] : 0;
}*/

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
function _buildFiltroProveedorCentro(&$whereClauses, $proveedor, $centro_costo) {
    if ($proveedor && is_numeric($proveedor) && intval($proveedor) > 0) {
        $p = intval($proveedor);
        $whereClauses[] = "c.id_proveedor = $p";
    }
    if ($centro_costo && is_numeric($centro_costo) && intval($centro_costo) > 0) {
        $cc = intval($centro_costo);
        // pedido tiene id_centro_costo
        $whereClauses[] = "p.id_centro_costo = $cc";
    }
}

// ============================================
// DASHBOARD: RESUMEN GENERAL DE ÓRDENES (TOTAL)
// - Considera filtros: proveedor, centro de costo, fecha inicio/fin
// - Devuelve: total_ordenes, ordenes_atendidas, ordenes_pendientes
// ============================================
function obtenerResumenOrdenes($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    // Map de estados: atendidas = est_compra IN (2,4) (según uso en BD)
    $where = ["c.est_compra != 0"];
    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT
                COUNT(DISTINCT c.id_compra) AS total_ordenes,
                SUM(CASE WHEN c.est_compra = 3 THEN 1 ELSE 0 END) AS ordenes_atendidas,
                SUM(CASE WHEN c.est_compra IN (1,2) THEN 1 ELSE 0 END) AS ordenes_pendientes
            FROM compra c
            LEFT JOIN pedido p ON c.id_pedido = p.id_pedido
            $where_sql";
    $result = $con->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        // asegurar valores numéricos
        $row['total_ordenes'] = intval($row['total_ordenes']);
        $row['ordenes_atendidas'] = intval($row['ordenes_atendidas']);
        $row['ordenes_pendientes'] = intval($row['ordenes_pendientes']);
        return $row;
    }
    return ['total_ordenes' => 0, 'ordenes_atendidas' => 0, 'ordenes_pendientes' => 0];
}

// ============================================
// ÓRDENES ATENDIDAS / PENDIENTES POR CENTRO DE COSTO
// - Filtros: proveedor, centro_costo (opcional), fecha_inicio, fecha_fin
// - Devuelve array por centro de costo
// ============================================
function obtenerOrdenesPorCentroCosto($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    global $bd_complemento;
    $where = ["c.est_compra != 0"];
    _buildFiltroProveedorCentro($where, $proveedor, $centro_costo);
    _buildFiltroFecha($where, $fecha_inicio, $fecha_fin, "DATE(c.fec_compra)");
    $where_sql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "SELECT
                COALESCE(cc.nom_area, 'SIN CENTRO') AS centro_costo,
                cc.id_area,
                COUNT(DISTINCT c.id_compra) AS total_ordenes,
                SUM(CASE WHEN c.est_compra = 3 THEN 1 ELSE 0 END) AS atendidas,
                SUM(CASE WHEN c.est_compra IN (1,2) THEN 1 ELSE 0 END) AS pendientes
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            LEFT JOIN {$bd_complemento}.area cc ON p.id_centro_costo = cc.id_area
            $where_sql
            GROUP BY cc.id_area, cc.nom_area
            HAVING total_ordenes > 0
            ORDER BY cc.nom_area";
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['total_ordenes'] = intval($row['total_ordenes']);
            $row['atendidas'] = intval($row['atendidas']);
            $row['pendientes'] = intval($row['pendientes']);
            $datos[] = $row;
        }
    }
    return $datos;
}

// ============================================
// ÓRDENES PAGADAS / PENDIENTES POR CENTRO DE COSTO
// - Determinación de pago por comprobante: comp.fec_pago IS NOT NULL o comp.est_comprobante = 3
// - Agrupa por centro de costo (pedido -> id_centro_costo)
// - Filtros: proveedor, centro_costo, fecha inicio/fin (aplica sobre fecha de comprobante si existe, sino sobre compra)
// ============================================
function obtenerPagosPorCentroCosto($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {
    global $bd_complemento;

    $where = ["1=1"];

    if ($proveedor && intval($proveedor) > 0) {
        $where[] = "c.id_proveedor = " . intval($proveedor);
    }
    if ($centro_costo && intval($centro_costo) > 0) {
        $where[] = "p.id_centro_costo = " . intval($centro_costo);
    }
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }

    $where_sql = "WHERE " . implode(" AND ", $where);

    $sql = "
    SELECT
        COALESCE(cc.nom_area, 'SIN CENTRO') AS centro_costo,
        cc.id_area,
        COUNT(*) AS total_ordenes,

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

            COALESCE(cd_sum.subtotal,0) AS subtotal,
            COALESCE(cd_sum.total_igv,0) AS total_igv,
            (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)) AS total_con_igv,

            COALESCE(det.porcentaje,0) AS porcentaje,

            /* TOTAL A PAGAR DEPENDIENDO DEL TIPO */
            CASE 
                WHEN c2.id_detraccion = 12 THEN
                    (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                    - ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))

                WHEN c2.id_detraccion = 13 THEN
                    (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                    + ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))

                ELSE
                    (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                    - ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))
            END AS total_a_pagar,

            /* TOTAL PAGADO (comprobantes pagados) */
            (
                SELECT COALESCE(SUM(cb.total_pagar),0)
                FROM comprobante cb
                WHERE cb.id_compra = c2.id_compra
                AND cb.est_comprobante = 3
            ) AS total_pagado

        FROM compra c2
        LEFT JOIN detraccion det ON det.id_detraccion = c2.id_detraccion

        LEFT JOIN (
            SELECT 
                cd.id_compra,
                SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) AS subtotal,
                SUM((cd.cant_compra_detalle * cd.prec_compra_detalle)*(cd.igv_compra_detalle/100)) AS total_igv
            FROM compra_detalle cd
            WHERE cd.est_compra_detalle = 1
            GROUP BY cd.id_compra
        ) cd_sum ON cd_sum.id_compra = c2.id_compra

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
            $row['monto_total']       = floatval($row['monto_total']);
            $row['monto_pagado']      = floatval($row['monto_pagado']);
            $row['monto_pendiente']   = floatval($row['monto_pendiente']);
            $datos[] = $row;
        }
    }

    return $datos;
}

// ============================================
// ÓRDENES PAGADAS / PENDIENTES POR PROVEEDOR
// - Filtros: proveedor (opcional), centro_costo (opcional), fecha inicio/fin
// - Usa comprobante.fec_pago o comp.est_comprobante = 3 para determinar pago
// ============================================
function obtenerPagosPorProveedor($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null) {

    $where = ["1=1"];

    if ($proveedor && intval($proveedor) > 0) {
        $where[] = "c.id_proveedor = " . intval($proveedor);
    }
    if ($centro_costo && intval($centro_costo) > 0) {
        $where[] = "p.id_centro_costo = " . intval($centro_costo);
    }
    if ($fecha_inicio && $fecha_fin) {
        $where[] = "DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }

    $where_sql = "WHERE " . implode(" AND ", $where);

    $sql = "
            SELECT
            pr.nom_proveedor AS proveedor,
            pr.id_proveedor,

            COUNT(*) AS total_ordenes,

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

                COALESCE(cd_sum.subtotal,0) AS subtotal,
                COALESCE(cd_sum.total_igv,0) AS total_igv,

                (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)) AS total_con_igv,

                COALESCE(det.porcentaje,0) AS pct,

                CASE 
                    WHEN c2.id_detraccion = 12 THEN
                        (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                        - ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))

                    WHEN c2.id_detraccion = 13 THEN
                        (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                        + ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))

                    ELSE
                        (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0))
                        - ((COALESCE(det.porcentaje,0)/100) * (COALESCE(cd_sum.subtotal,0) + COALESCE(cd_sum.total_igv,0)))
                END AS total_a_pagar,

                (
                    SELECT COALESCE(SUM(cb.total_pagar),0)
                    FROM comprobante cb
                    WHERE cb.id_compra = c2.id_compra
                    AND cb.est_comprobante = 3
                ) AS total_pagado

            FROM compra c2
            LEFT JOIN detraccion det ON det.id_detraccion = c2.id_detraccion

            LEFT JOIN (
                SELECT 
                    cd.id_compra,
                    COALESCE(SUM(cd.cant_compra_detalle * cd.prec_compra_detalle),0) AS subtotal,
                    COALESCE(SUM((cd.cant_compra_detalle * cd.prec_compra_detalle)*(cd.igv_compra_detalle/100)),0) AS total_igv
                FROM compra_detalle cd
                WHERE cd.est_compra_detalle = 1
                GROUP BY cd.id_compra
            ) cd_sum ON cd_sum.id_compra = c2.id_compra
        ) t ON t.id_compra = c.id_compra

        $where_sql

        GROUP BY pr.id_proveedor, pr.nom_proveedor
        ORDER BY monto_total DESC;
    ";

    $result = $con->query($sql);
    $datos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['total_ordenes']     = intval($row['total_ordenes']);
            $row['pagadas']           = intval($row['pagadas']);
            $row['pendientes_pago']   = intval($row['pendientes_pago']);
            $row['monto_total']       = floatval($row['monto_total']);
            $row['monto_pagado']      = floatval($row['monto_pagado']);
            $row['monto_pendiente']   = floatval($row['monto_pendiente']);
            $datos[] = $row;
        }
    }

    return $datos;
}

// ============================================
// ÓRDENES VENCIDAS POR PROVEEDOR POR MES
// - Filtros: proveedor (opcional), centro_costo (opcional), fecha_inicio/fin (opcional) 
// - Si no se pasa año, se usa el año actual.
// - Se considera vencida cuando: DATE_ADD(fec_compra, INTERVAL plaz_compra DAY) < CURDATE()
// ============================================
function obtenerOrdenesVencidasPorProveedorMes($con, $proveedor = null, $centro_costo = null, $fecha_inicio = null, $fecha_fin = null, $año = null) {
    if (!$año) $año = date('Y');

    $where = ["YEAR(c.fec_compra) = $año",
              "c.plaz_compra IS NOT NULL",
              "c.plaz_compra != ''",
              "c.plaz_compra != '0'",
              "DATEDIFF(CURDATE(), DATE_ADD(c.fec_compra, INTERVAL CAST(c.plaz_compra AS UNSIGNED) DAY)) > 0"
    ];

    if ($proveedor && is_numeric($proveedor) && intval($proveedor) > 0) {
        $p = intval($proveedor);
        $where[] = "c.id_proveedor = $p";
    }
    if ($centro_costo && is_numeric($centro_costo) && intval($centro_costo) > 0) {
        $cc = intval($centro_costo);
        $where[] = "p.id_centro_costo = $cc";
    }
    if ($fecha_inicio && $fecha_fin) {
        $fi = $fecha_inicio;
        $ff = $fecha_fin;
        $where[] = "DATE(c.fec_compra) BETWEEN '$fi' AND '$ff'";
    }

    $where_sql = "WHERE " . implode(" AND ", $where);

    $sql = "SELECT
                COALESCE(pr.nom_proveedor, 'SIN PROVEEDOR') AS proveedor,
                pr.id_proveedor,
                MONTH(c.fec_compra) AS mes,
                COUNT(DISTINCT c.id_compra) AS ordenes_vencidas
            FROM compra c
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            $where_sql
            GROUP BY pr.id_proveedor, pr.nom_proveedor, MONTH(c.fec_compra)
            ORDER BY pr.nom_proveedor, mes";
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['ordenes_vencidas'] = intval($row['ordenes_vencidas']);
            $row['mes'] = intval($row['mes']);
            $datos[] = $row;
        }
    }
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