<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Dashboard Almacén <small></small></h3>
      </div>
    </div>

    <div class="clearfix"></div>
    <div class="row">
      <!-- --------------------------------------- -->
      <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
          <div class="x_content">
            <div class="row">
              <div class="tile_count col-12">
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-cube"></i> Total Productos</span>
                  <div class="count"><?php echo $cantidad_productos; ?></div>
                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-users"></i> Total Usuarios</span>
                  <div class="count"><?php echo $cantidad_usuarios; ?></div>
                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-clipboard-list"></i> Total Pedidos</span>
                  <div class="count"><?php echo $cantidad_pedidos; ?></div>
                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-shopping-cart"></i> Total Compras</span>
                  <div class="count"><?php echo $cantidad_compras; ?></div>
                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-warehouse"></i> Total Almacenes</span>
                  <div class="count"><?php echo $cantidad_almacenes; ?></div>
                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                  <span class="count_top"><i class="fa fa-truck"></i> Total Proveedores</span>
                  <div class="count"><?php echo $cantidad_proveedores; ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Primera fila de gráficos -->
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
                <h2>Compras por Proveedor <small>Total registradas</small></h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content2">
                <div id="chart_compras_proveedor" style="width:100%; height:300px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Segunda fila de gráficos -->
        <div class="row">
          <div class="col-md-6 col-sm-6">
            <div class="x_panel">
              <div class="x_title">
                <h2>Productos por Material <small>Clasificación por tipo</small></h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content2">
                <div id="chart_productos_material" style="width:100%; height:300px;"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-sm-6">
            <div class="x_panel">
              <div class="x_title">
                <h2>Estado de Pedidos <small>Distribución actual</small></h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content2">
                <div id="chart_estado_pedidos" style="width:100%; height:300px;"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- --------------------------------------- -->
    </div>
  </div>
</div>
<!-- /page content -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  // Cargar Google Charts
  google.charts.load('current', {'packages':['corechart', 'bar']});
  google.charts.setOnLoadCallback(drawAllCharts);

  function drawAllCharts() {
    drawTiposProductoChart();
    drawComprasProveedorChart();
    drawProductosMaterialChart();
    drawEstadoPedidosChart();
    drawProductosAlmacenChart();
  }

  // Gráfico de Tipos de Producto (Pie Chart)
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
      titleTextStyle: {
        fontSize: 14,
        bold: true
      },
      pieHole: 0.4,
      colors: ['#26B99A', '#34495E', '#3498DB', '#9B59B6', '#E74C3C', '#F39C12'],
      legend: {
        position: 'bottom',
        alignment: 'center'
      },
      chartArea: {
        left: 20,
        top: 40,
        width: '90%',
        height: '75%'
      }
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_tipos_producto'));
    chart.draw(data, options);
  }

  // Gráfico de Compras por Proveedor (Column Chart)
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
      titleTextStyle: {
        fontSize: 14,
        bold: true
      },
      colors: ['#26B99A'],
      hAxis: {
        title: 'Proveedores'
      },
      vAxis: {
        title: 'Número de Compras',
        minValue: 0
      },
      legend: { position: 'none' },
      chartArea: {
        left: 60,
        top: 40,
        width: '85%',
        height: '70%'
      }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_compras_proveedor'));
    chart.draw(data, options);
  }

  // Gráfico de Productos por Material (Donut Chart)
  function drawProductosMaterialChart() {
    var data = google.visualization.arrayToDataTable([
      ['Material', 'Cantidad'],
      <?php 
      if (!empty($datos_productos_por_material)) {
        foreach ($datos_productos_por_material as $dato) {
          echo "['" . addslashes($dato[0]) . "', " . $dato[1] . "],";
        }
      } else {
        echo "['Sin datos', 1],";
      }
      ?>
    ]);

    var options = {
      title: 'Productos por Tipo de Material',
      titleTextStyle: {
        fontSize: 14,
        bold: true
      },
      pieHole: 0.4,
      colors: ['#E74C3C', '#3498DB', '#26B99A', '#9B59B6', '#F39C12', '#34495E'],
      legend: {
        position: 'bottom',
        alignment: 'center'
      },
      chartArea: {
        left: 20,
        top: 40,
        width: '90%',
        height: '75%'
      }
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_productos_material'));
    chart.draw(data, options);
  }

  // Gráfico de Estado de Pedidos (Pie Chart)
  function drawEstadoPedidosChart() {
    var data = google.visualization.arrayToDataTable([
      ['Estado', 'Cantidad'],
      <?php 
      if (!empty($datos_estado_pedidos)) {
        foreach ($datos_estado_pedidos as $dato) {
          echo "['" . addslashes($dato[0]) . "', " . $dato[1] . "],";
        }
      } else {
        echo "['Sin datos', 1],";
      }
      ?>
    ]);

    var options = {
      title: 'Estado Actual de Pedidos',
      titleTextStyle: {
        fontSize: 14,
        bold: true
      },
      pieHole: 0.4,
      colors: ['#26B99A', '#E74C3C', '#F39C12'],
      legend: {
        position: 'bottom',
        alignment: 'center'
      },
      chartArea: {
        left: 20,
        top: 40,
        width: '90%',
        height: '75%'
      }
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_estado_pedidos'));
    chart.draw(data, options);
  }



  // Hacer los gráficos responsivos
  window.addEventListener('resize', function() {
    drawAllCharts();
  });
</script>