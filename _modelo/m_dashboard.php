<?php
require_once("../_conexion/conexion.php");

// ============================================
// FUNCIONES EXISTENTES (Cards básicas)
// ============================================
function obtenerTotalProductos($con) {
    $sql = "SELECT COUNT(*) as total FROM producto WHERE est_producto = 1";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

function obtenerTotalUsuarios($con) {
    $sql = "SELECT COUNT(*) as total FROM usuario WHERE est_usuario = 1";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

function obtenerTotalPedidos($con) {
    $sql = "SELECT COUNT(*) as total FROM pedido WHERE est_pedido = 1";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

function obtenerTotalCompras($con) {
    $sql = "SELECT COUNT(*) as total FROM compra WHERE est_compra IN (1, 2, 3)";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

function obtenerTotalAlmacenes($con) {
    $sql = "SELECT COUNT(*) as total FROM almacen WHERE est_almacen = 1";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

function obtenerTotalProveedores($con) {
    $sql = "SELECT COUNT(*) as total FROM proveedor WHERE est_proveedor = 1";
    $result = $con->query($sql);
    return $result->fetch_assoc()['total'];
}

// ============================================
// DASHBOARD 3.a: ORDENES GENERADAS, ATENDIDAS, PENDIENTES
// ============================================
function obtenerResumenOrdenes($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = " AND DATE(fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            COUNT(*) as total_ordenes,
            SUM(CASE WHEN est_compra = 3 THEN 1 ELSE 0 END) as ordenes_atendidas,
            SUM(CASE WHEN est_compra IN (0, 1, 2) THEN 1 ELSE 0 END) as ordenes_pendientes
            FROM compra
            WHERE 1=1 $where";
    
    $result = $con->query($sql);
    return $result->fetch_assoc();
}

// ============================================
// DASHBOARD 3.b: ORDENES POR CENTRO DE COSTO
// ============================================
function obtenerOrdenesPorCentroCosto($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            a.nom_almacen as centro_costo,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN c.est_compra = 3 THEN 1 ELSE 0 END) as ordenes_atendidas,
            SUM(CASE WHEN c.est_compra IN (0, 1, 2) THEN 1 ELSE 0 END) as ordenes_pendientes
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN almacen a ON p.id_almacen = a.id_almacen
            WHERE 1=1 $where
            GROUP BY a.id_almacen, a.nom_almacen
            ORDER BY total_ordenes DESC";
    
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
// DASHBOARD 3.c: ORDENES PAGADAS Y PENDIENTES POR CENTRO DE COSTO
// ============================================
function obtenerPagosPorCentroCosto($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            a.nom_almacen as centro_costo,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN i.fpag_ingreso IS NOT NULL THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN i.fpag_ingreso IS NULL AND c.est_compra = 3 THEN 1 ELSE 0 END) as pendientes_pago,
            SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) as monto_total,
            m.nom_moneda as moneda
            FROM compra c
            INNER JOIN pedido p ON c.id_pedido = p.id_pedido
            INNER JOIN almacen a ON p.id_almacen = a.id_almacen
            LEFT JOIN ingreso i ON c.id_compra = i.id_compra
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra
            LEFT JOIN moneda m ON c.id_moneda = m.id_moneda
            WHERE c.est_compra IN (2, 3) $where
            GROUP BY a.id_almacen, a.nom_almacen, m.id_moneda, m.nom_moneda
            ORDER BY monto_total DESC";
    
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
// DASHBOARD 3.d: ORDENES PAGADAS Y PENDIENTES POR PROVEEDOR
// ============================================
function obtenerPagosPorProveedor($con, $fecha_inicio = null, $fecha_fin = null) {
    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = " AND DATE(c.fec_compra) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    
    $sql = "SELECT 
            pr.nom_proveedor as proveedor,
            COUNT(DISTINCT c.id_compra) as total_ordenes,
            SUM(CASE WHEN i.fpag_ingreso IS NOT NULL THEN 1 ELSE 0 END) as ordenes_pagadas,
            SUM(CASE WHEN i.fpag_ingreso IS NULL AND c.est_compra = 3 THEN 1 ELSE 0 END) as pendientes_pago,
            SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) as monto_total,
            m.nom_moneda as moneda
            FROM compra c
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            LEFT JOIN ingreso i ON c.id_compra = i.id_compra
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra
            LEFT JOIN moneda m ON c.id_moneda = m.id_moneda
            WHERE c.est_compra IN (2, 3) $where
            GROUP BY pr.id_proveedor, pr.nom_proveedor, m.id_moneda, m.nom_moneda
            ORDER BY monto_total DESC";
    
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
            MONTHNAME(c.fec_compra) as nombre_mes,
            COUNT(c.id_compra) as ordenes_vencidas,
            SUM(cd.cant_compra_detalle * cd.prec_compra_detalle) as monto_vencido
            FROM compra c
            INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            LEFT JOIN compra_detalle cd ON c.id_compra = cd.id_compra
            WHERE YEAR(c.fec_compra) = $año
            AND c.est_compra = 3
            AND DATEDIFF(NOW(), DATE_ADD(c.fec_compra, INTERVAL c.plaz_compra DAY)) > 0
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
// FUNCIONES PARA GRÁFICOS EXISTENTES
// ============================================
function obtenerDatosGraficoTiposProducto($con) {
    $sql = "SELECT 
            COALESCE(pt.nom_producto_tipo, 'Sin Tipo') as tipo, 
            COUNT(*) AS cantidad 
            FROM producto p
            LEFT JOIN producto_tipo pt ON p.id_producto_tipo = pt.id_producto_tipo
            WHERE p.est_producto = 1
            GROUP BY p.id_producto_tipo, pt.nom_producto_tipo
            ORDER BY cantidad DESC";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = [$row['tipo'], (int)$row['cantidad']];
        }
    } 
    
    return $datos;
}

function obtenerDatosGraficoComprasPorProveedor($con) {
    $sql = "SELECT 
            COALESCE(pr.nom_proveedor, 'Sin Proveedor') as proveedor, 
            COUNT(*) AS cantidad 
            FROM compra c
            LEFT JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
            WHERE c.est_compra IN (1, 2, 3)
            GROUP BY c.id_proveedor, pr.nom_proveedor
            ORDER BY cantidad DESC
            LIMIT 10";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = [$row['proveedor'], (int)$row['cantidad']];
        }
    } 
    
    return $datos;
}

