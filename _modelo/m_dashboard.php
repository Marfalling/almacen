<?php
require_once("../_conexion/conexion.php");




// Funciones para obtener datos básicos de las cards 
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
    $sql = "SELECT COUNT(*) as total FROM compra WHERE est_compra IN (1, 2)";
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

// Funciones para gráficos 
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
            WHERE c.est_compra IN (1, 2)
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
    } else {
        $datos = [['Activos', 1], ['Inactivos', 0]];
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
    } else {
        $datos = [['CONSUMIBLES', 2], ['HERRAMIENTAS', 1], ['NA', 2]];
    }
    
    return $datos;
}

?>