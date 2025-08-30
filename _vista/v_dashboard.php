        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Dashboard <small></small></h3>
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
                          <span class="count_top"><i class="fa fa-user"></i> Total Usuarios</span>
                          <div class="count"><?php echo $cantidad_usuarios; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                          <span class="count_top"><i class="fa fa-users"></i> Total Clientes</span>
                          <div class="count"><?php echo $cantidad_clientes; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                          <span class="count_top"><i class="fa fa-bolt"></i> Total Equipos</span>
                          <div class="count"><?php echo $cantidad_equipos; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                          <span class="count_top"><i class="fa fa-pie-chart"></i> Total Recepciones</span>
                          <div class="count"><?php echo $cantidad_recepciones; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                          <span class="count_top"><i class="fa fa-flask"></i> Total Muestras</span>
                          <div class="count"><?php echo $cantidad_muestras; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                          <span class="count_top"><i class="fa fa-bar-chart-o"></i> Total Resultados</span>
                          <div class="count"><?php echo $cantidad_resultados; ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 col-sm-8">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Registro De Muestras Individuales <small>Últimos 12 Meses</small></h2>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content2">
                        <div id="graph_area" style="width:100%; height:300px;"></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4 col-sm-4">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Tipos De Equipos</h2>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content2">
                        <div id="graph_donut" style="width:100%; height:300px;"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 col-sm-4">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Tipo De Usuario</h2>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content2">
                        <div id="user_donut" style="width:100%; height:300px;"></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-8 col-sm-8">
                    <div class="x_panel">
                      <div class="x_title">
                        <h2>Ingresos <small>Últimos 12 Meses</small></h2>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div id="graph_bar" style="width:100%; height:280px;"></div>
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