<!-- Vista Dashboard -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Dashboard Almacén <small>Análisis Integral</small></h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <!-- Panel de Filtros -->
    <div class="row">
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
            <form id="formFiltros" class="form-horizontal" method="GET" action="dashboard.php">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Fecha Inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?php echo $fecha_inicio; ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Fecha Fin:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                           value="<?php echo $fecha_fin; ?>">
                  </div>
                </div>
                <!--<div class="col-md-4">
                  <div class="form-group">
                    <label>Proveedor:</label>
                    <select class="form-control" id="proveedor" name="proveedor">
                      <option value="">Todos</option>
                      <?php foreach($lista_proveedores as $prov): ?>
                        <option value="<?php echo $prov['id_proveedor']; ?>"
                                <?php echo ($proveedor == $prov['id_proveedor']) ? 'selected' : ''; ?>>
                          <?php echo $prov['nom_proveedor']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>-->
                <!-- ⭐ PROVEEDOR MÚLTIPLE -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Proveedor(es):</label>
                        <select class="form-control select2-proveedores-dashboard" 
                                id="proveedor" 
                                name="proveedor[]" 
                                multiple>
                            <?php foreach($lista_proveedores as $prov): ?>
                                <option value="<?php echo $prov['id_proveedor']; ?>"
                                        <?php echo (in_array($prov['id_proveedor'], $proveedores_seleccionados)) ? 'selected' : ''; ?>>
                                    <?php echo $prov['nom_proveedor']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!--<div class="col-md-4">
                  <div class="form-group">
                    <label>Centro de Costo:</label>
                    <select class="form-control" id="centro_costo" name="centro_costo">
                      <option value="">Todos</option>
                      <?php foreach($lista_centros_costo as $cc): ?>
                        <option value="<?php echo $cc['id_area']; ?>"
                          <?php echo ($centro_costo == $cc['id_area']) ? 'selected' : ''; ?>>
                          <?php echo $cc['nom_area']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>-->
                <!-- ⭐ CENTRO DE COSTO MÚLTIPLE -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Centro(s) de Costo:</label>
                        <select class="form-control select2-centros-dashboard" 
                                id="centro_costo" 
                                name="centro_costo[]" 
                                multiple>
                            <?php foreach($lista_centros_costo as $cc): ?>
                                <option value="<?php echo $cc['id_area']; ?>"
                                        <?php echo (in_array($cc['id_area'], $centros_seleccionados)) ? 'selected' : ''; ?>>
                                    <?php echo $cc['nom_area']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 text-right">
                  <button type="submit" class="btn btn-primary">
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
    </div>

    <!-- Cards de Resumen -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_content">
            <div class="row">
              <div class="tile_count col-12">
                <div class="col-md-2 col-sm-6 tile_stats_count">
                  <span class="count_top"><i class="fa fa-cube"></i> Total Productos</span>
                  <div class="count"><?php echo $cantidad_productos; ?></div>
                  <span class="count_bottom">En el sistema</span>
                </div>
                <div class="col-md-3 col-sm-6 tile_stats_count">
                  <span class="count_top"><i class="fa fa-clipboard-list"></i> Pedidos</span>
                  <div class="count green"><?php echo $cantidad_pedidos; ?></div>
                  <span class="count_bottom"><i class="green">En el período</i></span>
                </div>
                <div class="col-md-2 col-sm-6 tile_stats_count">
                  <span class="count_top"><i class="fa fa-shopping-cart"></i> Compras</span>
                  <div class="count green"><?php echo $cantidad_compras; ?></div>
                  <span class="count_bottom"><i class="green">En el período</i></span>
                </div>
                <div class="col-md-3 col-sm-6 tile_stats_count">
                  <span class="count_top"><i class="fa fa-warehouse"></i> Total Almacenes</span>
                  <div class="count"><?php echo $cantidad_almacenes; ?></div>
                  <span class="count_bottom">En el sistema</span>
                </div>
                <div class="col-md-2 col-sm-6 tile_stats_count">
                  <span class="count_top"><i class="fa fa-truck"></i> Total Proveedores</span>
                  <div class="count"><?php echo $cantidad_proveedores; ?></div>
                  <span class="count_bottom">Activos</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.a: Estado General de Órdenes de Compra -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Estado General de Órdenes de Compra <small>Resumen del Período</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <?php 
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
                      <div class="progress-bar bg-blue" role="progressbar" style="width: 100%;"></div>
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
                           style="width: <?php echo $porcentaje_atendidas; ?>%;"></div>
                    </div>
                  </div>
                  <div class="w_right w_20">
                    <span><?php echo $resumen_ordenes['ordenes_atendidas']; ?></span>
                  </div>
                  <div class="clearfix"></div>
                  <p class="text-center">Atendidas (<?php echo $porcentaje_atendidas; ?>%)</p>
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
                           style="width: <?php echo $porcentaje_pendientes; ?>%;"></div>
                    </div>
                  </div>
                  <div class="w_right w_20">
                    <span><?php echo $resumen_ordenes['ordenes_pendientes']; ?></span>
                  </div>
                  <div class="clearfix"></div>
                  <p class="text-center">Pendientes (<?php echo $porcentaje_pendientes; ?>%)</p>
                </div>
              </div>
            </div>
            <div id="chart_ordenes_generales" style="width:100%; height:300px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.b: Ordenes de Compra por Almacén -->
    <!--<div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Órdenes de Compra por Almacén <small>Distribución</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Almacén</th>
                    <th>Total Órdenes</th>
                    <th>Atendidas</th>
                    <th>Pendientes</th>
                    <th>% Cumplimiento</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($ordenes_por_almacen)): ?>
                    <?php foreach($ordenes_por_almacen as $almacen): 
                      $porcentaje = $almacen['total_ordenes'] > 0 ? 
                        round(($almacen['ordenes_atendidas'] / $almacen['total_ordenes']) * 100, 2) : 0;
                    ?>
                    <tr>
                      <td><?php echo $almacen['almacen']; ?></td>
                      <td><?php echo $almacen['total_ordenes']; ?></td>
                      <td><span class="badge badge-success"><?php echo $almacen['ordenes_atendidas']; ?></span></td>
                      <td><span class="badge badge-warning"><?php echo $almacen['ordenes_pendientes']; ?></span></td>
                      <td>
                        <div class="progress" style="margin:0">
                          <div class="progress-bar bg-green" style="width:<?php echo $porcentaje; ?>%">
                            <?php echo $porcentaje; ?>%
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay datos para el período seleccionado</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div id="chart_almacen" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>-->

    <!-- Dashboard 3.b: Órdenes de Compra por Centro de Costo -->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Órdenes de Compra Por Centro de Costo <small>Distribución</small></h2>
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
                <tbody>
                  <?php if (!empty($ordenes_por_cc)): ?>
                    <?php foreach($ordenes_por_cc as $cc): 
                      $porcentaje = $cc['total_ordenes'] > 0 ?
                        round(($cc['atendidas'] / $cc['total_ordenes']) * 100, 2) : 0;
                    ?>
                    <tr>
                      <td><?php echo $cc['centro_costo']; ?></td>
                      <td><?php echo $cc['total_ordenes']; ?></td>
                      <td><span class="badge badge-success" style="font-size: 14px; padding: 6px 12px;"><?php echo $cc['atendidas']; ?></span></td>
                      <td><span class="badge badge-warning" style="font-size: 14px; padding: 6px 12px;"><?php echo $cc['pendientes']; ?></span></td>
                      <td>
                        <div class="progress" style="margin:0">
                          <div class="progress-bar bg-green" style="width:<?php echo $porcentaje; ?>%">
                            <?php echo $porcentaje; ?>%
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay datos para el período seleccionado</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div id="chart_almacen" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard 3.c: Pagos por Almacén -->
    <!--<div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Estado de Comprobantes por Almacén <small>Análisis Financiero</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Almacén</th>
                    <th>Total Comprobantes</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Monto Soles (S/)</th>
                    <th>Monto Dólares ($)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pagos_almacen)): ?>
                    <?php foreach($pagos_almacen as $pago): ?>
                    <tr>
                      <td><?php echo $pago['almacen']; ?></td>
                      <td><?php echo $pago['total_ordenes']; ?></td>
                      <td><span class="badge badge-success"><?php echo $pago['ordenes_pagadas']; ?></span></td>
                      <td><span class="badge badge-danger"><?php echo $pago['pendientes_pago']; ?></span></td>
                      <td><strong>S/ <?php echo number_format($pago['monto_total_soles'], 2); ?></strong></td>
                      <td><strong>$ <?php echo number_format($pago['monto_total_dolares'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay datos para el período seleccionado</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div id="chart_pagos_almacen" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>-->

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
                    <th>Total Órdenes</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Monto Pagado</th>
                    <th>Monto Pendiente</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pagos_por_cc)): ?>
                    <?php foreach($pagos_por_cc as $pago): ?>
                    <tr>
                      <td><?php echo $pago['centro_costo']; ?></td>
                      <td><?php echo $pago['total_ordenes']; ?></td>
                      <td><span class="badge badge-success" style="font-size: 14px; padding: 6px 12px;"><?php echo $pago['pagadas']; ?></span></td>
                      <td><span class="badge badge-danger" style="font-size: 14px; padding: 6px 12px;"><?php echo $pago['pendientes_pago']; ?></span></td>
                      <td><strong>S/ <?php echo number_format($pago['monto_pagado'], 2); ?></strong></td>
                      <td><strong>S/ <?php echo number_format($pago['monto_pendiente'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay datos para el período seleccionado</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div id="chart_pagos_almacen" style="width:100%; height:400px;"></div>
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
                    <th>Total órdenes</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Monto Pagado</th>
                    <th>Monto Pendiente</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pagos_por_proveedor)): ?>
                    <?php foreach($pagos_por_proveedor as $pago): ?>
                    <tr>
                      <td><?php echo $pago['proveedor']; ?></td>
                      <td><?php echo $pago['total_ordenes']; ?></td>
                      <td><span class="badge badge-success" style="font-size: 14px; padding: 6px 12px;"><?php echo $pago['pagadas']; ?></span></td>
                      <td><span class="badge badge-danger" style="font-size: 14px; padding: 6px 12px;"><?php echo $pago['pendientes_pago']; ?></span></td>
                      <td><strong>S/ <?php echo number_format($pago['monto_pagado'], 2); ?></strong></td>
                      <td><strong>S/ <?php echo number_format($pago['monto_pendiente'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay datos para el período seleccionado</td></tr>
                  <?php endif; ?>
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
                <tbody>
                  <?php if (!empty($proveedores_mes)): ?>
                    <?php foreach($proveedores_mes as $proveedor => $meses): ?>
                    <tr>
                      <td><strong><?php echo $proveedor; ?></strong></td>
                      <?php 
                      $total = 0;
                      for($m = 1; $m <= 12; $m++): 
                        $valor = $meses[$m];
                        $total += $valor;
                        $clase = $valor > 0 ? 'text-danger' : '';
                      ?>
                        <td class="<?php echo $clase; ?>"><?php echo $valor; ?></td>
                      <?php endfor; ?>
                      <td><strong class="text-danger"><?php echo $total; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="14" class="text-center">No hay órdenes vencidas en <?php echo date('Y'); ?></td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div id="chart_vencidas_mes" style="width:100%; height:400px;"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Scripts para gráficos -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart', 'bar']});
google.charts.setOnLoadCallback(drawAllCharts);

function limpiarFiltros() {
  window.location.href = 'dashboard.php';
}

function drawAllCharts() {
  drawOrdenesGeneralesChart();
  //drawAlmacenChart();
  drawCentroCostoChart();
  //drawPagosAlmacenChart();
  drawPagosCentroCostoChart();
  drawPagosProveedorChart();
  drawVencidasMesChart();
}

//NUEVO GRÁFICO Órdenes por Centro de Costo

function drawCentroCostoChart() {
  var data = google.visualization.arrayToDataTable([
    ['Centro Costo', 'Atendidas', 'Pendientes']
    <?php if (!empty($ordenes_por_cc)): ?>
      <?php foreach($ordenes_por_cc as $cc): ?>
        ,['<?php echo addslashes($cc['centro_costo']); ?>',
         <?php echo $cc['atendidas']; ?>,
         <?php echo $cc['pendientes']; ?>]
      <?php endforeach; ?>
    <?php else: ?>
      ,['Sin datos', 0, 0]
    <?php endif; ?>
  ]);

  var options = {
    title: 'Órdenes por Centro de Costo',
    chartArea: {width: '70%'},
    hAxis: { title: 'Centro de Costo' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#E74C3C'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_almacen'));
  chart.draw(data, options);
}

//NUEVO GRÁFICO Pagos por Centro de Costo

function drawPagosCentroCostoChart() {
  var data = google.visualization.arrayToDataTable([
    ['Centro Costo', 'Pagadas', 'Pendientes']
    <?php if (!empty($pagos_por_cc)): ?>
      <?php foreach($pagos_por_cc as $pago): ?>
        ,['<?php echo addslashes($pago['centro_costo']); ?>',
         <?php echo $pago['pagadas']; ?>,
         <?php echo $pago['pendientes_pago']; ?>]
      <?php endforeach; ?>
    <?php else: ?>
      ,['Sin datos', 0, 0]
    <?php endif; ?>
  ]);

  var options = {
    title: 'Pagos por Centro de Costo',
    chartArea: {width: '70%'},
    hAxis: { title: 'Centro de Costo' },
    vAxis: { title: 'Cantidad de Órdenes' },
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_pagos_almacen'));
  chart.draw(data, options);
}

//GRAFICOS ANTERIORES

function drawOrdenesGeneralesChart() {
  var data = google.visualization.arrayToDataTable([
    ['Estado', 'Cantidad'],
    ['Atendidas', <?php echo $resumen_ordenes['ordenes_atendidas']; ?>],
    ['Pendientes', <?php echo $resumen_ordenes['ordenes_pendientes']; ?>]
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

function drawAlmacenChart() {
  var data = google.visualization.arrayToDataTable([
    ['Almacén', 'Atendidas', 'Pendientes']
    <?php if (!empty($ordenes_por_almacen)): ?>
      <?php foreach($ordenes_por_almacen as $almacen): ?>
        ,['<?php echo addslashes($almacen['almacen']); ?>', 
         <?php echo $almacen['ordenes_atendidas']; ?>, 
         <?php echo $almacen['ordenes_pendientes']; ?>]
      <?php endforeach; ?>
    <?php else: ?>
      ,['Sin datos', 0, 0]
    <?php endif; ?>
  ]);

  var options = {
    title: 'Órdenes por Almacén',
    chartArea: {width: '70%'},
    hAxis: { title: 'Almacén' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#E74C3C'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_almacen'));
  chart.draw(data, options);
}

function drawPagosAlmacenChart() {
  var data = google.visualization.arrayToDataTable([
    ['Almacén', 'Pagadas', 'Pendientes']
    <?php if (!empty($pagos_almacen)): ?>
      <?php foreach($pagos_almacen as $pago): ?>
        ,['<?php echo addslashes($pago['almacen']); ?>', 
         <?php echo $pago['ordenes_pagadas']; ?>, 
         <?php echo $pago['pendientes_pago']; ?>]
      <?php endforeach; ?>
    <?php else: ?>
      ,['Sin datos', 0, 0]
    <?php endif; ?>
  ]);

  var options = {
    title: 'Estado de Pagos por Almacén',
    chartArea: {width: '70%'},
    hAxis: { title: 'Almacén' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#F39C12'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_pagos_almacen'));
  chart.draw(data, options);
}

function drawPagosProveedorChart() {
  var data = google.visualization.arrayToDataTable([
    ['Proveedor', 'Pagadas', 'Pendientes']
    <?php if (!empty($pagos_por_proveedor)): ?>
      <?php foreach($pagos_por_proveedor as $pago): ?>
        ,['<?php echo addslashes($pago['proveedor']); ?>', 
         <?php echo $pago['pagadas']; ?>, 
         <?php echo $pago['pendientes_pago']; ?>]
      <?php endforeach; ?>
    <?php else: ?>
      ,['Sin datos', 0, 0]
    <?php endif; ?>
  ]);

  var options = {
    title: 'Pagos por Proveedor',
    chartArea: {width: '70%'},
    hAxis: { title: 'Proveedor' },
    vAxis: { title: 'Cantidad de Órdenes' },
    colors: ['#26B99A', '#E74C3C'],
    isStacked: true
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_pagos_proveedor'));
  chart.draw(data, options);
}

function drawVencidasMesChart() {
  <?php if (!empty($proveedores_mes)): ?>
  var data = google.visualization.arrayToDataTable([
    ['Mes'
    <?php 
      $proveedores_unicos = array_keys($proveedores_mes);
      foreach($proveedores_unicos as $p) {
        echo ", '".addslashes($p)."'";  // ✅ Coma ANTES
      }
    ?>
    ],
    <?php
    $meses_nombre = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    for($m = 1; $m <= 12; $m++) {
      echo "['".$meses_nombre[$m-1]."'";
      foreach($proveedores_unicos as $p) {
        echo ", ".($proveedores_mes[$p][$m] ?? 0);  // ✅ Coma ANTES
      }
      echo ($m < 12 ? "]," : "]");  // ✅ Sin coma en el último
    }
    ?>
  ]);

  var options = {
    title: 'Órdenes Vencidas por Mes',
    chartArea: {width: '80%'},
    hAxis: { title: 'Mes' },
    vAxis: { title: 'Órdenes Vencidas', minValue: 0 },
    seriesType: 'bars',
    legend: { position: 'bottom' }
  };

  var chart = new google.visualization.ComboChart(document.getElementById('chart_vencidas_mes'));
  chart.draw(data, options);
  <?php else: ?>
  // Gráfico vacío cuando no hay datos
  var data = google.visualization.arrayToDataTable([
    ['Mes', 'Sin datos'],
    ['Ene', 0],
    ['Feb', 0],
    ['Mar', 0],
    ['Abr', 0],
    ['May', 0],
    ['Jun', 0],
    ['Jul', 0],
    ['Ago', 0],
    ['Sep', 0],
    ['Oct', 0],
    ['Nov', 0],
    ['Dic', 0]
  ]);
  
  var options = {
    title: 'Sin órdenes vencidas en el año actual',
    chartArea: {width: '80%'},
    hAxis: { title: 'Mes' },
    vAxis: { title: 'Órdenes Vencidas', minValue: 0 },
    legend: { position: 'none' }
  };
  
  var chart = new google.visualization.ColumnChart(document.getElementById('chart_vencidas_mes'));
  chart.draw(data, options);
  <?php endif; ?>
}

window.addEventListener('resize', function() {
  drawAllCharts();
});
</script>