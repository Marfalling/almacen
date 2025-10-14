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
function obtenerPagosPorAlmacen($con, $fecha_inicio = null, $fecha_fin = null) {
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
}
?>