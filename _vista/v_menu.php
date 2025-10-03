<?php
// v_menu.php - Sidebar dinámico basado en permisos ACTUALIZADO
require_once("../_conexion/sesion.php");
?>

<div class="col-md-3 left_col">
  <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
      <a href="#" class="site_title"><span>ARCE PERÚ</span></a>
    </div>

    <div class="clearfix"></div>

    <!-- menu profile quick info -->
    <div class="profile clearfix">
      <div class="profile_pic">
        <img src="../_complemento/images/img.jpg" alt="..." class="img-circle profile_img">
      </div>
      <div class="profile_info">
        <span>Bienvenido(a),</span>
        <h2><?php echo $usuario_sesion; ?></h2>
      </div>
    </div>
    <!-- /menu profile quick info -->

    <br />

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>Opciones</h3>
        <ul class="nav side-menu">
         <!-- Página de Bienvenida - Siempre accesible -->
          <li>
            <a href="bienvenido.php"><i class="fa fa-home"></i> Inicio</a>
          </li>

          <?php if (tieneAccesoModulo('dashboard')): ?>
          <li>
            <a href="dashboard.php"><i class="fa fa-tachometer"></i> Dashboard</a>
          </li>
          <?php endif; ?>
          
          <!-- SECCIÓN PROCESO -->
          <?php if (tieneAccesoModulo('uso de material') || tieneAccesoModulo('pedidos') || 
                   tieneAccesoModulo('compras') || tieneAccesoModulo('ingresos') || 
                   tieneAccesoModulo('salidas') || tieneAccesoModulo('devoluciones') ||
                   tieneAccesoModulo('movimientos')): ?>
          <li>
            <a><i class="fa fa-cogs"></i> Proceso <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <!-- Uso de Material -->
              <?php if (tieneAccesoModulo('uso de material')): ?>
              <li>
                <a>Uso de Material<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_uso de material')): ?>
                  <li><a href="uso_material_mostrar.php">Lista de uso de material</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_uso de material')): ?>
                  <li><a href="uso_material_nuevo.php">Nuevo uso de material</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Pedidos -->
              <?php if (tieneAccesoModulo('pedidos')): ?>
              <li>
                <a>Pedidos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_pedidos')): ?>
                  <li><a href="pedidos_mostrar.php">Lista de pedidos</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_pedidos')): ?>
                  <li><a href="pedidos_nuevo.php">Nuevo pedido</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Compras -->
              <?php if (tieneAccesoModulo('compras')): ?>
              <li>
                <a>Compras<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_compras')): ?>
                  <li><a href="compras_mostrar.php">Lista de compras</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_compras')): ?>
                  <!-- <li><a href="compras_nuevo.php">Nueva compra</a></li> -->
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Ingresos -->
              <?php if (tieneAccesoModulo('ingresos')): ?>
              <li>
                <a>Ingresos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_ingresos')): ?>
                  <li><a href="ingresos_mostrar.php">Lista de ingresos</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_ingresos')): ?>
                  <li><a href="ingresos_directo_nuevo.php">Nuevo ingreso directo</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Salidas - NUEVO MÓDULO -->
              <?php if (tieneAccesoModulo('salidas')): ?>
              <li>
                <a>Salidas<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_salidas')): ?>
                  <li><a href="salidas_mostrar.php">Lista de salidas</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_salidas')): ?>
                  <li><a href="salidas_nuevo.php">Nueva salida</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Devoluciones -->
              <?php if (tieneAccesoModulo('devoluciones')): ?>
              <li>
                <a>Devoluciones<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_devoluciones')): ?>
                  <li><a href="devoluciones_mostrar.php">Lista de devoluciones</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_devoluciones')): ?>
                  <li><a href="devoluciones_nuevo.php">Nueva devolución</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Movimientos - NUEVO MÓDULO (solo lectura) -->
              <?php if (tieneAccesoModulo('movimientos')): ?>
              <li>
                <a>Movimientos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_movimientos')): ?>
                  <li><a href="movimientos.php">Lista de Movimientos</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <!-- SECCIÓN ALMACÉN -->
          <?php if (tieneAccesoModulo('almacen arce') || tieneAccesoModulo('almacen clientes')): ?>
          <li>
            <a><i class="fa fa-archive"></i> Almacén <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <!-- Almacén Arce -->
              <?php if (verificarPermisoEspecifico('ver_almacen arce')): ?>
              <li><a href="almacen_total_mostrar.php">Almacén Total</a></li> 
              <li><a href="almacen_arce_mostrar.php">Almacén Arce</a></li>
              <?php endif; ?>
              
              <!-- Almacén Clientes -->
              <?php if (tieneAccesoModulo('almacen clientes')): ?>
              <li><a href="almacen_clientes_mostrar.php">Almacén Clientes</a></li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <!-- SECCIÓN PERSONAL -->
          <?php if (tieneAccesoModulo('personal') || tieneAccesoModulo('usuarios')): ?>
          <li>
            <a><i class="fa fa-users"></i> Personal <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <!-- Personal -->
              <?php if (tieneAccesoModulo('personal')): ?>
              <li>
                <a>Personal<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_personal')): ?>
                  <li><a href="personal_mostrar.php">Listado de personal</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_personal')): ?>
                  <li><a href="personal_nuevo.php">Nuevo personal</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Usuarios -->
              <?php if (tieneAccesoModulo('usuarios')): ?>
              <li>
                <a>Usuarios<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_usuarios')): ?>
                  <li><a href="usuario_mostrar.php">Listado de usuarios</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_usuarios')): ?>
                  <li><a href="usuario_nuevo.php">Nuevo usuario</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>
          
          <!-- SECCIÓN MANTENIMIENTO -->
          <?php 
          $acceso_mantenimiento = tieneAccesoModulo('modulos') || 
                                  tieneAccesoModulo('usuarios') || 
                                  tieneAccesoModulo('cliente') || 
                                  tieneAccesoModulo('almacen') ||
                                  tieneAccesoModulo('area') ||
                                  tieneAccesoModulo('cargo') ||
                                  tieneAccesoModulo('obras') ||
                                  tieneAccesoModulo('producto') ||
                                  tieneAccesoModulo('tipo de producto') ||
                                  tieneAccesoModulo('tipo de material') ||
                                  tieneAccesoModulo('unidad de medida') ||
                                  tieneAccesoModulo('proveedor') ||
                                  tieneAccesoModulo('moneda') ||
                                  tieneAccesoModulo('ubicacion');
          ?>
          
          <?php if ($acceso_mantenimiento): ?>
          <li>
            <a><i class="fa fa-wrench"></i> Mantenimiento <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">

              <!-- Módulos -->
              <?php if (tieneAccesoModulo('modulos')): ?>
              <li>
                <a>Módulos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_modulos')): ?>
                  <li><a href="modulo_mostrar.php">Módulo</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('crear_modulos')): ?>
                  <li><a href="modulo_nuevo.php">Nuevo módulo</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Configuración Personal -->
              <?php if (tieneAccesoModulo('rol de usuario') || 
                        tieneAccesoModulo('area') || 
                        tieneAccesoModulo('cargo')): ?>
              <li>
                <a>Configuración Personal<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_rol de usuario')): ?>
                  <li><a href="rol_usuario_mostrar.php">Rol de Usuario</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_area')): ?>
                  <li><a href="area_mostrar.php">Área</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_cargo')): ?>
                  <li><a href="cargo_mostrar.php">Cargo</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Clientes -->
              <?php if (verificarPermisoEspecifico('ver_cliente')): ?>
              <li><a href="clientes_mostrar.php">Clientes</a></li>
              <?php endif; ?>

              <!-- Obras -->
              <?php if (verificarPermisoEspecifico('ver_obras')): ?>
              <li><a href="obras_mostrar.php">Obras</a></li>
              <?php endif; ?>

              <!-- Almacenes -->
              <?php if (tieneAccesoModulo('almacen') || tieneAccesoModulo('ubicacion')): ?>
              <li>
                <a>Almacenes<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_almacen')): ?>
                  <li><a href="almacen_mostrar.php">Almacén</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_ubicacion')): ?>
                  <li><a href="ubicacion_mostrar.php">Ubicación</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Productos -->
              <?php if (tieneAccesoModulo('producto') || 
                        tieneAccesoModulo('tipo de producto') || 
                        tieneAccesoModulo('tipo de material') || 
                        tieneAccesoModulo('unidad de medida')): ?>
              <li>
                <a>Productos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_producto')): ?>
                  <li><a href="producto_mostrar.php">Producto</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_tipo de producto')): ?>
                  <li><a href="tipo_producto_mostrar.php">Tipo de producto</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_tipo de material')): ?>
                  <li><a href="tipo_material_mostrar.php">Tipo de material</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermisoEspecifico('ver_unidad de medida')): ?>
                  <li><a href="unidad_medida_mostrar.php">Unidad de medida</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <!-- Compras -->
              <?php if (tieneAccesoModulo('proveedor') || tieneAccesoModulo('moneda') || tieneAccesoModulo('detraccion')): ?>
              <li>
                <a>Compras<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermisoEspecifico('ver_proveedor')): ?>
                  <li><a href="proveedor_mostrar.php">Proveedor</a></li>
                  <?php endif; ?>

                  <?php if (verificarPermisoEspecifico('ver_moneda')): ?>
                  <li><a href="moneda_mostrar.php">Moneda</a></li>
                  <?php endif; ?>

                  <?php if (verificarPermisoEspecifico('ver_detraccion')): ?>
                  <li><a href="detraccion_mostrar.php">Detracción</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <!-- AUDITORÍA -->
          <?php if (verificarPermisoEspecifico('ver_auditoria')): ?>
          <li>
            <a href="auditoria_mostrar.php"><i class="fa fa-eye"></i> Auditoría</a>
          </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
    <!-- /sidebar menu -->
  </div>
</div>