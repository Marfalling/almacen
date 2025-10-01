<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_dashboard')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DASHBOARD', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_dashboard.php");

// ========================================================================
// FILTROS DEL DASHBOARD
// ========================================================================
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
$centro_costo = isset($_GET['centro_costo']) ? $_GET['centro_costo'] : null;
$proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : null;

// Si no hay fechas, usar valores por defecto (último mes)
if (!$fecha_inicio && !$fecha_fin) {
    $fecha_inicio = date('Y-m-01'); // Primer día del mes actual
    $fecha_fin = date('Y-m-d');     // Fecha actual
}

// ========================================================================
// CARDS PRINCIPALES (sin filtros - datos generales)
// ========================================================================
$cantidad_productos = obtenerTotalProductos($con);
$cantidad_usuarios = obtenerTotalUsuarios($con);
$cantidad_pedidos = obtenerTotalPedidos($con);
$cantidad_compras = obtenerTotalCompras($con);
$cantidad_almacenes = obtenerTotalAlmacenes($con);
$cantidad_proveedores = obtenerTotalProveedores($con);

// ========================================================================
// DATOS PARA GRÁFICOS DINÁMICOS (CON FILTROS)
// ========================================================================

// 3.a - Resumen general de órdenes
$resumen_ordenes = obtenerResumenOrdenes($con, $fecha_inicio, $fecha_fin);

// 3.b - Órdenes por centro de costo
$ordenes_por_centro = obtenerOrdenesPorCentroCosto($con, $fecha_inicio, $fecha_fin);

// 3.c - Pagos por centro de costo
$pagos_centro = obtenerPagosPorCentroCosto($con, $fecha_inicio, $fecha_fin);

// 3.d - Pagos por proveedor
$pagos_proveedor = obtenerPagosPorProveedor($con, $fecha_inicio, $fecha_fin);

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

// ========================================================================
// GRÁFICOS ESTÁTICOS (sin filtros)
// ========================================================================
$datos_tipos_producto = obtenerDatosGraficoTiposProducto($con);
$datos_compras_por_proveedor = obtenerDatosGraficoComprasPorProveedor($con);
$datos_productos_por_material = obtenerDatosGraficoProductosPorMaterial($con);
$datos_estado_pedidos = obtenerDatosGraficoEstadoPedidos($con);

?>
<!DOCTYPE html>
<html lang="en">

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
      
      // Incluir la vista del dashboard
      require_once("../_vista/v_dashboard.php");

      require_once("../_vista/v_footer.php");
      ?>
    </div>
  </div>

  <?php
  require_once("../_vista/v_script.php");
  require_once("../_vista/v_alertas.php");
  ?>

  <script>
    // Script adicional para inicializar filtros con valores actuales
    document.addEventListener('DOMContentLoaded', function() {
      <?php if ($fecha_inicio): ?>
        document.getElementById('fecha_inicio').value = '<?php echo $fecha_inicio; ?>';
      <?php endif; ?>
      
      <?php if ($fecha_fin): ?>
        document.getElementById('fecha_fin').value = '<?php echo $fecha_fin; ?>';
      <?php endif; ?>
      
    });

    // Función mejorada para aplicar filtros
    function aplicarFiltros() {
      var fecha_inicio = document.getElementById('fecha_inicio').value;
      var fecha_fin = document.getElementById('fecha_fin').value;

      
      // Construir URL con parámetros
      var url = 'dashboard.php?';
      var params = [];
      
      if (fecha_inicio) params.push('fecha_inicio=' + fecha_inicio);
      if (fecha_fin) params.push('fecha_fin=' + fecha_fin);

      url += params.join('&');
      window.location.href = url;
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
      window.location.href = 'dashboard.php';
    }
  </script>

</body>

</html>