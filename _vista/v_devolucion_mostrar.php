<?php 
//=======================================================================
// VISTA: v_devoluciones_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Devoluciones<small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Devoluciones<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="devoluciones_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva Devolución</a>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="devoluciones_mostrar.php" class="form-inline mb-3">
                            <label for="fecha_inicio" class="mr-2">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_inicio); ?>">

                            <label for="fecha_fin" class="mr-2">Hasta:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control mr-2"
                                value="<?php echo htmlspecialchars($fecha_fin); ?>">

                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nº Documento</th>
                                                <th>Almacén</th>
                                                <th>Ubicación</th>
                                                <th>Cliente destino</th>
                                                <th>Registrado por</th>
                                                <th>Fecha de Devolución</th>
                                                <th>Observaciones</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($devoluciones as $devolucion) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo 'U00' . $devolucion['id_devolucion']; ?></td>
                                                    <td><?php echo $devolucion['nom_almacen']; ?></td>
                                                    <td><?php echo $devolucion['nom_ubicacion']; ?></td>
                                                    <td><?php echo $devolucion['nom_cliente_destino']; ?></td>
                                                    <td><?php echo $devolucion['nom_personal']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($devolucion['fec_devolucion'])); ?></td>
                                                    <td><?php echo $devolucion['obs_devolucion']; ?></td>
                                                    <td>
                                                        <?php if($devolucion['est_devolucion'] == 1) { ?>
                                                            <span class="badge badge-success badge_size">Activo</span>
                                                        <?php } elseif($devolucion['est_devolucion'] == 2) { ?>
                                                            <span class="badge badge-success badge_size">Confirmado</span>
                                                          <?php } else { ?>
                                                            <span class="badge badge-danger badge_size">Anulado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">

                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDetalleDevolucion<?php echo $devolucion['id_devolucion']; ?>" 
                                                                    title="Ver Detalle">
                                                                <i class="fa fa-eye"></i>
                                                            </button>

                                                            <?php if ($devolucion['est_devolucion'] != 1) { ?>
                                                                <a href="#"
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                title="Editar"
                                                                tabindex="-1"
                                                                aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <a href="<?php echo ($devolucion['est_devolucion'] == 1) 
                                                                            ? 'devoluciones_editar.php?id='.$devolucion['id_devolucion'] 
                                                                            : '#'; ?>" 
                                                                class="btn btn-warning btn-sm" 
                                                                title="Editar"
                                                                <?php echo ($devolucion['est_devolucion'] != 1) ? 'onclick="return false;" style="pointer-events:none; opacity:0.65;"' : ''; ?>>
                                                                <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } ?>

                                                            <a href="devoluciones_pdf.php?id=<?php echo $devolucion['id_devolucion']; ?>" 
                                                               class="btn btn-secondary btn-sm" 
                                                               title="Generar PDF"
                                                               target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>

                                                            <form method="post" action="devoluciones_mostrar.php" style="display:inline;">
                                                                <input type="hidden" name="id_devolucion" value="<?php echo $devolucion['id_devolucion']; ?>">
                                                                <input type="hidden" name="confirmar" value="1">

                                                                <?php if ($devolucion['est_devolucion'] != 1) { ?>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm disabled" 
                                                                            title="Confirmar Devolución" 
                                                                            disabled>
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button type="button" 
                                                                            name="confirmar" 
                                                                            class="btn btn-success btn-sm btn-confirmar" 
                                                                            title="Confirmar Devolución">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                <?php } ?>
                                                            </form>


                                                            <form method="post" action="devoluciones_mostrar.php" style="display:inline;">
                                                                <input type="hidden" name="id_devolucion" value="<?php echo $devolucion['id_devolucion']; ?>">
                                                                <input type="hidden" name="anular" value="1">

                                                                <?php if ($devolucion['est_devolucion'] != 1) { ?>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm disabled" 
                                                                            title="Anular Devolución" 
                                                                            disabled>
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button type="button" 
                                                                            name="anular" 
                                                                            class="btn btn-danger btn-sm btn-anular" 
                                                                            title="Anular Devolución">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                <?php } ?>
                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php 
                                                $contador++;
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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

<!-- Modales para ver detalle de cada devolución -->
<?php 
foreach($devoluciones as $devolucion) { 
    $dev_data = ConsultarDevolucion($devolucion['id_devolucion']);
    $dev_detalle = ConsultarDevolucionDetalle($devolucion['id_devolucion']);
    
    if (!empty($dev_data)) {
        $dev_info = $dev_data[0];
?>
<div class="modal fade" id="modalDetalleDevolucion<?php echo $devolucion['id_devolucion']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetalleDevolucionLabel<?php echo $devolucion['id_devolucion']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleDevolucionLabel<?php echo $devolucion['id_devolucion']; ?>">
                    Detalle de Devolución - <?php echo $dev_info['id_devolucion']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Información General</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>ID Devolución:</strong></td>
                                <td><?php echo $dev_info['id_devolucion']; ?></td>
                                <td><strong>Fecha y hora:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($dev_info['fec_devolucion'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Almacén:</strong></td>
                                <td><?php echo $dev_info['nom_almacen']; ?></td>
                                <td><strong>Ubicación:</strong></td>
                                <td><?php echo $dev_info['nom_ubicacion']; ?></td>
                                <tr>
                                    <td><strong>Cliente destino:</strong></td>
                                    <td colspan="3"><?php echo $dev_info['nom_cliente_destino']; ?></td>
                                </tr>
                            </tr>
                            <tr>
                                <td><strong>Registrado por:</strong></td>
                                <td colspan="3"><?php echo $dev_info['nom_personal']; ?></td>
                            </tr>
                            <?php if (!empty($dev_info['obs_devolucion'])) { ?>
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td colspan="3"><?php echo $dev_info['obs_devolucion']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles de la Devolución</strong></h5>
                        <?php if (!empty($dev_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Detalle</th>
                                        <th>Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_det = 1;
                                    foreach ($dev_detalle as $detalle) { 
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_det; ?></td>
                                            <td><?php echo $detalle['nom_producto']; ?></td>
                                            <td><?php echo $detalle['cant_devolucion_detalle']; ?></td>
                                            <td><?php echo $detalle['det_devolucion_detalle']; ?></td>
                                            <td><?php echo $detalle['nom_unidad_medida']; ?></td>
                                        </tr>
                                    <?php 
                                        $contador_det++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay detalles para esta devolución.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <form method="get" action="devoluciones_editar.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $devolucion['id_devolucion']; ?>">
                    <button type="submit" class="btn btn-warning text-white" 
                        <?php echo ($devolucion['est_devolucion'] != 1) ? 'disabled' : ''; ?>>
                        <i class="fa fa-edit"></i> Editar Devolución
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
    }
} 
?>

