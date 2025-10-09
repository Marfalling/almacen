<?php 
//=======================================================================
// VISTA: v_movimiento_mostrar.php 
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Movimientos</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Listado de Movimientos</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <!-- ================== FILTRO DE FECHAS ================== -->
                        <form method="GET" class="form-inline mb-3">
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_inicio" class="mr-2">Desde:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                    value="<?= htmlspecialchars($fecha_inicio) ?>">
                            </div>
                            <div class="form-group mx-sm-2 mb-2">
                                <label for="fecha_fin" class="mr-2">Hasta:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                                    value="<?= htmlspecialchars($fecha_fin) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                        </form>
                        <!-- ======================================================= -->

                        <!-- Tabla de Movimientos -->
                        <div class="card-box table-responsive">
                            <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Nº Orden</th>
                                        <th>Personal</th>
                                        <th>Producto</th>
                                        <th>Almacén</th>
                                        <th>Ubicación</th>
                                        <th>Tipo Orden</th>
                                        <th>Tipo Movimiento</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = 1;
                                    foreach($movimientos as $mov) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $contador; ?></td>
                                        <td><?php echo (!empty($mov['fec_movimiento'])) ? date('d/m/Y H:i', strtotime($mov['fec_movimiento'])) : '-'; ?></td>
                                        <td><?php echo (!empty($mov['id_orden'])) ? $mov['id_orden'] : '-'; ?></td>
                                        <td><?php echo trim(($mov['nom_personal'] ?? '') . ' ' . ($mov['ape_personal'] ?? '')); ?></td>
                                        <td><?php echo $mov['nom_producto'] ?? '-'; ?></td>
                                        <td><?php echo $mov['nom_almacen'] ?? '-'; ?></td>
                                        <td><?php echo $mov['nom_ubicacion'] ?? '-'; ?></td>
                                        <td>
                                            <?php
                                            switch (intval($mov['tipo_orden'])) {
                                                case 1: echo '<span class="badge badge-info badge_size">INGRESO</span>'; break;
                                                case 2: echo '<span class="badge badge-warning badge_size">SALIDA</span>'; break;
                                                case 3: echo '<span class="badge badge-secondary badge_size">DEVOLUCIÓN</span>'; break;
                                                case 4: echo '<span class="badge badge-primary badge_size">USO</span>'; break;
                                                default: echo '<span class="badge badge-light badge_size">OTRO</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($mov['tipo_movimiento'] == 1) {
                                                echo '<span class="badge badge-success badge_size">Ingreso</span>';
                                            } elseif ($mov['tipo_movimiento'] == 2) {
                                                echo '<span class="badge badge-danger badge_size">Salida</span>';
                                            } else {
                                                echo '<span class="badge badge-secondary badge_size">Otro</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo number_format($mov['cant_movimiento'] ?? 0, 2); ?></td>
                                        <td>
                                            <?php 
                                            echo (intval($mov['est_movimiento']) === 1) 
                                                ? '<span class="badge badge-success badge_size">Activo</span>'
                                                : '<span class="badge badge-danger badge_size">Inactivo</span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        $contador++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Tabla -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->