function obtenerDatosGraficoEstadoPedidos($con) {
    $sql = "SELECT 
            CASE 
                WHEN est_pedido = 1 THEN 'Activos'
                WHEN est_pedido = 0 THEN 'Inactivos'
                ELSE 'Otros'
            END as estado,
            COUNT(*) as cantidad
            FROM pedido 
            GROUP BY est_pedido
            ORDER BY cantidad DESC";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = [$row['estado'], (int)$row['cantidad']];
        }
    }
    
    return $datos;
}

function obtenerDatosGraficoProductosPorMaterial($con) {
    $sql = "SELECT 
            COALESCE(mt.nom_material_tipo, 'Sin Clasificar') as material,
            COUNT(*) as cantidad
            FROM producto p
            LEFT JOIN material_tipo mt ON p.id_material_tipo = mt.id_material_tipo
            WHERE p.est_producto = 1
            GROUP BY p.id_material_tipo, mt.nom_material_tipo
            ORDER BY cantidad DESC";
    
    $result = $con->query($sql);
    $datos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = [$row['material'], (int)$row['cantidad']];
        }
    }
    
    return $datos;
}

// ============================================
// FUNCIONES DE FILTROS
// ============================================
function obtenerListaCentrosCosto($con) {
    $sql = "SELECT id_almacen, nom_almacen FROM almacen WHERE est_almacen = 1 ORDER BY nom_almacen";
    $result = $con->query($sql);
    $datos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
    }
    return $datos;
}

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

?>