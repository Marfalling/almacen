<?php

require_once("../_conexion/sesion.php");
require_once("../_modelo/m_dashboard.php");

// Verificar permisos
if (!verificarPermisoEspecifico('ver_dashboard')) {
    header("location: bienvenido.php?permisos=true");
    exit;
}

// ========================================================================
// OBTENER FILTROS
// ========================================================================
$fecha_inicio  = $_GET['fecha_inicio']  ?? null;
$fecha_fin     = $_GET['fecha_fin']     ?? null;
//$proveedor     = $_GET['proveedor']     ?? null;
//$centro_costo  = $_GET['centro_costo']  ?? null;

// Capturar filtros múltiples
$proveedores_seleccionados = isset($_GET['proveedor']) && is_array($_GET['proveedor']) 
    ? array_map('intval', $_GET['proveedor']) 
    : [];

$centros_seleccionados = isset($_GET['centro_costo']) && is_array($_GET['centro_costo']) 
    ? array_map('intval', $_GET['centro_costo']) 
    : [];

// Convertir a string para SQL (o pasar array al modelo)
$proveedor_filtro = !empty($proveedores_seleccionados) 
    ? implode(',', $proveedores_seleccionados) 
    : null;

$centro_filtro = !empty($centros_seleccionados) 
    ? implode(',', $centros_seleccionados) 
    : null;

// Si no hay fechas, usar valores por defecto (último mes)
if (!$fecha_inicio && !$fecha_fin) {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin    = date('Y-m-d');
}

// ========================================================================
// OBTENER DATOS PARA FILTROS
// ========================================================================
$lista_proveedores = obtenerListaProveedores($con);
// (si quieres lista centro costo:)
$lista_centros_costo = obtenerListaCentros($con);

// ========================================================================
// CARDS PRINCIPALES
// ========================================================================
$cantidad_productos   = obtenerTotalProductos($con);
$cantidad_almacenes   = obtenerTotalAlmacenes($con);
$cantidad_proveedores = obtenerTotalProveedores($con);

// Estos sí dependen de fecha
$cantidad_pedidos = obtenerTotalPedidos($con, $fecha_inicio, $fecha_fin);
$cantidad_compras = obtenerTotalCompras($con, $fecha_inicio, $fecha_fin);
$cantidad_ingresos = obtenerTotalIngresos($con, $fecha_inicio, $fecha_fin);
$cantidad_salidas = obtenerTotalSalidas($con, $fecha_inicio, $fecha_fin);
$cantidad_devoluciones = obtenerTotalDevoluciones($con, $fecha_inicio, $fecha_fin);

// ========================================================================
// DATOS PARA LOS REPORTES PRINCIPALES
// ========================================================================

// 1. Órdenes generadas, atendidas y pendientes
$resumen_ordenes = obtenerResumenOrdenes(
    $con, 
    $proveedor_filtro, 
    $centro_filtro, 
    $fecha_inicio, 
    $fecha_fin
);

// 2. Órdenes atendidas y pendientes POR centro de costo
$ordenes_por_cc = obtenerOrdenesPorCentroCosto(
    $con,
    $proveedor_filtro,
    $centro_filtro,
    $fecha_inicio,
    $fecha_fin
);

// 3. Órdenes pagadas y pendientes POR centro de costo
$pagos_por_cc = obtenerPagosPorCentroCosto(
    $con,
    $proveedor_filtro,
    $centro_filtro,
    $fecha_inicio,
    $fecha_fin
);

// 4. Órdenes pagadas y pendientes POR proveedor
$pagos_por_proveedor = obtenerPagosPorProveedor(
    $con,
    $proveedor_filtro,
    $centro_filtro,
    $fecha_inicio,
    $fecha_fin
);

// 5. Órdenes vencidas por proveedor por mes
$año_actual = date('Y');

$vencidas = obtenerOrdenesVencidasPorProveedorMes(
    $con,
    $proveedor_filtro,
    $centro_filtro,
    $fecha_inicio,
    $fecha_fin,
    $año_actual
);

// Organizar para la tabla
$proveedores_mes = [];
foreach ($vencidas as $row) {
    $prov = $row['proveedor'];
    $mes  = $row['mes'];
    if (!isset($proveedores_mes[$prov])) {
        $proveedores_mes[$prov] = array_fill(1, 12, 0);
    }
    $proveedores_mes[$prov][$mes] = $row['ordenes_vencidas'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Almacén</title>
  <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
  <div class="container body">
    <div class="main_container">
      <?php
      require_once("../_vista/v_menu.php");
      require_once("../_vista/v_menu_user.php");
      require_once("../_vista/v_dashboard.php");
      require_once("../_vista/v_footer.php");
      ?>
    </div>
  </div>
  <?php
  require_once("../_vista/v_script.php");
  require_once("../_vista/v_alertas.php");
  ?>
</body>
</html>