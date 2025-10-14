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
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
$proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : null;

// Si no hay fechas, usar valores por defecto (último mes)
if (!$fecha_inicio && !$fecha_fin) {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin = date('Y-m-d');
}

// ========================================================================
// OBTENER DATOS PARA FILTROS
// ========================================================================
$lista_proveedores = obtenerListaProveedores($con);

// ========================================================================
// CARDS PRINCIPALES
// ========================================================================
$cantidad_productos = obtenerTotalProductos($con);
$cantidad_almacenes = obtenerTotalAlmacenes($con);
$cantidad_proveedores = obtenerTotalProveedores($con);

// ESTOS SÍ CAMBIAN CON FILTROS
$cantidad_pedidos = obtenerTotalPedidos($con, $fecha_inicio, $fecha_fin);
$cantidad_compras = obtenerTotalCompras($con, $fecha_inicio, $fecha_fin);

// ========================================================================
// DATOS PARA GRÁFICOS DINÁMICOS (CON FILTROS)
// ========================================================================

// 3.a - Resumen general de órdenes
$resumen_ordenes = obtenerResumenOrdenes($con, $fecha_inicio, $fecha_fin);

// 3.b - Órdenes por almacén
$ordenes_por_almacen = obtenerOrdenesPorAlmacen($con, $fecha_inicio, $fecha_fin);

// 3.c - Pagos por almacén
$pagos_almacen = obtenerPagosPorAlmacen($con, $fecha_inicio, $fecha_fin);

// 3.d - Pagos por proveedor
$pagos_proveedor = obtenerPagosPorProveedor($con, $fecha_inicio, $fecha_fin, $proveedor);

// 3.e - Órdenes vencidas por proveedor por mes
$año_actual = date('Y');
$vencidas = obtenerOrdenesVencidasPorProveedorMes($con, $año_actual);

// Organizar datos de órdenes vencidas para la tabla
$proveedores_mes = [];
foreach($vencidas as $row) {
    $prov = $row['proveedor'];
    $mes = $row['mes'];
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