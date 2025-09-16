<?php 
//=======================================================================
// VISTA: v_salidas_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Salidas<small></small></h3>
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
                                <h2>Listado de Salidas<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="salidas_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nueva Salida</a>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nº Documento</th>
                                                <th>Tipo Material</th>
                                                <th>Almacén Origen</th>
                                                <th>Ubicación Origen</th>
                                                <th>Almacén Destino</th>
                                                <th>Ubicación Destino</th>
                                                <th>Fecha Requerida</th>
                                                <th>Fecha Registro</th>
                                                <th>Registrado por</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($salidas as $salida) { 
                                            ?>
                                                <tr>
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo $salida['ndoc_salida']; ?></td>
                                                    <td><?php echo $salida['nom_material_tipo']; ?></td>
                                                    <td><?php echo $salida['nom_almacen_origen']; ?></td>
                                                    <td><?php echo $salida['nom_ubicacion_origen']; ?></td>
                                                    <td><?php echo $salida['nom_almacen_destino']; ?></td>
                                                    <td><?php echo $salida['nom_ubicacion_destino']; ?></td>
                                                  <td><?php echo date('d/m/Y', strtotime($salida['fec_req_salida'])); ?></td>
                                                     <td><?php echo date('d/m/Y H:i', strtotime($salida['fec_salida'])); ?></td>
                                                     <td><?php echo $salida['nom_personal'] . ' ' . $salida['ape_personal']; ?></td>
                                                    <td>
                                                        <?php if($salida['est_salida'] == 1) { ?>
                                                            <span class="badge badge-success badge_size">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger badge_size">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDetalleSalida<?php echo $salida['id_salida']; ?>" 
                                                                    title="Ver Detalle">
                                                                <i class="fa fa-eye"></i>
                                                            </button>

                                                            <a href="salidas_editar.php?id=<?php echo $salida['id_salida']; ?>" 
                                                               class="btn btn-warning btn-sm" 
                                                               title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            <a href="salida_pdf.php?id=<?php echo $salida['id_salida']; ?>" 
                                                               class="btn btn-secondary btn-sm" 
                                                               title="Generar PDF"
                                                               target="_blank">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </a>
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

<!-- Modales para ver detalle de cada salida -->
<?php 
foreach($salidas as $salida) { 
    // Obtener detalles de la salida para el modal
    $salida_data = ConsultarSalida($salida['id_salida']);
    $salida_detalle = ConsultarSalidaDetalle($salida['id_salida']);
    
    if (!empty($salida_data)) {
        $salida_info = $salida_data[0];
?>
<div class="modal fade" id="modalDetalleSalida<?php echo $salida['id_salida']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetalleSalidaLabel<?php echo $salida['id_salida']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleSalidaLabel<?php echo $salida['id_salida']; ?>">
                    Detalle de Salida - <?php echo $salida_info['ndoc_salida']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Información General</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Nº Documento:</strong></td>
                                <td><?php echo $salida_info['ndoc_salida']; ?></td>
                                <td><strong>Fecha de Traslado:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($salida_info['fec_salida'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Material:</strong></td>
                                <td><?php echo $salida_info['nom_material_tipo']; ?></td>
                                <td><strong>Fecha Requerida:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($salida_info['fec_req_salida'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Almacén Origen:</strong></td>
                                <td><?php echo $salida_info['nom_almacen_origen'] . ' (' . $salida_info['nom_ubicacion_origen'] . ')'; ?></td>
                                <td><strong>Almacén Destino:</strong></td>
                                <td><?php echo $salida_info['nom_almacen_destino'] . ' (' . $salida_info['nom_ubicacion_destino'] . ')'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Registrado por:</strong></td>
                                <td><?php echo $salida_info['nom_personal'] . ' ' . $salida_info['ape_personal']; ?></td>
                                <td><strong>Personal Encargado:</strong></td>
                                <td><?php echo ($salida_info['nom_encargado'] ? $salida_info['nom_encargado'] . ' ' . $salida_info['ape_encargado'] : 'No especificado'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Personal que Recibe:</strong></td>
                                <td><?php echo ($salida_info['nom_recibe'] ? $salida_info['nom_recibe'] . ' ' . $salida_info['ape_recibe'] : 'No especificado'); ?></td>
                                <td colspan="1"></td>
                            </tr>
                            <?php if (!empty($salida_info['obs_salida'])) { ?>
                            <tr>
                                <td><strong>Observaciones:</strong></td>
                                <td colspan="3"><?php echo $salida_info['obs_salida']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5><strong>Detalles del Traslado</strong></h5>
                        <?php if (!empty($salida_detalle)) { ?>
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Producto/Material</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Stock Origen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador_detalle = 1;
                                    foreach ($salida_detalle as $detalle) { 
                                    ?>
                                        <tr>
                                            <td><?php echo $contador_detalle; ?></td>
                                            <td><?php echo $detalle['prod_salida_detalle']; ?></td>
                                            <td><?php echo $detalle['cant_salida_detalle']; ?></td>
                                            <td><?php echo $detalle['nom_unidad_medida']; ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo $detalle['cantidad_disponible_origen']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php 
                                        $contador_detalle++;
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No hay detalles disponibles para esta salida.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="salidas_editar.php?id=<?php echo $salida['id_salida']; ?>" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Editar Salida
                </a>
            </div>
        </div>
    </div>
</div>
<?php 
    }
} 
?>