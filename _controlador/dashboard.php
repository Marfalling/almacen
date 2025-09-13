<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_dashboard')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DASHBOARD', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}
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

      require_once("../_modelo/m_dashboard.php");

      // Variables globales para el dashboard 
      $cantidad_productos = obtenerTotalProductos($con);
      $cantidad_usuarios = obtenerTotalUsuarios($con);
      $cantidad_pedidos = obtenerTotalPedidos($con);
      $cantidad_compras = obtenerTotalCompras($con);
      $cantidad_almacenes = obtenerTotalAlmacenes($con);
      $cantidad_proveedores = obtenerTotalProveedores($con);
     
      // Datos para gráficos 
      $datos_tipos_producto = obtenerDatosGraficoTiposProducto($con);
      $datos_compras_por_proveedor = obtenerDatosGraficoComprasPorProveedor($con);
      $datos_productos_por_material = obtenerDatosGraficoProductosPorMaterial($con);
      $datos_estado_pedidos = obtenerDatosGraficoEstadoPedidos($con);

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