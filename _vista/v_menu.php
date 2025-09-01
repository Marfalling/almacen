<?php
// v_menu.php - Sidebar dinámico basado en permisos CORREGIDO

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
          
          <!-- Dashboard - Siempre visible -->
          <li>
            <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
          </li>

          <?php if (verificarAccesoModulo('uso de material') || verificarAccesoModulo('pedidos') || verificarAccesoModulo('compras') || verificarAccesoModulo('ingresos') || verificarAccesoModulo('devoluciones')): ?>
          <li>
            <a><i class="fa fa-cogs"></i> Proceso <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <?php if (verificarAccesoModulo('uso de material')): ?>
              <li>
                <a>Uso de Material<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('uso de material', 'ver')): ?>
                  <li><a href="uso_material_mostrar.php">Lista de uso de material</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('uso de material', 'crear')): ?>
                  <li><a href="uso_material_nuevo.php">Nuevo uso de material</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('pedidos')): ?>
              <li>
                <a>Pedidos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('pedidos', 'ver')): ?>
                  <li><a href="pedidos_mostrar.php">Lista de pedidos</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('pedidos', 'crear')): ?>
                  <li><a href="pedidos_nuevo.php">Nuevo pedido</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('compras')): ?>
              <li>
                <a>Compras<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('compras', 'ver')): ?>
                  <li><a href="compras_mostrar.php">Lista de compras</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('compras', 'crear')): ?>
                  <li><a href="compras_nuevo.php">Nueva compra</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('ingresos')): ?>
              <li>
                <a>Ingresos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('ingresos', 'ver')): ?>
                  <li><a href="ingresos_mostrar.php">Lista de ingresos</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('ingresos', 'crear')): ?>
                  <li><a href="ingresos_nuevo.php">Nuevo ingreso</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('devoluciones')): ?>
              <li>
                <a>Devoluciones<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('devoluciones', 'ver')): ?>
                  <li><a href="devoluciones_mostrar.php">Lista de devoluciones</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('devoluciones', 'crear')): ?>
                  <li><a href="devoluciones_nuevo.php">Nueva devolución</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <?php if (verificarAccesoModulo('almacen arce') || verificarAccesoModulo('almacen clientes')): ?>
          <li>
            <a><i class="fa fa-warehouse"></i> Almacén <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <?php if (verificarAccesoModulo('almacen arce')): ?>
              <li><a href="almacen_arce.php">Almacén Arce</a></li>
              <?php endif; ?>
              
              <?php if (verificarAccesoModulo('almacen clientes')): ?>
              <li>
                <a>Almacén Clientes<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('almacen clientes', 'ver')): ?>
                  <li><a href="almacen_clientes_mostrar.php">Lista de almacenes</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('almacen clientes', 'crear')): ?>
                  <li><a href="almacen_clientes_nuevo.php">Nuevo almacén cliente</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <?php if (verificarAccesoModulo('personal') || verificarAccesoModulo('usuarios')): ?>
          <li>
            <a><i class="fa fa-users"></i> Personal <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              
              <?php if (verificarAccesoModulo('personal')): ?>
              <li>
                <a>Personal<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('personal', 'ver')): ?>
                  <li><a href="personal_mostrar.php">Listado de personal</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('personal', 'crear')): ?>
                  <li><a href="personal_nuevo.php">Nuevo personal</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('usuarios')): ?>
              <li>
                <a>Usuarios<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('usuarios', 'ver')): ?>
                  <li><a href="usuario_mostrar.php">Listado de usuarios</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('usuarios', 'crear')): ?>
                  <li><a href="usuario_nuevo.php">Nuevo usuario</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>
          
          <?php 
          // SECCIÓN DE MANTENIMIENTO
          $acceso_mantenimiento = verificarAccesoModulo('modulos') ||  // CORREGIDO: usar 'modulos' 
                                  verificarAccesoModulo('usuarios') || 
                                  verificarAccesoModulo('cliente') || 
                                  verificarAccesoModulo('almacen') ||
                                  verificarAccesoModulo('area') ||
                                  verificarAccesoModulo('cargo') ||
                                  verificarAccesoModulo('obras') ||
                                  verificarAccesoModulo('producto') ||
                                  verificarAccesoModulo('tipo de producto') ||
                                  verificarAccesoModulo('tipo de material') ||
                                  verificarAccesoModulo('unidad de medida') ||
                                  verificarAccesoModulo('proveedor') ||
                                  verificarAccesoModulo('moneda') ||
                                  verificarAccesoModulo('ubicacion');
          ?>
          <?php if ($acceso_mantenimiento): ?>
          <li>
            <a><i class="fa fa-wrench"></i> Mantenimiento <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">

              <?php if (verificarAccesoModulo('modulos')): ?>  <!-- CORREGIDO -->
              <li>
                <a>Módulos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarPermiso('modulos', 'ver')): ?>  <!-- CORREGIDO -->
                  <li><a href="modulo_mostrar.php">Módulo</a></li>
                  <?php endif; ?>
                  <?php if (verificarPermiso('modulos', 'crear')): ?>  <!-- CORREGIDO -->
                  <li><a href="modulo_nuevo.php">Nuevo módulo</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('usuarios') || verificarAccesoModulo('area') || verificarAccesoModulo('cargo')): ?>
              <li>
                <a>Personal<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarAccesoModulo('rol de usuario')): ?>
                  <li><a href="rol_usuario_mostrar.php">Rol de Usuario</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('area')): ?>
                  <li><a href="area_mostrar.php">Área</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('cargo')): ?>
                  <li><a href="cargo_mostrar.php">Cargo</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('cliente')): ?>
              <li><a href="clientes_mostrar.php">Clientes</a></li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('obras')): ?>
              <li><a href="obras_mostrar.php">Obras</a></li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('almacen') || verificarAccesoModulo('ubicacion')): ?>
              <li>
                <a>Almacenes<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarAccesoModulo('almacen')): ?>
                  <li><a href="almacen_mostrar.php">Almacén</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('ubicacion')): ?>
                  <li><a href="ubicacion_mostrar.php">Ubicación</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('producto') || verificarAccesoModulo('tipo de producto') || verificarAccesoModulo('tipo de material') || verificarAccesoModulo('unidad de medida')): ?>
              <li>
                <a>Productos<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarAccesoModulo('producto')): ?>
                  <li><a href="producto_mostrar.php">Producto</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('tipo de producto')): ?>
                  <li><a href="tipo_producto_mostrar.php">Tipo de producto</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('tipo de material')): ?>
                  <li><a href="tipo_material_mostrar.php">Tipo de material</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('unidad de medida')): ?>
                  <li><a href="unidad_medida_mostrar.php">Unidad de medida</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

              <?php if (verificarAccesoModulo('proveedor') || verificarAccesoModulo('moneda')): ?>
              <li>
                <a>Compras<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <?php if (verificarAccesoModulo('proveedor')): ?>
                  <li><a href="proveedor_mostrar.php">Proveedor</a></li>
                  <?php endif; ?>
                  <?php if (verificarAccesoModulo('moneda')): ?>
                  <li><a href="moneda_mostrar.php">Moneda</a></li>
                  <?php endif; ?>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php endif; ?>

          <?php if (verificarAccesoModulo('auditoria')): ?>
          <li>
            <a href="auditoria_mostrar.php"><i class="fa fa-clipboard-list"></i> Auditoría</a>
          </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
    <!-- /sidebar menu -->
  </div>
</div>