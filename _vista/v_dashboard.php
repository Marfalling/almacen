<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Dashboard Almacén <small>Análisis Integral</small></h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <!-- Panel de Filtros -->
    
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Filtros de Dashboard</h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="formFiltros" class="form-horizontal">
              <div class="form-group">
                <div class="col-md-3">
                  <label>Fecha Inicio:</label>
                  <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                </div>
                <div class="col-md-3">
                  <label>Fecha Fin:</label>
                  <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                    <i class="fa fa-search"></i> Aplicar Filtros
                  </button>
                  <button type="button" class="btn btn-default" onclick="limpiarFiltros()">
                    <i class="fa fa-eraser"></i> Limpiar
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    <!-- Cards de Resumen -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_content">
            <div class="row">
              <div class="tile_count col-12">
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-cube"></i> Total Productos</span>
                  <div class="count"><?php echo $cantidad_productos; ?></div>
                </div>
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-users"></i> Total Usuarios</span>
                  <div class="count"><?php echo $cantidad_usuarios; ?></div>
                </div>
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-clipboard-list"></i> Total Pedidos</span>
                  <div class="count"><?php echo $cantidad_pedidos; ?></div>
                </div>
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-shopping-cart"></i> Total Compras</span>
                  <div class="count"><?php echo $cantidad_compras; ?></div>
                </div>
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-warehouse"></i> Total Almacenes</span>
                  <div class="count"><?php echo $cantidad_almacenes; ?></div>
                </div>
                <div class="col-md-2 col-sm-4 tile_stats_count">
                  <span class="count_top"><i class="fa fa-truck"></i> Total Proveedores</span>
                  <div class="count"><?php echo $cantidad_proveedores; ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.a: Ordenes Generadas, Atendidas, Pendientes -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Estado General de Órdenes <small>Resumen Global</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <?php 
            $resumen_ordenes = obtenerResumenOrdenes($con);
            $porcentaje_atendidas = $resumen_ordenes['total_ordenes'] > 0 ? 
                round(($resumen_ordenes['ordenes_atendidas'] / $resumen_ordenes['total_ordenes']) * 100, 2) : 0;
            $porcentaje_pendientes = $resumen_ordenes['total_ordenes'] > 0 ?
                round(($resumen_ordenes['ordenes_pendientes'] / $resumen_ordenes['total_ordenes']) * 100, 2) : 0;
            ?>
            <div class="row">
              <div class="col-md-4">
                <div class="widget_summary">
                  <div class="w_left w_25">
                    <span><i class="fa fa-list"></i></span>
                  </div>
                  <div class="w_center w_55">
                    <div class="progress">
                      <div class="progress-bar bg-blue" role="progressbar" style="width: 100%;">
                      </div>
                    </div>
                  </div>
                  <div class="w_right w_20">
                    <span><?php echo $resumen_ordenes['total_ordenes']; ?></span>
                  </div>
                  <div class="clearfix"></div>
                  <p class="text-center">Total de Órdenes</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="widget_summary">
                  <div class="w_left w_25">
                    <span><i class="fa fa-check"></i></span>
                  </div>
                  <div class="w_center w_55">
                    <div class="progress">
                      <div class="progress-bar bg-green" role="progressbar" 
                           style="width: <?php echo $porcentaje_atendidas; ?>%;">
                      </div>
                    </div>
                  </div>
                  <div class="w_right w_20">
                    <span><?php echo $resumen_ordenes['ordenes_atendidas']; ?></span>
                  </div>
                  <div class="clearfix"></div>
                  <p class="text-center">Órdenes Atendidas (<?php echo $porcentaje_atendidas; ?>%)</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="widget_summary">
                  <div class="w_left w_25">
                    <span><i class="fa fa-clock"></i></span>
                  </div>
                  <div class="w_center w_55">
                    <div class="progress">
                      <div class="progress-bar bg-orange" role="progressbar" 
                           style="width: <?php echo $porcentaje_pendientes; ?>%;">
                      </div>
                    </div>
                  </div>
                  <div class="w_right w_20">
                    <span><?php echo $resumen_ordenes['ordenes_pendientes']; ?></span>
                  </div>
                  <div class="clearfix"></div>
                  <p class="text-center">Órdenes Pendientes (<?php echo $porcentaje_pendientes; ?>%)</p>
                </div>
              </div>
            </div>
            <div id="chart_ordenes_generales" style="width:100%; height:300px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.b: Ordenes por Centro de Costo -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Órdenes por Centro de Costo <small>Atendidas vs Pendientes</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Centro de Costo</th>
                    <th>Total Órdenes</th>
                    <th>Atendidas</th>
                    <th>Pendientes</th>
                    <th>% Cumplimiento</th>
                  </tr>
                </thead>
                <tbody id="tabla_centro_costo">
                  <?php
                  $ordenes_centro = obtenerOrdenesPorCentroCosto($con);
                  foreach($ordenes_centro as $centro) {
                    $porcentaje = $centro['total_ordenes'] > 0 ? 
                      round(($centro['ordenes_atendidas'] / $centro['total_ordenes']) * 100, 2) : 0;
                    echo "<tr>";
                    echo "<td>".$centro['centro_costo']."</td>";
                    echo "<td>".$centro['total_ordenes']."</td>";
                    echo "<td><span class='badge badge-success'>".$centro['ordenes_atendidas']."</span></td>";
                    echo "<td><span class='badge badge-warning'>".$centro['ordenes_pendientes']."</span></td>";
                    echo "<td>
                            <div class='progress' style='margin:0'>
                              <div class='progress-bar' style='width:".$porcentaje."%'>".$porcentaje."%</div>
                            </div>
                          </td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div id="chart_centro_costo" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.c: Pagos por Centro de Costo -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Estado de Pagos por Centro de Costo <small>Análisis Financiero</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Centro de Costo</th>
                    <th>Moneda</th>
                    <th>Total Órdenes</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Monto Total</th>
                  </tr>
                </thead>
                <tbody id="tabla_pagos_centro">
                  <?php
                  $pagos_centro = obtenerPagosPorCentroCosto($con);
                  foreach($pagos_centro as $pago) {
                    echo "<tr>";
                    echo "<td>".$pago['centro_costo']."</td>";
                    echo "<td>".$pago['moneda']."</td>";
                    echo "<td>".$pago['total_ordenes']."</td>";
                    echo "<td><span class='badge badge-success'>".$pago['ordenes_pagadas']."</span></td>";
                    echo "<td><span class='badge badge-danger'>".$pago['pendientes_pago']."</span></td>";
                    echo "<td><strong>".number_format($pago['monto_total'], 2)."</strong></td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div id="chart_pagos_centro" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.d: Pagos por Proveedor -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Estado de Pagos por Proveedor <small>Análisis de Proveedores</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Proveedor</th>
                    <th>Moneda</th>
                    <th>Total Órdenes</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Monto Total</th>
                  </tr>
                </thead>
                <tbody id="tabla_pagos_proveedor">
                  <?php
                  $pagos_proveedor = obtenerPagosPorProveedor($con);
                  foreach($pagos_proveedor as $pago) {
                    echo "<tr>";
                    echo "<td>".$pago['proveedor']."</td>";
                    echo "<td>".$pago['moneda']."</td>";
                    echo "<td>".$pago['total_ordenes']."</td>";
                    echo "<td><span class='badge badge-success'>".$pago['ordenes_pagadas']."</span></td>";
                    echo "<td><span class='badge badge-danger'>".$pago['pendientes_pago']."</span></td>";
                    echo "<td><strong>".number_format($pago['monto_total'], 2)."</strong></td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div id="chart_pagos_proveedor" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.e: Órdenes Vencidas por Proveedor por Mes -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Órdenes Vencidas por Proveedor <small>Análisis Mensual <?php echo date('Y'); ?></small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Proveedor</th>
                    <th>Ene</th>
                    <th>Feb</th>
                    <th>Mar</th>
                    <th>Abr</th>
                    <th>May</th>
                    <th>Jun</th>
                    <th>Jul</th>
                    <th>Ago</th>
                    <th>Sep</th>
                    <th>Oct</th>
                    <th>Nov</th>
                    <th>Dic</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="tabla_vencidas_mes">
                  <?php
                  $vencidas = obtenerOrdenesVencidasPorProveedorMes($con);
                  $proveedores_mes = [];
                  
                  // Organizar datos por proveedor y mes
                  foreach($vencidas as $row) {
                    $prov = $row['proveedor'];
                    $mes = $row['mes'];
                    if (!isset($proveedores_mes[$prov])) {
                      $proveedores_mes[$prov] = array_fill(1, 12, 0);
                    }
                    $proveedores_mes[$prov][$mes] = $row['ordenes_vencidas'];
                  }
                  
                  // Mostrar tabla
                  foreach($proveedores_mes as $proveedor => $meses) {
                    echo "<tr>";
                    echo "<td><strong>".$proveedor."</strong></td>";
                    $total = 0;
                    for($m = 1; $m <= 12; $m++) {
                      $valor = $meses[$m];
                      $total += $valor;
                      $clase = $valor > 0 ? 'text-danger' : '';
                      echo "<td class='".$clase."'>".$valor."</td>";
                    }
                    echo "<td><strong>".$total."</strong></td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div id="chart_vencidas_mes" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos originales -->
    <div class="row">
      <div class="col-md-6 col-sm-6">
        <div class="x_panel">
          <div class="x_title">
            <h2>Productos por Tipo <small>Distribución actual</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content2">
            <div id="chart_tipos_producto" style="width:100%; height:300px;"></div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-sm-6">
        <div class="x_panel">
          <div class="x_title">
            <h2>Compras por Proveedor <small>Top 10</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content2">
            <div id="chart_compras_proveedor" style="width:100%; height:300px;"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<!-- /page content -->

<!-- Scripts para gráficos y funcionalidad -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
// Cargar Google Charts
google.charts.load('current', {'packages':['corechart', 'bar', 'table']});
google.charts.setOnLoadCallback(drawAllCharts);

// Función para mostrar/ocultar filtros
function toggleFiltros() {
  var panel = document.getElementById('panelFiltros');
  if (panel.style.display === 'none') {
    panel.style.display = 'block';
  } else {
    panel.style.display = 'none';
  }
}

// Función para aplicar filtros
function aplicarFiltros() {
  var formData = {
    fecha_inicio: document.getElementById('fecha_inicio').value,
    fecha_fin: document.getElementById('fecha_fin').value,
    centro_costo: document.getElementById('centro_costo').value,
    proveedor: document.getElementById('proveedor').value
  };
  
  // Hacer petición AJAX para actualizar dashboard
  $.ajax({
    url: '../_controlador/ajax_dashboard.php',
    type: 'POST',
    data: formData,
    success: function(response) {
      // Actualizar contenido del dashboard
      location.reload(); // Por simplicidad, recargar página
    },
    error: function() {
      alert('Error al aplicar filtros');
    }
  });
}

// Función para limpiar filtros
function limpiarFiltros() {
  document.getElementById('formFiltros').reset();
  location.reload();
}

// Dibujar todos los gráficos
function drawAllCharts() {
  drawOrdenesGeneralesChart();
  drawCentroCostoChart();
  drawPagosCentroChart();
  drawPagosProveedorChart();
  drawVencidasMesChart();
  drawTiposProductoChart();
  drawComprasProveedorChart();
}

// Gráfico 3.a: Estado general de órdenes
function drawOrdenesGeneralesChart() {
  var data = google.visualization.arrayToDataTable([
    ['Estado', 'Cantidad'],
    <?php 
    $resumen = obtenerResumenOrdenes($con);
    echo "['Atendidas', ".$resumen['ordenes_atendidas']."],";
    echo "['Pendientes', ".$resumen['ordenes_pendientes']."],";
    ?>
  ]);

  var options = {
    title: 'Distribución de Estado de Órdenes',
    pieHole: 0.4,
    colors: ['#26B99A', '#E74C3C'],
    legend: { position: 'bottom' },
    chartArea: { width: '90%', height: '75%' }
  };

  var chart = new google.visualization.PieChart(document.getElementById('chart_ordenes_generales'));
  chart.draw(data, options);
}

// Gráfico 3.b: Órdenes por centro de costo
function drawCentroCostoChart() {
  var data = google.visualization.arrayToDataTable([
    ['Centro de Costo', 'Atendidas', 'Pendientes'],
    <?php 
    $ordenes = obtenerOrdenesPorCentroCosto($con);
    foreach($ordenes as $centro) {
      echo "['".$centro['centro_costo']."', ".$centro['ordenes_atendidas'].", ".$centro['ordenes_pendientes']."],";
    }
    ?>
  ]);

  var options = {
    title: 'Órdenes por Centro de Costo',
    chartArea: {width: '70%'},
    hAxis: { title: 'Centro de Costo' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#E74C3C'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_centro_costo'));
  chart.draw(data, options);
}

// Gráfico 3.c: Pagos por centro de costo
function drawPagosCentroChart() {
  var data = google.visualization.arrayToDataTable([
    ['Centro de Costo', 'Pagadas', 'Pendientes'],
    <?php 
    $pagos = obtenerPagosPorCentroCosto($con);
    foreach($pagos as $pago) {
      echo "['".$pago['centro_costo']."', ".$pago['ordenes_pagadas'].", ".$pago['pendientes_pago']."],";
    }
    ?>
  ]);

  var options = {
    title: 'Estado de Pagos por Centro de Costo',
    chartArea: {width: '70%'},
    hAxis: { title: 'Centro de Costo' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#F39C12'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_pagos_centro'));
  chart.draw(data, options);
}

// Gráfico 3.d: Pagos por proveedor
function drawPagosProveedorChart() {
  var data = google.visualization.arrayToDataTable([
    ['Proveedor', 'Pagadas', 'Pendientes'],
    <?php 
    $pagos = obtenerPagosPorProveedor($con);
    foreach($pagos as $pago) {
      echo "['".$pago['proveedor']."', ".$pago['ordenes_pagadas'].", ".$pago['pendientes_pago']."],";
    }
    ?>
  ]);

  var options = {
    title: 'Estado de Pagos por Proveedor',
    chartArea: {width: '70%'},
    hAxis: { title: 'Proveedor', slantedText: true, slantedTextAngle: 45 },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#E74C3C'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_pagos_proveedor'));
  chart.draw(data, options);
}

// Gráfico 3.e: Órdenes vencidas por mes
function drawVencidasMesChart() {
  var data = google.visualization.arrayToDataTable([
    ['Mes', <?php 
      $proveedores_unicos = array_keys($proveedores_mes ?? []);
      foreach($proveedores_unicos as $p) {
        echo "'".$p."', ";
      }
    ?>],
    <?php
    $meses_nombre = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    for($m = 1; $m <= 12; $m++) {
      echo "['".$meses_nombre[$m-1]."', ";
      foreach($proveedores_unicos as $p) {
        echo ($proveedores_mes[$p][$m] ?? 0).", ";
      }
      echo "],";
    }
    ?>
  ]);

  var options = {
    title: 'Órdenes Vencidas por Proveedor y Mes',
    chartArea: {width: '80%'},
    hAxis: { title: 'Mes' },
    vAxis: { title: 'Órdenes Vencidas' },
    seriesType: 'bars',
    series: {5: {type: 'line'}}
  };

  var chart = new google.visualization.ComboChart(document.getElementById('chart_vencidas_mes'));
  chart.draw(data, options);
}

// Gráfico de Tipos de Producto (original)
function drawTiposProductoChart() {
  var data = google.visualization.arrayToDataTable([
    ['Tipo', 'Cantidad'],
    <?php 
    if (!empty($datos_tipos_producto)) {
      foreach ($datos_tipos_producto as $dato) {
        echo "['" . addslashes($dato[0]) . "', " . $dato[1] . "],";
      }
    } else {
      echo "['Sin datos', 1],";
    }
    ?>
  ]);

  var options = {
    title: 'Distribución de Productos por Tipo',
    pieHole: 0.4,
    colors: ['#26B99A', '#34495E', '#3498DB'],
    legend: { position: 'bottom' },
    chartArea: { width: '90%', height: '75%' }
  };

  var chart = new google.visualization.PieChart(document.getElementById('chart_tipos_producto'));
  chart.draw(data, options);
}

// Gráfico de Compras por Proveedor (original)
function drawComprasProveedorChart() {
  var data = google.visualization.arrayToDataTable([
    ['Proveedor', 'Compras'],
    <?php 
    if (!empty($datos_compras_por_proveedor)) {
      foreach ($datos_compras_por_proveedor as $dato) {
        echo "['" . addslashes($dato[0]) . "', " . $dato[1] . "],";
      }
    } else {
      echo "['Sin datos', 0],";
    }
    ?>
  ]);

  var options = {
    title: 'Compras Registradas por Proveedor',
    colors: ['#26B99A'],
    hAxis: { title: 'Proveedores' },
    vAxis: { title: 'Número de Compras', minValue: 0 },
    legend: { position: 'none' },
    chartArea: { left: 60, top: 40, width: '85%', height: '70%' }
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_compras_proveedor'));
  chart.draw(data, options);
}

// Hacer los gráficos responsivos
window.addEventListener('resize', function() {
  drawAllCharts();
});
</script>